# PortalApp Keuangan

Sistem manajemen sertifikasi dan keuangan berbasis web. Mengelola data sertifikat (SBU Konstruksi, SKK, SBU Konsultan, SMAP, SIMPK, Notaris), input data sertifikasi, transaksi keuangan, fee P3SM, dan dashboard ringkasan.

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Backend** | Laravel 12, PHP 8.2+, SQLite |
| **Frontend** | React 18, TypeScript, Redux Toolkit, Tailwind CSS 3 |
| **Build Tool** | Vite 6 |
| **Auth** | Laravel Sanctum (Bearer Token) |
| **Chart** | Recharts |

## Prasyarat

- **PHP** ≥ 8.2 (dengan ekstensi `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`)
- **Composer** ≥ 2.x
- **Node.js** ≥ 18.x
- **npm** ≥ 9.x

> **Tip:** Gunakan [Laragon](https://laragon.org/) untuk setup otomatis PHP + Composer di Windows.

---

## Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/RialinArdiansah/PortalApps.git
cd PortalApps
```

### 2. Setup Backend

```bash
cd backend

# Install dependencies
composer install

# Salin file environment
cp .env.example .env

# Generate application key
php artisan key:generate

# Buat file database SQLite
# Windows:
type nul > database/database.sqlite
# Linux/Mac:
# touch database/database.sqlite

# Jalankan migrasi dan seeder
php artisan migrate:fresh --seed

# Jalankan server backend
php artisan serve
```

Backend berjalan di: `http://localhost:8000`

### 3. Setup Frontend

```bash
# Buka terminal baru
cd frontend

# Install dependencies
npm install

# Jalankan development server
npm run dev
```

Frontend berjalan di: `http://localhost:5173`

> Frontend otomatis mem-proxy request `/api/*` ke `http://localhost:8000` via Vite config.

---

## Akun Default

Setelah menjalankan `php artisan migrate:fresh --seed`, akun berikut tersedia:

| Username | Password | Role |
|----------|----------|------|
| `superadmin` | `superadmin123` | Super admin |
| `admin` | `admin123` | Admin |
| `manager` | `manager123` | Manager |
| `karyawan` | `karyawan123` | Karyawan |
| `marketing1` | `marketing123` | Marketing |
| `mitra1` | `mitra123` | Mitra |

---

## Database

### Konfigurasi Default

Sistem menggunakan **SQLite** secara default. File database berada di:

```
backend/database/database.sqlite
```

Konfigurasi database diatur di file `backend/.env`:

```env
DB_CONNECTION=sqlite
```

> ⚠️ **Penting:** File `database.sqlite` tidak ter-commit ke Git. Setiap install baru WAJIB membuat database baru.

### Membuat Database

```bash
cd backend

# 1. Buat file SQLite kosong (Windows)
type nul > database/database.sqlite

# Untuk Linux/Mac:
# touch database/database.sqlite

# 2. Jalankan semua migrasi (buat tabel)
php artisan migrate

# 3. Isi data awal (WAJIB - tanpa ini fitur tidak berfungsi)
php artisan db:seed

# Atau langkah 1-3 sekaligus:
php artisan migrate:fresh --seed
```

### Skema Tabel

Sistem memiliki **13 tabel** yang dibuat melalui migrasi Laravel:

| No | Tabel | Deskripsi |
|----|-------|-----------|
| 1 | `users` | Data user (superadmin, admin, marketing, dll) |
| 2 | `certificates` | Jenis sertifikat (SBU Konstruksi, SKK, dll) |
| 3 | `sbu_types` | Tipe SBU (konstruksi, konsultan, skk, smap, simpk, notaris) |
| 4 | `asosiasi` | Data asosiasi per tipe SBU (P3SM, GAPEKNAS, INKINDO, dll) |
| 5 | `klasifikasi` | Klasifikasi + sub-klasifikasi per tipe SBU |
| 6 | `biaya_items` | Item biaya: kualifikasi, biaya setor, biaya lainnya |
| 7 | `marketing_names` | Daftar nama marketing |
| 8 | `submissions` | Data input sertifikat (form utama) |
| 9 | `transactions` | Transaksi keuangan (keluar, tabungan, kas) |
| 10 | `fee_p3sm` | Data fee P3SM bulanan |
| 11 | `personal_access_tokens` | Token autentikasi Sanctum |
| 12 | `cache` | Cache Laravel |
| 13 | `jobs` | Antrian job Laravel |

### Relasi Antar Tabel

```
sbu_types
├── asosiasi (sbu_type_id → sbu_types.id)
├── klasifikasi (sbu_type_id → sbu_types.id)
└── biaya_items (sbu_type_id → sbu_types.id, asosiasi_id → asosiasi.id)

users
├── submissions (submitted_by_id → users.id)
└── transactions (submitted_by_id → users.id)
```

### Data Seeder

Seeder (`database/seeders/DatabaseSeeder.php`) mengisi data berikut:

| Data | Jumlah | Contoh |
|------|--------|--------|
| **Users** | 6 akun | superadmin, admin, manager, karyawan, marketing, mitra |
| **Certificates** | 6 jenis | SBU Konstruksi, SKK Konstruksi, SBU Konsultan, SMAP, SIMPK, Notaris |
| **Marketing** | 3 nama | Hidayatullah, Abdul Fatahurahman, Nuzul Arifin |
| **SBU Types** | 6 tipe | konstruksi, konsultan, skk, smap, simpk, notaris |
| **Asosiasi** | 5 asosiasi | P3SM, GAPEKNAS, INKINDO, PERKINDO, LPJK |
| **Klasifikasi** | 6 item | Umum Bangunan Gedung, Ahli Utama, dll |
| **Biaya Items** | 28 item | Kualifikasi, Biaya Setor, Biaya Lainnya per tipe |
| **Submissions** | 7 sampel | Data input sertifikat contoh |
| **Transactions** | 3 sampel | Transaksi contoh |

> **Tanpa seeder, dropdown di form sertifikat akan kosong!** Ini penyebab utama "fitur hilang" saat install di laptop baru.

### Migrasi ke MySQL

Jika ingin menggunakan MySQL (misalnya dari Laragon):

```bash
# 1. Buat database di MySQL
mysql -u root -e "CREATE DATABASE portalapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. Edit file backend/.env
```

Ubah konfigurasi database di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=portalapp
DB_USERNAME=root
DB_PASSWORD=
```

```bash
# 3. Jalankan migrasi + seeder
cd backend
php artisan migrate:fresh --seed
```

### Reset Database

```bash
# Hapus semua tabel dan buat ulang + isi data awal
php artisan migrate:fresh --seed

# Hanya isi ulang data tanpa reset tabel (error jika data sudah ada)
php artisan db:seed
```

### Backup & Restore (SQLite)

```bash
# Backup
copy backend\database\database.sqlite backend\database\backup.sqlite

# Restore
copy backend\database\backup.sqlite backend\database\database.sqlite
```

---

## Struktur Proyek

```
PortalApps/
├── backend/                    # Laravel 12 API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/   # API Controllers
│   │   │   └── Resources/         # API Resources (JSON transform)
│   │   ├── Models/                # Eloquent Models
│   │   └── Services/              # Business Logic (ReferenceDataService)
│   ├── database/
│   │   ├── migrations/            # Schema definitions
│   │   ├── seeders/               # Data seeder (WAJIB dijalankan)
│   │   └── database.sqlite        # SQLite database file
│   └── routes/
│       └── api.php                # API route definitions
│
├── frontend/                   # React + TypeScript SPA
│   ├── src/
│   │   ├── app/                   # Redux store config
│   │   ├── components/            # Reusable components
│   │   │   ├── common/            # Modal, Pagination, etc.
│   │   │   └── certificates/      # SbuAdminModal (Kelola Menu)
│   │   ├── features/              # Redux slices per fitur
│   │   ├── pages/                 # Halaman utama
│   │   ├── types/                 # TypeScript interfaces
│   │   └── utils/                 # Helper functions
│   └── vite.config.ts             # Vite + proxy config
│
└── README.md
```

---

## Daftar API Endpoints

Semua endpoint memerlukan header `Authorization: Bearer {token}` kecuali `/api/login`.

### Auth
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `POST` | `/api/login` | Login (public) |
| `POST` | `/api/logout` | Logout |
| `GET` | `/api/me` | Get current user info |

### Users
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/api/users` | List semua user |
| `POST` | `/api/users` | Tambah user |
| `PUT` | `/api/users/{id}` | Update user |
| `DELETE` | `/api/users/{id}` | Hapus user |

### Certificates & Reference Data
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/api/certificates` | List sertifikat + semua reference data |
| `POST` | `/api/certificates` | Tambah jenis sertifikat |
| `PUT` | `/api/certificates/reference-data` | Update data Kelola Menu |
| `PUT` | `/api/certificates/{id}` | Update jenis sertifikat |
| `DELETE` | `/api/certificates/{id}` | Hapus jenis sertifikat |

### Marketing
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/api/marketing` | List marketing |
| `POST` | `/api/marketing` | Tambah marketing |
| `PUT` | `/api/marketing/{id}` | Update marketing |
| `DELETE` | `/api/marketing/{id}` | Hapus marketing |

### Submissions (Data Input Sertifikat)
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/api/submissions` | List data input |
| `POST` | `/api/submissions` | Tambah data input |
| `PUT` | `/api/submissions/{id}` | Update data input |
| `DELETE` | `/api/submissions/{id}` | Hapus data input |

### Transactions (Keuangan)
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/api/transactions` | List transaksi |
| `POST` | `/api/transactions` | Tambah transaksi |
| `PUT` | `/api/transactions/{id}` | Update transaksi |
| `DELETE` | `/api/transactions/{id}` | Hapus transaksi |

### Fee P3SM
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/api/fee-p3sm` | List fee P3SM |
| `POST` | `/api/fee-p3sm` | Tambah fee |
| `PUT` | `/api/fee-p3sm/{id}` | Update fee |
| `DELETE` | `/api/fee-p3sm/{id}` | Hapus fee |

### Dashboard
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/api/dashboard/summary` | Ringkasan dashboard |
| `GET` | `/api/dashboard/ranking` | Ranking marketing |

---

## Fitur Utama

- **Dashboard** — Ringkasan total data, omzet, keuntungan, grafik bulanan, ranking marketing
- **Manajemen Sertifikat** — Kelola jenis sertifikat dan sub-menu
- **Kelola Menu** — Kelola asosiasi, klasifikasi, sub-klasifikasi, kualifikasi, biaya setor, dan biaya lainnya per tipe sertifikat
- **Input Data Sertifikat** — Form cascading (sertifikat → asosiasi → klasifikasi → sub-klasifikasi → kualifikasi) dengan auto-hitung biaya
- **Transaksi Keuangan** — Catat transaksi keluar, tabungan, dan kas
- **Fee P3SM** — Kelola data fee bulanan
- **Manajemen User** — CRUD user dengan role-based access
- **Marketing** — Kelola daftar nama marketing
- **Dark Mode** — Toggle tema gelap/terang
- **Responsive** — Tampilan mobile-friendly

---

## Perintah Berguna

### Backend

```bash
# Reset database + isi ulang data
php artisan migrate:fresh --seed

# Jalankan server
php artisan serve

# Cek syntax PHP
php artisan route:list

# Buka tinker (REPL)
php artisan tinker
```

### Frontend

```bash
# Development server
npm run dev

# Build production
npm run build

# Type check
npx tsc --noEmit

# Jalankan test
npm run test
```

---

## Troubleshooting

### ❌ Dropdown biaya / asosiasi kosong
**Penyebab:** Database belum di-seed.
```bash
cd backend
php artisan migrate:fresh --seed
```

### ❌ 401 Unauthorized
**Penyebab:** Token expired atau belum login.
- Login ulang di halaman login
- Pastikan backend berjalan di `http://localhost:8000`

### ❌ CORS / Network Error
**Penyebab:** Backend tidak berjalan.
```bash
cd backend
php artisan serve
```
Pastikan frontend berjalan di port `5173` (Vite proxy akan meneruskan ke `8000`).

### ❌ "SQLSTATE: no such table"
**Penyebab:** Migrasi belum dijalankan.
```bash
cd backend
php artisan migrate:fresh --seed
```

### ❌ Frontend blank / error setelah clone
```bash
cd frontend
rm -rf node_modules
npm install
npm run dev
```

---

## Lisensi

MIT License
