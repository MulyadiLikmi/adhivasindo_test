# Pasarin — Toko Online Sederhana

Take-home test Fullstack Developer (Adhivasindo).

Project ini dibuat menggunakan Laravel 10 dalam satu repository (monorepo). Backend menyediakan REST API dengan JWT Authentication, sedangkan frontend menggunakan Blade dan vanilla JavaScript.

---

# Tech Stack

* PHP 8.1+
* Laravel 10
* MySQL
* JWT Authentication (`tymon/jwt-auth`)
* Blade
* Vanilla JavaScript (Fetch API)

---

# Setup Project (Windows + XAMPP)

## 1. Install kebutuhan

Install terlebih dahulu:

* XAMPP
* Composer
* Git (opsional)

Jalankan Apache dan MySQL melalui XAMPP Control Panel.

---

## 2. Aktifkan ekstensi PHP

Buka file:

```text
C:\xampp\php\php.ini
```

Pastikan ekstensi berikut aktif:

```ini
extension=zip
extension=fileinfo
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=curl
```

---

## 3. Install dependency

```bash
composer install
```

Apabila Composer menolak instalasi karena advisory keamanan:

```bash
composer config policy.advisories.block false
composer install
```

---

## 4. Konfigurasi environment

```bash
copy .env.example .env
php artisan key:generate
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
```

---

## 5. Buat database

Buat database baru dengan nama:

```text
pasarin_db
```

Sesuaikan konfigurasi pada file `.env`.

```env
DB_DATABASE=pasarin_db
DB_USERNAME=root
DB_PASSWORD=
```

Jika MySQL menggunakan port selain 3306, ubah juga nilai `DB_PORT` pada `.env`.

---

## 6. Jalankan migration dan seeder

```bash
php artisan migrate --seed
```

Seeder akan membuat data awal berupa:

* akun admin
* akun customer
* kategori
* produk contoh

---

## 7. Jalankan aplikasi

```bash
php artisan serve
```

Buka:

```text
http://localhost:8000
```

### Akun Demo

| Role     | Email                                           | Password    |
| -------- | ----------------------------------------------- | ----------- |
| Admin    | [admin@pasarin.test](mailto:admin@pasarin.test) | password123 |
| Customer | [budi@pasarin.test](mailto:budi@pasarin.test)   | password123 |

---

# Struktur Project

```text
app/
 ├── Http/
 │   ├── Controllers/
 │   └── Middleware/
 ├── Models/

database/
 ├── migrations/
 └── seeders/

routes/
 ├── api.php
 └── web.php

resources/views/
public/
 ├── css/
 └── js/

erd.png
Pasarin.postman_collection.json
```

---

# Fitur

## Backend

* Login dan register menggunakan JWT Authentication.
* Role user terdiri dari **admin** dan **customer**.
* CRUD produk khusus admin.
* Daftar kategori untuk kebutuhan frontend.
* Checkout dengan validasi stok.
* Konfirmasi status order oleh admin.
* Laporan penjualan yang menampilkan ringkasan transaksi, produk terlaris, dan daftar order.

## Frontend

* Landing page dengan pencarian dan filter kategori.
* Halaman login dan register.
* Keranjang belanja menggunakan localStorage.
* Dashboard admin untuk melihat statistik penjualan.
* CRUD produk melalui dashboard admin.
* Tampilan navbar menyesuaikan role pengguna yang sedang login.

---

# Dokumentasi API

File Postman sudah disediakan:

```text
Pasarin.postman_collection.json
```

Import ke Postman, kemudian isi variable:

* `base_url`
* `token`

Token dapat diperoleh dari endpoint login.

---

# ERD

Diagram relasi database tersedia pada file:

```text
erd.png
```

---

# Troubleshooting

**Composer atau PHP tidak dikenali**

Buka kembali Command Prompt setelah instalasi atau pastikan PATH sudah benar.

**Gagal koneksi database**

Pastikan MySQL sudah berjalan dan database `pasarin_db` sudah dibuat.

**Port 8000 digunakan aplikasi lain**

```bash
php artisan serve --port=8001
```

**Data produk tidak muncul**

Pastikan migration dan seeder sudah dijalankan.

```bash
php artisan migrate --seed
```

**Route terbaru belum terbaca**

```bash
php artisan route:clear
```
