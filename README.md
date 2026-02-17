# SPK Perangkingan Siswa Berprestasi

Sistem Pendukung Keputusan (SPK) untuk perangkingan siswa berprestasi menggunakan metode **SMART** (Simple Multi-Attribute Rating Technique) dan **MOORA** (Multi-Objective Optimization on the basis of Ratio Analysis).

Dibangun dengan **Laravel 12**, **MySQL**, dan template **Sneat Bootstrap**.

## Fitur Utama

### 🔐 Multi-Role Authentication

| Role | Akses |
|---|---|
| **Admin** | Kelola seluruh data master, input nilai rapor, agregasi penilaian, perhitungan SMART-MOORA, manajemen pengguna |
| **Wali Kelas** | Input nilai rapor siswa kelasnya, agregasi penilaian, perhitungan, data siswa kelas sendiri |
| **Kepala Sekolah** | Lihat data penilaian, hasil perangkingan, export laporan PDF & Excel (read-only) |

### 📊 Metode Perhitungan

- **SMART** — Normalisasi bobot kriteria, konversi nilai utilitas
- **MOORA** — Ratio System & Reference Point untuk perangkingan
- **Perbandingan** hasil kedua metode dalam satu tampilan

### 📝 Input Data Rapor (6 Kriteria)

| Kode | Kriteria | Sumber Data |
|---|---|---|
| C1 | Pengetahuan | Rata-rata nilai pengetahuan per mata pelajaran |
| C2 | Keterampilan | Rata-rata nilai keterampilan per mata pelajaran |
| C3 | Sikap | Predikat sikap spiritual & sosial |
| C4 | Ekstrakurikuler | Predikat kegiatan ekstrakurikuler |
| C5 | Pelanggaran | Total poin pelanggaran (otomatis dari riwayat) |
| C6 | Absensi | Total hari tidak hadir (sakit + izin + alpa) |

### 📄 Export Laporan

- **PDF** — Hasil perangkingan siswa berprestasi
- **Excel** — Data perangkingan dalam format spreadsheet

---

## Requirement

### Software

| Software | Versi Minimum | Keterangan |
|---|---|---|
| **PHP** | >= 8.2 | Bahasa pemrograman server-side |
| **Composer** | >= 2.x | PHP dependency manager |
| **Node.js** | >= 18.x | Build frontend assets |
| **MySQL** / MariaDB | >= 5.7 / >= 10.3 | Database manager |
| **Web Server** | - | Apache atau Nginx |
| **Git** | - | Version control (opsional) |

### Rekomendasi Development Environment

- **[Laragon](https://laragon.org)** — Local development stack (Apache + MySQL + PHP) — **direkomendasikan**
- **XAMPP** — Alternatif local development stack
- **VS Code** — Code editor

### PHP Extensions

| Extension | Keterangan |
|---|---|
| `mbstring` | String multibyte |
| `xml` | Parsing XML |
| `zip` | Export Excel (maatwebsite/excel) |
| `gd` | Manipulasi gambar / PDF |
| `bcmath` | Perhitungan presisi tinggi |
| `pdo_mysql` | Koneksi MySQL |
| `fileinfo` | Deteksi tipe file |
| `openssl` | Enkripsi |
| `tokenizer` | Laravel requirement |
| `dom` | PDF generation (dompdf) |

> Jika menggunakan **Laragon**, semua extension di atas sudah aktif secara default.

### Package Composer

| Package | Versi | Fungsi |
|---|---|---|
| laravel/framework | ^12.0 | Framework utama |
| laravel/tinker | ^2.10.1 | REPL console |
| barryvdh/laravel-dompdf | ^3.1 | Export laporan PDF |
| maatwebsite/excel | ^3.1 | Export laporan Excel |

---

## Instalasi

### 1. Clone Project

```bash
cd c:\laragon\www
git clone https://github.com/sinyoapril/SPK-SmartMoora.git SPKSmartMoora
cd SPKSmartMoora
```

Atau jika dari file ZIP, ekstrak ke `c:\laragon\www\SPKSmartMoora`.

### 2. Install Dependensi

```bash
composer install
npm install
```

### 3. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spksmartmoora
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Setup Database

Buat database baru di MySQL:

```bash
mysql -u root -e "CREATE DATABASE spksmartmoora"
```

Atau melalui **phpMyAdmin**: http://localhost/phpmyadmin → New → `spksmartmoora` → Create.

Jalankan migrasi dan seeder:

```bash
php artisan migrate --seed
```

> **Catatan:** Perintah `--seed` akan menjalankan `DatabaseSeeder` yang sudah menangani urutan seeder secara otomatis (User → TahunAjaran → Kelas → Siswa → Kriteria → SubKriteria → MataPelajaran → JenisPelanggaran → NilaiPengetahuan → NilaiKeterampilan → NilaiSikap → NilaiEkstrakurikuler → NilaiAbsensi).

### 5. Build Assets & Jalankan

```bash
npm run build
php artisan serve
```

Buka browser → **http://127.0.0.1:8000**

---

## Akun Default

| Role | Email | Password |
|---|---|---|
| Admin | admin@spk.com | password |
| Kepala Sekolah | kepsek@spk.com | password |
| Wali Kelas X | walikelas.x@spk.com | password |
| Wali Kelas XI | walikelas.xi@spk.com | password |
| Wali Kelas XII | walikelas.xii@spk.com | password |

---

## Struktur Role & Menu

### Admin

- **Dashboard** — Statistik keseluruhan
- **Data Master** — Tahun Ajaran, Kelas, Siswa, Mata Pelajaran, Kriteria, Sub Kriteria, Jenis Pelanggaran
- **Input Data Rapor** — Nilai Pengetahuan, Keterampilan, Sikap, Ekstrakurikuler, Absensi
- **Riwayat Pelanggaran** — Catat pelanggaran siswa (auto-hitung C5)
- **Penilaian** — Agregasi data rapor ke C1-C6
- **Perhitungan** — SMART & MOORA, perbandingan hasil
- **Manajemen Pengguna** — Kelola akun user

### Wali Kelas

- **Dashboard** — Statistik kelas sendiri
- **Data Siswa** — CRUD siswa di kelas sendiri
- **Input Data Rapor** — Nilai Pengetahuan, Keterampilan, Sikap, Ekstrakurikuler, Absensi (auto-scoped ke kelas)
- **Riwayat Pelanggaran** — Catat pelanggaran siswa kelas sendiri
- **Penilaian** — Agregasi data rapor ke C1-C6 (kelas sendiri)
- **Perhitungan** — SMART & MOORA (kelas sendiri)

### Kepala Sekolah

- **Dashboard** — Statistik keseluruhan
- **Perangkingan** — Lihat hasil perhitungan & perbandingan
- **Data Referensi** — Siswa, Kelas, Kriteria, Mata Pelajaran, Penilaian (read-only)
- **Export** — Laporan PDF & Excel

---

## Reset Database

```bash
php artisan migrate:fresh --seed
```

---

## Troubleshooting

| Masalah | Solusi |
|---|---|
| `php artisan` tidak ditemukan | Pastikan PHP sudah ter-install dan ada di PATH |
| `composer: command not found` | Install Composer dari https://getcomposer.org |
| 500 Internal Server Error | Jalankan `php artisan key:generate`, cek file `.env` |
| Unknown database | Buat database `spksmartmoora` terlebih dahulu |
| Table not found | Jalankan `php artisan migrate` |
| Class not found | Jalankan `composer dump-autoload` |
| Vite manifest not found | Jalankan `npm run build` |

### Command Berguna

```bash
# Clear cache
php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear

# Lihat daftar route
php artisan route:list
```

---

## Teknologi

- **Backend** — Laravel 12, PHP 8.2+
- **Frontend** — Blade Templates, Sneat Bootstrap 5
- **Database** — MySQL / MariaDB
- **Chart** — ApexCharts
- **Export** — DomPDF, Maatwebsite Excel
- **Alert** — SweetAlert2

---

## Lisensi

Project ini dibuat untuk keperluan akademik.
