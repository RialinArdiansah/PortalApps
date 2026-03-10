# PortalApp Keuangan (Native PHP)

Sistem manajemen sertifikasi dan keuangan berbasis web (Native PHP). Mengelola data sertifikat (SBU Konstruksi, SKK, SBU Konsultan, SMAP, SIMPK, Notaris), input data sertifikasi, transaksi keuangan, fee P3SM, dan dashboard ringkasan.

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Backend / Web Server** | Native PHP 8.x, Vanilla PHP |
| **Database** | MySQL |
| **Frontend** | HTML, CSS, JavaScript (Vanilla / Tailwind CSS via CDN) |
| **Server** | Apache / Nginx (Laragon disarankan untuk lokal) |

## Prasyarat

- **PHP** ≥ 8.0 (dengan ekstensi PDO MySQL aktif)
- **Web Server** lokal seperti [Laragon](https://laragon.org/) atau XAMPP.
- **MySQL Database Server**

---

## Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/RialinArdiansah/PortalApps.git
cd "PortalApp Native"
```

Letakkan folder project ini di dalam folder Document Root web server lokal kamu (misalnya `C:\laragon\www` untuk Laragon atau `C:\xampp\htdocs` untuk XAMPP).

### 2. Setup Database

Proyek ini dilengkapi dengan skrip setup otomatis yang akan membuat database, menjalankan skema tabel, dan mengisi data awal (seeder).

Buka terminal/command prompt, arahkan ke direktori project, dan jalankan perintah:

```bash
php database/setup.php
```

Skrip ini secara default akan:
1. Membuat database bernama `portal_sertifikasi`
2. Melakukan import skema dari `database/schema.sql`
3. Melakukan insert data-data awal (seeder) yang krusial

*Catatan: Pastikan service MySQL di Laragon/XAMPP kamu sudah berjalan sebelum menjalankan skrip ini.*

### 3. Konfigurasi (Opsional)

Jika kredensial MySQL kamu berbeda dari bawaan XAMPP/Laragon (`root` dengan password kosong), kamu perlu mengubahnya di file `config.php`:

```php
// config.php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'portal_sertifikasi');
define('DB_USER', 'root'); // Ubah jika perlu
define('DB_PASS', '');     // Ubah jika perlu
```

### 4. Menjalankan Aplikasi

#### Cara 1: Menggunakan Virtual Host (Laragon)
Jika menggunakan Laragon, project otomatis dapat diakses melalui:
`http://portalapp-native.test/public/` atau `http://localhost/PortalApp%20Native/public/`

#### Cara 2: Menggunakan PHP Built-in Server
Jika tidak menggunakan Apache/Nginx, kamu bisa menggunakan built-in server PHP. Jalankan perintah ini di root folder project:
```bash
php -S localhost:8080 -t public
```
Aplikasi bisa diakses di browser pada: `http://localhost:8080`

---

## Akun Default

Setelah menjalankan script `setup.php`, beberapa akun demo ini dapat digunakan untuk login:

| Username | Password | Role |
|----------|----------|------|
| `superadmin` | `superadmin123` | Super admin |
| `admin` | `admin123` | Admin |
| `manager` | `manager123` | Manager |
| `karyawan` | `karyawan123` | Karyawan |
| `marketing1` | `marketing123` | Marketing |
| `mitra1` | `mitra123` | Mitra |

---

## Fitur Utama

- **Dashboard** — Ringkasan total data, omzet, keuntungan, grafik bulanan, ranking marketing
- **Manajemen Sertifikat** — Kelola jenis sertifikat dan sub-menu
- **Kelola Menu** — Kelola asosiasi, klasifikasi, sub-klasifikasi, kualifikasi, biaya setor, dan biaya lainnya per tipe sertifikat
- **Input Data Sertifikat** — Form input dinamis sesuai asosiasi, klasifikasi dan kualifikasi
- **Transaksi Keuangan** — Catat transaksi keluar, tabungan, dan kas
- **Fee P3SM** — Kelola data fee bulanan
- **Manajemen User** — CRUD user dengan role-based access
- **Marketing** — Kelola daftar nama marketing

---

## Struktur Proyek Utama

```
PortalApp Native/
├── config.php               # Konfigurasi Database dan Aplikasi
├── public/                  # Document Root web (Akses utama dari sini)
│   ├── index.php            # Entry Point aplikasi
│   └── assets/              # CSS, JS, Gambar, dll
├── controllers/             # PHP Controllers pengolah logika request
├── models/                  # PHP Models untuk query ke database
├── views/                   # File-file tampilan HTML/PHP
├── core/                    # Library atau helper pendukung core sistem
├── database/                # Skrip Setup DB, Schema, dan Data Seeder
├── routes.php               # Routing URL ke controller
└── README.md                # Dokumentasi proyek
```

---

## Lisensi

MIT License
