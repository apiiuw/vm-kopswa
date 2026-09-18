# Panduan Deployment ke Hostinger (Subdomain: ptsup.co.id)

Dokumen ini berisi panduan teknis langkah demi langkah untuk melakukan deploy aplikasi **Sistem Monitoring Kendaraan PT SUP** ke server **Hostinger** (hPanel) pada subdomain baru (contoh: `fleet.ptsup.co.id` atau `monitoring.ptsup.co.id`).

---

## 1. Pembuatan Subdomain & Direktori Root di Hostinger

1. Masuk ke **hPanel Hostinger** Anda.
2. Buka menu **Domains** -> **Subdomains**.
3. Buat subdomain baru (misal: `monitoring` atau `armada`) untuk domain utama `ptsup.co.id`.
4. **PENTING (Document Root)**:
   - Secara default Hostinger akan mengarahkan root ke `public_html/monitoring`.
   - Ubah Document Root subdomain tersebut menjadi:
     ```
     public_html/monitoring/public
     ```
   - Dengan mengarahkan document root langsung ke folder `public/`, aplikasi Laravel akan berjalan secara aman tanpa mengekspos file konfigurasi `.env`.

---

## 2. Unggah Berkas Proyek

1. **Kompresi File (Zip)**:
   - Kompres seluruh isi folder proyek `vehicle-monitoring` ke dalam file `.zip`.
   - *Catatan*: Folder `vendor/` dan `node_modules/` tidak wajib disertakan jika Anda menjalankan `composer install` via SSH terminal Hostinger, namun jika hosting Anda tidak memiliki akses SSH, sertakan folder `vendor/` dan folder `public/build/` yang sudah selesai di-*compile*.
2. **Unggah via File Manager**:
   - Buka **File Manager** di hPanel Hostinger.
   - Masuk ke direktori `public_html/monitoring/`.
   - Upload file `.zip` lalu lakukan **Extract**.

---

## 3. Konfigurasi Database MySQL di Hostinger

1. Buka menu **Databases** -> **Management** di hPanel Hostinger.
2. Buat database MySQL baru, misalnya:
   - Database Name: `u123456789_ptsup_fleet`
   - Username: `u123456789_admin`
   - Password: `PasswordKuatDB2026!`
3. Salin file `.env.example` menjadi `.env` pada folder root proyek:
   ```ini
   APP_NAME="Monitoring Kendaraan PT SUP"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://monitoring.ptsup.co.id

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=u123456789_ptsup_fleet
   DB_USERNAME=u123456789_admin
   DB_PASSWORD=PasswordKuatDB2026!
   ```

---

## 4. Eksekusi Migrasi & Seeder Database

### Cara A: Melalui Terminal SSH Hostinger (Direkomendasikan)
Buka menu **Advanced** -> **SSH Access** di hPanel, lalu jalankan:
```bash
cd public_html/monitoring
php artisan key:generate --force
php artisan migrate --seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Cara B: Import SQL via phpMyAdmin
Jika tidak menggunakan SSH:
1. Jalankan perintah export database lokal ke SQL:
   ```bash
   php artisan migrate:fresh --seed
   ```
2. Buka **phpMyAdmin** pada database Hostinger Anda, lalu klik tab **Import** file dump SQL.

---

## 5. Akun Pengguna Internal Default

Setelah seeder berjalan, Anda dapat langsung login menggunakan akun internal:
- **URL**: `https://monitoring.ptsup.co.id/login`
- **Email**: `operasional@ptsup.co.id`
- **Password**: `password123`

*(Password dapat diubah kapan saja melalui menu Manajemen Pengguna Internal).*

---

## 6. Import Master Data Armada & Foto Dokumen ke Server

Tersedia perintah artisan khusus yang telah disiapkan: `php artisan armada:import-excel`.

### Opsi A: Jalankan di Terminal SSH Server (Download Otomatis)
Pastikan berkas `Master-Data-Vehicle-Monitoring-SUP.xlsx` sudah ada di root proyek di server, lalu jalankan:
```bash
php artisan armada:import-excel
```
*Perintah ini otomatis:*
- Mengosongkan data dummy armada lama.
- Membaca baris 3 sampai baris terakhir dari sheet `1. Data Armada`.
- Mengunduh otomatis seluruh foto STNK & BPKB dari link Google Drive ke direktori `storage/app/public/vehicles/`.
- Memetakan tarif sewa, lokasi kebun, tanggal pajak 1 th & 5 th, nomor rangka/mesin, dan pengemudi.

### Opsi B: Menggunakan Berkas Foto yang Sudah Terunduh di Lokal (Sangat Cepat)
Karena seluruh 76 berkas STNK dan 76 berkas BPKB sudah berhasil diunduh lengkap di komputer lokal:
1. Anda cukup zip folder lokal: `storage/app/public/vehicles/`
2. Unggah dan ekstrak di server pada path yang sama: `storage/app/public/vehicles/`
3. Jalankan perintah di server tanpa harus mendownload ulang:
   ```bash
   php artisan armada:import-excel --no-download
   ```
4. Pastikan symlink storage aktif:
   ```bash
   php artisan storage:link
   ```
