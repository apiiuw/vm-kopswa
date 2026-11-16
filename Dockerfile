FROM php:8.4-apache

ENV DEBIAN_FRONTEND=noninteractive \
    APACHE_DOCUMENT_ROOT=/var/www/html/public

WORKDIR /var/www/html

# Network debug tools (mandatory) + PHP build deps + Node.js for Vite
RUN apt-get update && apt-get install -y wget curl net-tools iputils-ping dnsutils netcat-openbsd && rm -rf /var/lib/apt/lists/* \
    && apt-get update && apt-get install -y \
        git unzip zip \
        libpng-dev libonig-dev libxml2-dev libzip-dev \
        ca-certificates gnupg \
    && rm -rf /var/lib/apt/lists/* \
    && mkdir -p /etc/apt/keyrings \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x nodistro main" > /etc/apt/sources.list.d/nodesource.list \
    && apt-get update && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && a2enmod rewrite

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Apache vhost pointing to Laravel public/
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf

# PHP dependencies first (better layer caching)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Full application code (no code changes — copied as-is)
COPY . .

# Remove stale Vite dev-server marker if committed by accident;
# with it present, @vite injects http://localhost:5173 URLs (console errors)
RUN rm -f public/hot

# Frontend assets build (node_modules excluded via .dockerignore)
RUN npm install --ignore-scripts && npm run build && npm cache clean --force

RUN composer dump-autoload --optimize \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
