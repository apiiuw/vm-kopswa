# Notes Perubahan Fitur & Menu — Sistem Monitoring Aset & Sewa Koperasi Swadharma

Dokumen ini berisi pemetaan 16 kebutuhan bisnis baru terhadap kondisi aplikasi saat ini, mencakup perubahan menu, penambahan fitur, penyesuaian alur, serta kebutuhan struktur database dan API.

---

## 1. Ringkasan Perubahan Menu Sidebar

| Menu Saat Ini | Menu Baru / Perubahan | Ringkasan Perubahan |
| :--- | :--- | :--- |
| **Dashboard** | **Dashboard** *(Diperluas)* | Tambah monitoring termin sewa (1/3/6 bln), status unit (Disewa / Standby / Dijual), ringkasan tagihan & piutang per kontrak, serta alert polis asuransi & faktur pajak. |
| **Klien Proyek Sewa** (`/vendors`) | **Klien & Kontrak Sewa** *(Dipecah 2 Entitas)* | Memisahkan **Master Klien** (perusahaan/NPWP) dan **Daftar Kontrak Sewa** (1 klien dapat memiliki banyak kontrak dengan termin 1/3/6 bulanan). |
| *(Belum ada)* | **BAST & Serah Terima** *(Menu Baru)* | Kelola **BAST Unit Baru**, **BAST Unit Pengganti**, dan **BAST Pengembalian** (mencantumkan tanggal berlaku kontrak & cetak PDF). |
| **Data Armada** (`/vehicles`) | **Data Aset & Unit Sewa** | Mendukung **Mobil, Motor, Mesin Fotocopy, & Dispenser** (>100 unit + import Excel via UI), status **Aktif Disewa / Aktif Standby / Dijual**, serta **Nilai Perolehan, Faktur, Penyusutan & Nilai Buku**. |
| **Servis & Pemeliharaan** (`/maintenance`) | **Pemeliharaan & Unit Pengganti** | Mencatat biaya servis & **biaya kendaraan/unit pengganti** (terhubung ke BAST pengganti & kontrak). |
| **STNK & Pajak** (`/documents`) | **Legalitas Kendaraan (STNK/BPKB)** | Berlaku khusus kategori kendaraan (Mobil & Motor); tidak diwajibkan untuk Fotocopy & Dispenser. |
| **Asuransi & Klaim** (`/insurance`) | **Polis Asuransi & Klaim** | Tambah tab/sub-modul **Master Polis Asuransi** (no. polis, All Risk/TLO, premi, masa berlaku, jatuh tempo) di samping data klaim. |
| **Pendapatan & Kas** (`/financial`) | **Keuangan, Tagihan & Pajak** | Tagihan berdasarkan **Nomor Kontrak** & termin (1/3/6 bln), **Monitoring Penagihan & Piutang**, **Pendapatan Lain-lain** (diskon dealer), **Penyusutan Aset**, serta sub-modul **Faktur Pajak (Coretax)**. |
| **Pengguna Internal** (`/users`) | **Pengguna & Hak Akses** | Akun klien tidak lagi dikunci ke 1 kontrak, melainkan terhubung ke **Klien** dan dapat mengakses beberapa kontrak miliknya di Portal Klien. |
| *(Belum ada)* | **Integrasi API & Coretax** *(Menu Baru di Pengaturan)* | Kelola API Key/Token untuk **API Accounting**, log sinkronisasi jurnal, serta konfigurasi/ekspor **Coretax**. |

---

## 2. Detail Notes Perubahan per Poin Kebutuhan (1 – 16)

### 1. BAST untuk Unit Baru & Unit Pengganti Termasuk Saat Pengembalian
- **Kondisi Saat Ini:** Belum tersedia modul/dokumen BAST (Berita Acara Serah Terima).
- **Perubahan yang Dibutuhkan:**
  - Tambah menu **BAST & Serah Terima** dengan 3 jenis dokumen:
    1. **BAST Unit Baru** (penyerahan awal ke klien)
    2. **BAST Unit Pengganti** (penyerahan unit cadangan saat unit utama servis/rusak)
    3. **BAST Pengembalian** (penarikan unit selesai kontrak / pengembalian unit pengganti)
  - Mendukung generate nomor BAST otomatis, daftar unit yang diserahterimakan, catatan kondisi unit, upload foto/scan BAST bertanda tangan, dan **Cetak PDF BAST**.

### 2. Periode Berlaku Kontrak Dibunyikan dalam BAST
- **Kondisi Saat Ini:** Tanggal mulai dan selesai kontrak (`contract_start_date`, `contract_end_date`) baru tersimpan di data vendor/klien.
- **Perubahan yang Dibutuhkan:**
  - Setiap pembuatan BAST wajib terhubung ke **Kontrak (`contract_id`)**.
  - Pada template & hasil cetak PDF BAST otomatis tercantum klausul masa berlaku kontrak (*"Mulai berlaku sejak tanggal [DD/MM/YYYY] sampai dengan [DD/MM/YYYY]"*).

### 3. Biaya Kendaraan Pengganti Dimasukkan ke Sistem
- **Kondisi Saat Ini:** Sudah ada kategori `motor_pengganti` di *Servis & Pemeliharaan* dan *Pengeluaran Kas*, namun masih berlabel motor dan belum terhubung ke kontrak/BAST pengganti.
- **Perubahan yang Dibutuhkan:**
  - Perluas kategori menjadi **Biaya Kendaraan/Unit Pengganti** (mencakup mobil & motor).
  - Hubungkan pencatatan biaya unit pengganti ke **Nomor Kontrak** dan referensi **BAST Unit Pengganti**, sehingga biaya sewa/operasional unit pengganti langsung masuk ke perhitungan biaya & arus kas kontrak tersebut.

### 4. Update Status untuk Kendaraan yang Telah Dijual
- **Kondisi Saat Ini:** Status kendaraan baru terdiri dari `active`, `maintenance`, `replaced`, `lost`, `claimed`. Belum ada status terjual/dijual.
- **Perubahan yang Dibutuhkan:**
  - Tambahkan status **`dijual`** / **`terjual`** pada unit aset.
  - Sediakan form pencatatan penjualan aset (tanggal terjual, harga jual, pembeli/keterangan) yang otomatis menghitung selisih terhadap **Sisa Nilai Buku** untuk kebutuhan jurnal akuntansi.

### 5. Kategori Mobil Aktif Disewa, Mobil Aktif Standby, Mobil Dijual
- **Kondisi Saat Ini:** Status `active` belum membedakan apakah unit sedang dipakai klien atau sedang *standby* di pool, serta tipe aset baru fokus pada motor.
- **Perubahan yang Dibutuhkan:**
  - Standarisasi status unit di **Data Aset & Unit Sewa**:
    - **Aktif Disewa** (terikat pada kontrak aktif)
    - **Aktif Standby** (unit siap pakai / cadangan di pool Koperasi Swadharma)
    - **Maintenance / Dalam Perbaikan**
    - **Diganti (Replaced)**
    - **Dijual / Terjual**
    - **Hilang / Klaim TLO**
  - Tambahkan filter kategori & status serta kartu ringkasan di **Dashboard** dan **Data Aset**.

### 6. Database per Kontrak (Bukan per User)
- **Kondisi Saat Ini:** Tabel `vendors` menggabungkan entitas Klien dan Kontrak menjadi satu (`1 klien = 1 kontrak`), dan `users.vendor_id` mengikat user klien ke satu vendor saja.
- **Perubahan yang Dibutuhkan:**
  - Pisahkan struktur menjadi:
    - **`clients`**: Master data perusahaan mitra (nama, NPWP, alamat, PIC utama).
    - **`contracts`**: Data kontrak sewa (`client_id`, nomor kontrak, tanggal mulai–akhir, skema termin, nilai kontrak).
  - Relasikan aset, tagihan, pengeluaran, BAST, dan tiket permohonan ke **`contract_id`**.
  - Satu user klien terhubung ke `client_id` dan dapat melihat/memilih beberapa kontrak miliknya.

### 7. Tagihan Berdasarkan Nomor Kontrak
- **Kondisi Saat Ini:** Pembuatan invoice di *Pendapatan & Kas* memilih `vendor_id` dan menginput jumlah unit Honda CRF / Supra secara manual.
- **Perubahan yang Dibutuhkan:**
  - Pada form buat tagihan/invoice, pengguna memilih **Nomor Kontrak**.
  - Sistem otomatis menarik daftar unit aktif dalam kontrak tersebut (mobil, motor, mesin fotocopy, maupun dispenser) beserta tarif sewanya untuk dihitung menjadi draf tagihan.

### 8. Koneksi ke Sistem Accounting via API
- **Kondisi Saat Ini:** Belum ada REST API eksternal (baru tersedia export manual PDF & Excel).
- **Perubahan yang Dibutuhkan:**
  - Bangun endpoint **REST API (`routes/api.php`)** dengan autentikasi Bearer Token / API Key (Laravel Sanctum) untuk integrasi ke sistem Accounting:
    - Endpoint data **Tagihan & Pendapatan Sewa**
    - Endpoint **Pendapatan Lain-lain** (diskon dealer)
    - Endpoint **Pengeluaran Operasional** (servis, pajak, pengganti, premi asuransi)
    - Endpoint **Penyusutan Bulanan & Penjualan Aset**
    - Endpoint **Status Pembayaran / Pelunasan Invoice**
  - Tambah menu **Pengaturan -> Integrasi API & Token** untuk manajemen token dan log akses API.

### 9. Monitoring Sewa dengan Sistem Bulanan, 3 Bulanan, dan 6 Bulanan
- **Kondisi Saat Ini:** Sistem mengasumsikan tarif dan tagihan selalu bulanan (`rental_price_per_month`).
- **Perubahan yang Dibutuhkan:**
  - Tambah field **Siklus Penagihan / Termin Sewa** pada Kontrak & Unit:
    - `bulanan` (1 Bulan)
    - `triwulanan` (3 Bulanan)
    - `semesteran` (6 Bulanan)
  - Sesuaikan jadwal jatuh tempo penagihan dan kalender/notifikasi tagihan di **Dashboard** dan **Keuangan** agar mendukung penagihan per 1, 3, atau 6 bulan.

### 10. Pembuatan Faktur Pajak untuk Koneksi ke Coretax
- **Kondisi Saat Ini:** Invoice baru menghitung PPN 11% secara sederhana tanpa Nomor Seri Faktur Pajak (NSFP), NPWP/NITKU, maupun format ekspor Coretax.
- **Perubahan yang Dibutuhkan:**
  - Tambah sub-modul **Faktur Pajak** pada modul Keuangan / Tagihan:
    - Kelola data **NPWP / NIK / NITKU** klien, Kode Transaksi Pajak, Nomor Seri Faktur Pajak (NSFP), Dasar Pengenaan Pajak (DPP), PPN, dan PPh.
    - Fitur **Generate / Export Format Coretax (XML / CSV standar DJP)** atau koneksi API Coretax/PJAP sesuai kredensial yang tersedia.

### 11. Unit yang Disewakan Mencakup Kendaraan, Mesin Fotocopy, & Dispenser
- **Kondisi Saat Ini:** Modul aset masih khusus sepeda motor (`vehicles` dengan kolom wajib plat nomor, nomor rangka, mesin, STNK, BPKB).
- **Perubahan yang Dibutuhkan:**
  - Generalisasi entitas menjadi **Aset Sewa** dengan jenis/kategori aset:
    - **Mobil**
    - **Motor**
    - **Mesin Fotocopy**
    - **Dispenser**
  - Kolom teknis kendaraan (Plat Nomor, No. Rangka, No. Mesin, STNK, BPKB, Pajak) hanya ditampilkan/diwajibkan untuk **Mobil & Motor**.
  - Untuk **Mesin Fotocopy & Dispenser**, gunakan **Kode Aset / Serial Number (S/N), Tipe/Merek, dan Lokasi Penempatan**.

### 12. Nilai Perolehan, Penyusutan, Nilai Faktur & Sisa Nilai Buku
- **Kondisi Saat Ini:** Belum ada pencatatan nilai faktur pembelian, nilai perolehan, dan perhitungan penyusutan aset.
- **Perubahan yang Dibutuhkan:**
  - Tambah bagian **Valuasi & Penyusutan** pada setiap unit aset:
    - **Nilai Faktur Pembelian (Dealer/Supplier)**
    - **Nilai Perolehan Aset**
    - **Tanggal Perolehan & Umur Ekonomis (Bulan/Tahun)** + Metode Penyusutan (*Straight Line* / Garis Lurus)
    - **Penyusutan per Bulan & Akumulasi Penyusutan** (dihitung otomatis)
    - **Sisa Nilai Buku (*Net Book Value*)** saat ini
  - Sediakan laporan rekap penyusutan & nilai buku di menu Keuangan (serta via API Accounting).

### 13. Diskon Dealer Saat Pengadaan Dibukukan sebagai Pendapatan Lain-lain
- **Kondisi Saat Ini:** Modul pemasukan (`incomes`) hanya mencatat tagihan sewa unit.
- **Perubahan yang Dibutuhkan:**
  - Tambah kategori/tab **Pendapatan Lain-lain** di menu *Keuangan, Tagihan & Pajak*.
  - Saat mencatat pengadaan unit baru, jika terdapat **Diskon dari Dealer**, nominal diskon tersebut otomatis tercatat sebagai entri **Pendapatan Lain-lain** (lengkap dengan referensi dealer, tanggal, dan nomor faktur unit).

### 14. Dukungan >100 Unit Kendaraan (Mobil & Motor) + Aset Lainnya
- **Kondisi Saat Ini:** Sudah memiliki pagination dan filter dasar, namun belum ada fitur *Import Excel* langsung dari halaman web (baru tersedia command CLI khusus format awal).
- **Perubahan yang Dibutuhkan:**
  - Tambah fitur **Import & Update Massal Excel/CSV** langsung di menu *Data Aset & Unit Sewa* agar pengisian & pembaruan >100 unit mobil/motor/fotocopy/dispenser berjalan cepat.
  - Optimalkan pencarian cepat (plat nomor, serial number, kontrak, lokasi) dan ringkasan statistik per jenis aset di Dashboard.

### 15. Monitoring Penagihan (Nagih Berapa & Status Pembayaran)
- **Kondisi Saat Ini:** Tab Tagihan sudah menampilkan nominal bruto/PPN dan status `draft`, `tertagih`, `lunas`, namun belum merangkum piutang per kontrak & per termin.
- **Perubahan yang Dibutuhkan:**
  - Tambah tampilan **Monitoring Penagihan & Piutang per Kontrak** di Dashboard & menu Keuangan:
    - **Total Nilai Kontrak**
    - **Total Sudah Ditagih**
    - **Total Sudah Dibayar (Lunas)**
    - **Sisa Belum Dibayar (Piutang) & Umur Piutang / Status Jatuh Tempo**
    - Status pembayaran per termin (Bulanan / 3 Bulanan / 6 Bulanan).

### 16. Maintain Data Polis Asuransi Kendaraan
- **Kondisi Saat Ini:** Menu *Asuransi & Klaim* (`/insurance`) baru mencatat pengajuan **klaim** asuransi (`insurance_claims`), belum mencatat **master data polis asuransi**.
- **Perubahan yang Dibutuhkan:**
  - Tambah tab/tabel **Data Polis Asuransi (`insurance_policies`)** di menu *Polis Asuransi & Klaim*:
    - Nomor Polis, Perusahaan Asuransi, Jenis Pertanggungan (*All Risk / TLO / Kombinasi*), Nilai Premi, Tanggal Mulai & Tanggal Jatuh Tempo Polis, serta upload file PDF Polis.
    - Hubungkan polis ke satu atau banyak unit kendaraan (*fleet policy*).
    - Tambah **notifikasi/alert polis akan jatuh tempo** di Dashboard & daftar polis agar perpanjangan tidak terlewat.

---

## 3. Ringkasan Perubahan Struktur Database

- **Tabel Baru:**
  - `clients` (Master Perusahaan Klien, NPWP, NITKU, alamat, kontak)
  - `contracts` (Relasi ke `client_id`, nomor kontrak, periode kontrak, termin 1/3/6 bln, status)
  - `handover_documents` / `basts` (BAST unit baru, pengganti, pengembalian + tanggal kontrak)
  - `insurance_policies` (Master data polis asuransi kendaraan & masa berlaku)
  - `tax_invoices` (Faktur pajak & data XML/Coretax terkait invoice)
  - `other_incomes` / perluasan `incomes` (Pendapatan lain-lain termasuk diskon dealer)
  - `personal_access_tokens` / `api_logs` (Autentikasi & log API Accounting)
- **Perubahan Tabel Eksisting:**
  - `vehicles` -> diperluas menjadi aset multi-jenis (`asset_type`: mobil, motor, fotocopy, dispenser), status baru (`aktif_disewa`, `aktif_standby`, `dijual`, dll.), relasi `contract_id`, serta kolom valuasi (`invoice_value`, `acquisition_value`, `acquisition_date`, `useful_life_months`, `depreciation_method`, `sold_date`, `sold_price`).
  - `incomes`, `expenses`, `budgets`, `maintenance_records`, `client_requests` -> relasi utama diarahkan ke `contract_id` + `billing_cycle`.
  - `users` -> akun klien dihubungkan ke `client_id` agar dapat mengakses multi-kontrak.

---

## 4. Rekomendasi Tahapan Pengerjaan (Roadmap)

1. **Fase 1 — Fondasi Data & Aset (Core Database):**
   - Pemisahan `clients` & `contracts` (multi-kontrak per user/klien).
   - Generalisasi `vehicles` menjadi multi-aset (Mobil, Motor, Fotocopy, Dispenser), status (Aktif Disewa, Standby, Dijual), valuasi & penyusutan, serta Import Excel UI (>100 unit).
2. **Fase 2 — Operasional, BAST & Asuransi:**
   - Modul **BAST** (Unit Baru, Pengganti, Pengembalian + periode kontrak + cetak PDF).
   - Integrasi biaya kendaraan pengganti & Master **Polis Asuransi** + alert jatuh tempo.
3. **Fase 3 — Keuangan, Pajak Coretax & API Accounting:**
   - Tagihan per **Nomor Kontrak** & termin (1/3/6 bulanan), Monitoring Penagihan/Piutang, & Pendapatan Lain-lain (diskon dealer).
   - Sub-modul **Faktur Pajak (Coretax)** dan **REST API Accounting**.
