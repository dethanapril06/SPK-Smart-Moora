# SPK Perangkingan Siswa Berprestasi

Sistem Pendukung Keputusan untuk perangkingan siswa berprestasi menggunakan metode **SMART** (Simple Multi-Attribute Rating Technique) dan **MOORA** (Multi-Objective Optimization on the basis of Ratio Analysis).

Dibangun dengan **Laravel 12**, **MySQL**, dan template **Sneat Bootstrap**.

---

## Requirement

### Software

| Software            | Versi Minimum    | Keterangan                     |
| ------------------- | ---------------- | ------------------------------ |
| **PHP**             | >= 8.2           | Bahasa pemrograman server-side |
| **Composer**        | >= 2.x           | PHP dependency manager         |
| **MySQL** / MariaDB | >= 5.7 / >= 10.3 | Database manager               |
| **Web Server**      | -                | Apache atau Nginx              |
| **Git**             | -                | Version control (opsional)     |

### Rekomendasi Development Environment

- **[Laragon](https://laragon.org)** — Local development stack (Apache + MySQL + PHP) — **direkomendasikan**
- **XAMPP** — Alternatif local development stack
- **VS Code** — Code editor

### PHP Extensions

| Extension   | Keterangan                       |
| ----------- | -------------------------------- |
| `mbstring`  | String multibyte                 |
| `xml`       | Parsing XML                      |
| `zip`       | Export Excel (maatwebsite/excel) |
| `gd`        | Manipulasi gambar / PDF          |
| `bcmath`    | Perhitungan presisi tinggi       |
| `pdo_mysql` | Koneksi MySQL                    |
| `fileinfo`  | Deteksi tipe file                |
| `openssl`   | Enkripsi                         |
| `tokenizer` | Laravel requirement              |
| `dom`       | PDF generation (dompdf)          |

> Jika menggunakan **Laragon**, semua extension di atas sudah aktif secara default.

### Package Composer

| Package                 | Versi   | Fungsi               |
| ----------------------- | ------- | -------------------- |
| laravel/framework       | ^12.0   | Framework utama      |
| laravel/tinker          | ^2.10.1 | REPL console         |
| barryvdh/laravel-dompdf | ^3.1    | Export laporan PDF   |
| maatwebsite/excel       | ^3.1    | Export laporan Excel |

## Instalasi

### 1. Clone / Ekstrak Project

```bash
cd c:\laragon\www
git clone <repository-url> SPKSmartMoora
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
php artisan migrate

php artisan db:seed --class=UserSeeder
php artisan db:seed --class=TahunAjaranSeeder
php artisan db:seed --class=KelasSeeder
php artisan db:seed --class=KriteriaSeeder
php artisan db:seed --class=SubKriteriaSeeder
php artisan db:seed --class=JenisPelanggaranSeeder
php artisan db:seed --class=SiswaSeeder
php artisan db:seed --class=PenilaianSeeder
```

> **Penting:** Jalankan seeder sesuai urutan di atas karena ada ketergantungan foreign key antar tabel.

### 5. Build Assets & Jalankan

```bash
npm run build
php artisan serve
```

Buka browser → **http://127.0.0.1:8000**

---

## Akun Default

| Role           | Email                 | Password |
| -------------- | --------------------- | -------- |
| Admin          | admin@spk.com         | password |
| Kepala Sekolah | kepsek@spk.com        | password |
| Wali Kelas X   | walikelas.x@spk.com   | password |
| Wali Kelas XI  | walikelas.xi@spk.com  | password |
| Wali Kelas XII | walikelas.xii@spk.com | password |

---

## Reset Database

```bash
php artisan migrate:fresh

php artisan db:seed --class=UserSeeder
php artisan db:seed --class=TahunAjaranSeeder
php artisan db:seed --class=KelasSeeder
php artisan db:seed --class=KriteriaSeeder
php artisan db:seed --class=SubKriteriaSeeder
php artisan db:seed --class=JenisPelanggaranSeeder
php artisan db:seed --class=SiswaSeeder
php artisan db:seed --class=PenilaianSeeder
```

---

## Troubleshooting

| Masalah                       | Solusi                                               |
| ----------------------------- | ---------------------------------------------------- |
| `php artisan` tidak ditemukan | Pastikan PHP sudah ter-install dan ada di PATH       |
| `composer: command not found` | Install Composer dari https://getcomposer.org        |
| 500 Internal Server Error     | Jalankan `php artisan key:generate`, cek file `.env` |
| Unknown database              | Buat database `spksmartmoora` terlebih dahulu        |
| Table not found               | Jalankan `php artisan migrate`                       |
| Class not found               | Jalankan `composer dump-autoload`                    |
| Vite manifest not found       | Jalankan `npm run build`                             |

### Command Berguna

```bash
# Clear cache
php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear

# Lihat daftar route
php artisan route:list
```
