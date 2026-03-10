# Panduan Deployment — OpenLiteSpeed

## Prasyarat Server

- **OS**: Ubuntu 20.04+ / CentOS 8+ / Debian 11+
- **Web Server**: OpenLiteSpeed 1.7+
- **PHP**: LSPHP 8.1+ (dengan ekstensi: `pdo`, `pdo_mysql`, `mbstring`, `json`, `openssl`)
- **Database**: MySQL 8.0+ atau MariaDB 10.5+

---

## Langkah-Langkah Deployment

### 1. Upload Project ke Server

```bash
# Buat direktori project
sudo mkdir -p /var/www/portal-sertifikasi

# Upload project (dari lokal ke server)
scp -r ./* user@server_ip:/var/www/portal-sertifikasi/

# ATAU clone dari Git
cd /var/www
git clone <repository_url> portal-sertifikasi
```

### 2. Konfigurasi Environment

```bash
cd /var/www/portal-sertifikasi

# Copy template environment
cp .env.example .env

# Edit dengan nilai produksi
nano .env
```

Isi `.env` yang harus disesuaikan:
```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_HOST=127.0.0.1
DB_NAME=portal_sertifikasi
DB_USER=portal_user
DB_PASS=strong_password_here
```

### 3. Set Permission

```bash
# Set ownership ke user OLS (biasanya nobody atau www-data)
sudo chown -R nobody:nogroup /var/www/portal-sertifikasi

# Set permission
sudo find /var/www/portal-sertifikasi -type f -exec chmod 644 {} \;
sudo find /var/www/portal-sertifikasi -type d -exec chmod 755 {} \;

# Storage harus writable
sudo chmod -R 775 /var/www/portal-sertifikasi/storage
sudo chown -R nobody:nogroup /var/www/portal-sertifikasi/storage

# Proteksi .env
sudo chmod 600 /var/www/portal-sertifikasi/.env
```

### 4. Import Database

```bash
# Login ke MySQL
mysql -u root -p

# Buat database & user
CREATE DATABASE portal_sertifikasi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'portal_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON portal_sertifikasi.* TO 'portal_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import schema
mysql -u portal_user -p portal_sertifikasi < /var/www/portal-sertifikasi/database/schema.sql
```

### 5. Konfigurasi OpenLiteSpeed

Buka WebAdmin Console di `http://server_ip:7080`:

1. **Virtual Hosts** → Edit (atau buat baru):
   - **Document Root**: `/var/www/portal-sertifikasi/public`
   - **Domain Name**: `yourdomain.com`
   - **Index Files**: `index.php, index.html`

2. **Tab Rewrite**:
   - **Enable Rewrite**: `Yes`
   - **Auto Load from .htaccess**: `Yes`
   - **Rewrite Rules**:
     ```
     RewriteCond %{REQUEST_FILENAME} !-f
     RewriteCond %{REQUEST_FILENAME} !-d
     RewriteRule ^ index.php [L,QSA]
     ```

3. **Tab Script Handler**:
   - **Suffixes**: `php`
   - **Handler Type**: `LiteSpeed SAPI`
   - **Handler Name**: pilih versi PHP yang terinstall (mis. `lsphp81`)

4. **Listeners** → pastikan domain terdaftar di listener port 80/443.

5. Klik **Graceful Restart**.

> Untuk referensi lebih lengkap, lihat file `deploy/openlitespeed-vhost.conf`.

### 6. Setup SSL (HTTPS)

```bash
# Install Certbot untuk OLS
sudo apt install certbot

# Generate sertifikat
sudo certbot certonly --webroot -w /var/www/portal-sertifikasi/public -d yourdomain.com

# Konfigurasi SSL di WebAdmin Console:
# Listeners > SSL > tambahkan listener di port 443
# Set certificate dan key path dari Certbot
```

### 7. Verifikasi

```bash
# Cek OLS status
sudo /usr/local/lsws/bin/lswsctrl status

# Cek akses situs
curl -I https://yourdomain.com

# Pastikan response header mengandung:
# X-Content-Type-Options: nosniff
# X-Frame-Options: SAMEORIGIN
```

---

## Troubleshooting

| Masalah | Solusi |
|---|---|
| 404 pada semua halaman | Pastikan Rewrite sudah On dan `.htaccess` ter-load |
| 500 Internal Server Error | Cek `storage/logs/php-error.log` dan OLS error log |
| Asset CSS/JS tidak tampil | Pastikan Document Root = folder `public/` |
| Permission denied | Cek ownership (lihat langkah 3) |
| PHP not working | Cek LSPHP handler sudah terkonfigurasi |

---

## Struktur Folder di Server

```
/var/www/portal-sertifikasi/
├── config.php           ← konfigurasi app (baca dari .env)
├── .env                 ← kredensial (JANGAN commit ke Git!)
├── .htaccess            ← security rules
├── routes.php           ← definisi routing
├── core/                ← framework core (Router, Auth, DB)
├── controllers/         ← controller classes
├── models/              ← model classes
├── views/               ← template PHP
├── database/            ← SQL schema
├── storage/
│   └── logs/            ← error logs (production)
├── deploy/              ← panduan deployment (file ini)
└── public/              ← DOCUMENT ROOT ← Arahkan OLS ke sini!
    ├── index.php        ← front controller
    ├── .htaccess        ← rewrite + caching
    └── assets/
        ├── css/
        └── js/
```
