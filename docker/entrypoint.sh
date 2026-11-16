#!/bin/bash
set -e

cd /var/www/html

# Shared uploads volume: /app/uploads -> storage/app/public (Laravel public disk)
mkdir -p /app/uploads
mkdir -p storage/app/public
# Mirror shared uploads into the public disk without deleting existing files
cp -rn /app/uploads/. storage/app/public/ 2>/dev/null || true

# Generate .env on first boot from container environment (never touches repo code)
if [ ! -f .env ]; then
  cp .env.example .env 2>/dev/null || touch .env
  {
    echo "APP_NAME=${APP_NAME:-VM-Kopswa}"
    echo "APP_ENV=${APP_ENV:-production}"
    echo "APP_DEBUG=${APP_DEBUG:-false}"
    echo "APP_URL=${APP_URL:-http://localhost}"
    echo "APP_KEY="
    echo "LOG_CHANNEL=stack"
    echo "DB_CONNECTION=mysql"
    echo "DB_HOST=${DB_HOST:-vm-kopswa-db}"
    echo "DB_PORT=${DB_PORT:-3306}"
    echo "DB_DATABASE=${DB_DATABASE:-vm_kopswa}"
    echo "DB_USERNAME=${DB_USERNAME:-vm_kopswa}"
    echo "DB_PASSWORD=${DB_PASSWORD:-vm_kopswa_secret}"
    echo "SESSION_DRIVER=file"
    echo "CACHE_STORE=file"
    echo "QUEUE_CONNECTION=sync"
  } > .env
fi

# App key (only if missing)
if ! grep -q "^APP_KEY=base64:" .env; then
  php artisan key:generate --force
fi

# Wait for MySQL to accept connections
echo "Waiting for database ${DB_HOST:-vm-kopswa-db}:${DB_PORT:-3306}..."
for i in $(seq 1 60); do
  if php -r "try { new PDO('mysql:host='.getenv('DB_HOST').';port='.getenv('DB_PORT'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); echo 'ok'; } catch (Throwable \$e) { exit(1); }" 2>/dev/null | grep -q ok; then
    echo "Database reachable."
    break
  fi
  sleep 2
  if [ "$i" -eq 60 ]; then
    echo "WARNING: database not reachable after 120s, continuing anyway."
  fi
done

# Migrate + seed automatically on startup (ensures data persists across down/up)
php artisan migrate --force || true
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | tail -n 1 | tr -cd '0-9')
if [ -z "$USER_COUNT" ] || [ "$USER_COUNT" = "0" ]; then
  echo "Seeding database (first run)..."
  php artisan db:seed --force || true
else
  echo "Database already seeded (${USER_COUNT} users), skipping seeder."
fi

php artisan storage:link 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

exec apache2-foreground
