-- ═══════════════════════════════════════════════════════════════════
-- Portal Sertifikasi — MySQL Schema
-- Converted from Laravel migrations
-- ═══════════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS portal_sertifikasi
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE portal_sertifikasi;

-- ── users ──
CREATE TABLE IF NOT EXISTS users (
    id CHAR(36) NOT NULL PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Super admin','admin','manager','karyawan','marketing','mitra') DEFAULT 'karyawan',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role (role)
) ENGINE=InnoDB;

-- ── certificates ──
CREATE TABLE IF NOT EXISTS certificates (
    id CHAR(36) NOT NULL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    sub_menus JSON DEFAULT ('[]'),
    sbu_type_slug VARCHAR(255) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── sbu_types ──
CREATE TABLE IF NOT EXISTS sbu_types (
    id CHAR(36) NOT NULL PRIMARY KEY,
    slug VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    menu_config JSON NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── marketing_names ──
CREATE TABLE IF NOT EXISTS marketing_names (
    id CHAR(36) NOT NULL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── asosiasi ──
CREATE TABLE IF NOT EXISTS asosiasi (
    id CHAR(36) NOT NULL PRIMARY KEY,
    sbu_type_id CHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    sub_klasifikasi JSON NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sbu_type_id) REFERENCES sbu_types(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── klasifikasi ──
CREATE TABLE IF NOT EXISTS klasifikasi (
    id CHAR(36) NOT NULL PRIMARY KEY,
    sbu_type_id CHAR(36) NOT NULL,
    asosiasi_id CHAR(36) NULL,
    name VARCHAR(255) NOT NULL,
    sub_klasifikasi JSON DEFAULT ('[]'),
    kualifikasi JSON NULL,
    sub_bidang JSON NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sbu_type_id) REFERENCES sbu_types(id) ON DELETE CASCADE,
    FOREIGN KEY (asosiasi_id) REFERENCES asosiasi(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── biaya_items ──
CREATE TABLE IF NOT EXISTS biaya_items (
    id CHAR(36) NOT NULL PRIMARY KEY,
    sbu_type_id CHAR(36) NOT NULL,
    asosiasi_id CHAR(36) NULL,
    category ENUM('kualifikasi','biaya_setor','biaya_lainnya') NOT NULL,
    name VARCHAR(255) NOT NULL,
    kode VARCHAR(255) NULL,
    biaya BIGINT DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_composite (sbu_type_id, asosiasi_id, category),
    FOREIGN KEY (sbu_type_id) REFERENCES sbu_types(id) ON DELETE CASCADE,
    FOREIGN KEY (asosiasi_id) REFERENCES asosiasi(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── submissions ──
CREATE TABLE IF NOT EXISTS submissions (
    id CHAR(36) NOT NULL PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    marketing_name VARCHAR(255) NOT NULL,
    input_date DATE NOT NULL,
    submitted_by_id CHAR(36) NOT NULL,
    certificate_type VARCHAR(255) NOT NULL,
    sbu_type VARCHAR(50) NOT NULL,
    selected_sub JSON NULL,
    selected_klasifikasi JSON NULL,
    selected_sub_klasifikasi VARCHAR(255) NULL,
    selected_kualifikasi JSON NULL,
    selected_biaya_lainnya JSON NULL,
    biaya_setor_kantor BIGINT DEFAULT 0,
    keuntungan BIGINT DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_submitted_by (submitted_by_id),
    INDEX idx_input_date (input_date),
    INDEX idx_marketing (marketing_name),
    FOREIGN KEY (submitted_by_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── transactions ──
CREATE TABLE IF NOT EXISTS transactions (
    id CHAR(36) NOT NULL PRIMARY KEY,
    transaction_date DATE NOT NULL,
    transaction_name VARCHAR(255) NOT NULL,
    cost BIGINT DEFAULT 0,
    transaction_type ENUM('Keluar','Tabungan','Kas') NOT NULL,
    submitted_by_id CHAR(36) NOT NULL,
    proof VARCHAR(255) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_submitted_by (submitted_by_id),
    INDEX idx_transaction_date (transaction_date),
    INDEX idx_transaction_type (transaction_type),
    FOREIGN KEY (submitted_by_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── fee_p3sm ──
CREATE TABLE IF NOT EXISTS fee_p3sm (
    id CHAR(36) NOT NULL PRIMARY KEY,
    cost BIGINT DEFAULT 0,
    month TINYINT NOT NULL,
    year SMALLINT NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_period (month, year)
) ENGINE=InnoDB;
