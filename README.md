# VM-Kopswa — Sistem Monitoring Kendaraan

<!-- Updated: 2026-11-16 15:46 WIB -->

Aplikasi monitoring armada kendaraan sewa operasional kebun milik **Koperasi Swadharma**
untuk klien seperti PTPN I Regional 7: data armada, STNK/BPKB & pajak,
service & maintenance, asuransi/klaim, anggaran (plafond), kas masuk/keluar, portal
klien (layanan & tiket), serta export laporan PDF & Excel.

> Codebase aplikasi dijalankan apa adanya (tanpa perubahan code) — repo ini hanya
> menambahkan file containerisasi (`Dockerfile`, `docker-compose.yml`, `helmchart/`).

## Tech Stack

| Layer    | Teknologi |
|----------|-----------|
| Backend  | Laravel 13 (PHP 8.3) |
| Database | MySQL 8.0 |
| Frontend | Blade + Tailwind CSS 4 (Vite), Chart.js, SweetAlert2, Lucide |
| Web server (container) | Apache 2 (mod_rewrite) |
| Export | barryvdh/laravel-dompdf, phpoffice/phpspreadsheet |

## Struktur Modul

- **Dashboard** (`/`) — ringkasan armada & keuangan
- **Vendors** (`/vendors`) — multi-vendor/klien + lokasi kebun
- **Vehicles** (`/vehicles`) — data armada + file STNK/BPKB
- **Maintenance** (`/maintenance`) — service, ban, accu, sparepart
- **Documents** (`/documents`) — STNK, pajak 5 tahunan, blokir Polda
- **Insurance** (`/insurance`) — asuransi & klaim
- **Financial** (`/financial`) — anggaran, pemasukan, pengeluaran
- **Exports** (`/exports/*`) — laporan PDF & Excel
- **Client portal** (`/client/*`) — dashboard, tiket layanan realtime, daftar kendaraan

## Menjalankan dengan Docker Compose

Prasyarat: Docker + Docker Compose, dan network eksternal `autodev-net`:

```sh
docker network create autodev-net   # sekali saja, jika belum ada
docker compose -p vm-kopswa up -d --build
```

Aplikasi tersedia di container **`vm-kopswa-app`** (port 80, via network `autodev-net`).
Database MySQL berjalan di container `vm-kopswa-db` dengan volume persisten
`vm-kopswa_db_data`. Uploads dipetakan ke
`/project/autodev-py/data_sharing/vm-kopswa:/app/uploads`.

Saat pertama start, entrypoint otomatis menjalankan `migrate --force` dan
`db:seed` (hanya jika tabel users masih kosong), sehingga data tetap ada
setelah `down`/`up`.

```sh
docker compose -p vm-kopswa logs -f   # lihat log
docker compose -p vm-kopswa down      # hentikan
```

## Akun Default (dari `database/seeders/DatabaseSeeder.php`)

| Email | Password | Peran |
|-------|----------|-------|
| `info@ptsup.co.id` | `password123` | Admin internal Koperasi Swadharma |
| `pic.reg7@ptpn.id` | `password123` | Klien (PTPN I Regional 7) |

## Deploy ke Kubernetes (Helm)

```sh
helm install vm-kopswa ./helmchart
```

`helmchart/values.yaml` mengatur `global.ingress.enabled`,
`global.ingress.host`, `global.ingress.tlsSecret`, serta
`services.vm-kopswa-app` / `services.vm-kopswa-db` (image + port).

## Variabel Environment (service `vm-kopswa-app`)

`APP_NAME`, `APP_ENV`, `APP_DEBUG`, `APP_URL`, `DB_HOST` (default `vm-kopswa-db`),
`DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
