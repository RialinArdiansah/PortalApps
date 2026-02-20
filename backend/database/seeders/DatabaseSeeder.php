<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Certificate;
use App\Models\MarketingName;
use App\Models\SbuType;
use App\Models\Asosiasi;
use App\Models\Klasifikasi;
use App\Models\BiayaItem;
use App\Models\Submission;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ========== USERS ==========
        $superAdmin = User::create(['full_name' => 'Super Admin', 'username' => 'superadmin', 'email' => 'superadmin@sultangroup.com', 'password' => 'superadmin123', 'role' => 'Super admin']);
        $admin = User::create(['full_name' => 'Admin', 'username' => 'admin', 'email' => 'admin@sultangroup.com', 'password' => 'admin123', 'role' => 'admin']);
        $manager = User::create(['full_name' => 'Manager', 'username' => 'manager', 'email' => 'manager@sultangroup.com', 'password' => 'manager123', 'role' => 'manager']);
        $karyawan = User::create(['full_name' => 'Karyawan', 'username' => 'karyawan', 'email' => 'karyawan@sultangroup.com', 'password' => 'karyawan123', 'role' => 'karyawan']);
        $marketing = User::create(['full_name' => 'Marketing', 'username' => 'marketing', 'email' => 'marketing@sultangroup.com', 'password' => 'marketing123', 'role' => 'marketing']);
        $mitra = User::create(['full_name' => 'Mitra', 'username' => 'mitra', 'email' => 'mitra@sultangroup.com', 'password' => 'mitra123', 'role' => 'mitra']);

        // ========== CERTIFICATES ==========
        Certificate::create(['name' => 'SBU Konstruksi', 'sub_menus' => []]);
        Certificate::create(['name' => 'SKK Konstruksi', 'sub_menus' => []]);
        Certificate::create(['name' => 'SBU Konsultan', 'sub_menus' => []]);
        Certificate::create(['name' => 'Dokumen SMAP', 'sub_menus' => []]);
        Certificate::create(['name' => 'Akun SIMPK dan Alat', 'sub_menus' => []]);
        Certificate::create(['name' => 'Notaris', 'sub_menus' => []]);

        // ========== MARKETING NAMES ==========
        MarketingName::create(['name' => 'Hidayatullah']);
        MarketingName::create(['name' => 'Abdul Fatahurahman']);
        MarketingName::create(['name' => 'Nuzul Arifin']);

        // ========== SBU TYPES ==========
        $konstruksi = SbuType::create(['slug' => 'konstruksi', 'name' => 'SBU Konstruksi']);
        $konsultan = SbuType::create(['slug' => 'konsultan', 'name' => 'SBU Konsultan']);
        $skk = SbuType::create(['slug' => 'skk', 'name' => 'SKK Konstruksi']);
        $smap = SbuType::create(['slug' => 'smap', 'name' => 'Dokumen SMAP']);
        $simpk = SbuType::create(['slug' => 'simpk', 'name' => 'Akun SIMPK dan Alat']);
        $notaris = SbuType::create(['slug' => 'notaris', 'name' => 'Notaris']);

        // ========== ASOSIASI (Konstruksi) ==========
        $p3sm = Asosiasi::create(['sbu_type_id' => $konstruksi->id, 'name' => 'P3SM']);
        $gapeknas = Asosiasi::create(['sbu_type_id' => $konstruksi->id, 'name' => 'GAPEKNAS']);

        // ========== ASOSIASI (Konsultan) ==========
        $inkindo = Asosiasi::create(['sbu_type_id' => $konsultan->id, 'name' => 'INKINDO']);
        $perkindo = Asosiasi::create(['sbu_type_id' => $konsultan->id, 'name' => 'PERKINDO']);

        // ========== ASOSIASI (SKK) ==========
        $lpjk = Asosiasi::create(['sbu_type_id' => $skk->id, 'name' => 'LPJK']);

        // ========== KLASIFIKASI (Konstruksi) ==========
        Klasifikasi::create(['sbu_type_id' => $konstruksi->id, 'name' => 'UMUM BANGUNAN GEDUNG', 'sub_klasifikasi' => ['Sub 1', 'Sub 2', 'Sub 3']]);
        Klasifikasi::create(['sbu_type_id' => $konstruksi->id, 'name' => 'UMUM BANGUNAN SIPIL', 'sub_klasifikasi' => ['Sub A', 'Sub B']]);

        // ========== KLASIFIKASI (Konsultan) ==========
        Klasifikasi::create(['sbu_type_id' => $konsultan->id, 'name' => 'Layanan Jasa Inspeksi Rekayasa', 'sub_klasifikasi' => ['Sub Konsultan 1', 'Sub Konsultan 2']]);
        Klasifikasi::create(['sbu_type_id' => $konsultan->id, 'name' => 'Layanan Jasa Inspeksi Non-Rekayasa', 'sub_klasifikasi' => ['Sub Konsultan A', 'Sub Konsultan B']]);

        // ========== KLASIFIKASI (SKK) ==========
        Klasifikasi::create(['sbu_type_id' => $skk->id, 'name' => 'Ahli Utama', 'sub_klasifikasi' => ['Sub SKK 1', 'Sub SKK 2'], 'kualifikasi' => ['Kualifikasi SKK A', 'Kualifikasi SKK B'], 'sub_bidang' => ['Sub Bidang 1', 'Sub Bidang 2']]);
        Klasifikasi::create(['sbu_type_id' => $skk->id, 'name' => 'Ahli Madya', 'sub_klasifikasi' => ['Sub SKK A', 'Sub SKK B'], 'kualifikasi' => [], 'sub_bidang' => []]);

        // ========== BIAYA ITEMS — P3SM ==========
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $p3sm->id, 'category' => 'kualifikasi', 'name' => 'Kecil (K) BUJKN', 'biaya' => 315000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $p3sm->id, 'category' => 'kualifikasi', 'name' => 'Menengah (M) BUJKN', 'biaya' => 2257500]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $p3sm->id, 'category' => 'kualifikasi', 'name' => 'Besar (B) BUJKN', 'biaya' => 9450000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $p3sm->id, 'category' => 'biaya_setor', 'name' => 'Kecil (K) BUJKN', 'biaya' => 750000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $p3sm->id, 'category' => 'biaya_setor', 'name' => 'Menengah (M) BUJKN', 'biaya' => 2650000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $p3sm->id, 'category' => 'biaya_setor', 'name' => 'Besar (B) BUJKN', 'biaya' => 12000000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $p3sm->id, 'category' => 'biaya_lainnya', 'name' => 'Biaya Materai P3SM', 'biaya' => 10000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $p3sm->id, 'category' => 'biaya_lainnya', 'name' => 'Biaya Administrasi P3SM', 'biaya' => 50000]);

        // ========== BIAYA ITEMS — GAPEKNAS ==========
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $gapeknas->id, 'category' => 'kualifikasi', 'name' => 'Kualifikasi GAPEKNAS 1', 'biaya' => 500000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $gapeknas->id, 'category' => 'kualifikasi', 'name' => 'Kualifikasi GAPEKNAS 2', 'biaya' => 3000000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $gapeknas->id, 'category' => 'biaya_setor', 'name' => 'Kualifikasi GAPEKNAS 1', 'biaya' => 1000000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $gapeknas->id, 'category' => 'biaya_setor', 'name' => 'Kualifikasi GAPEKNAS 2', 'biaya' => 4000000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $gapeknas->id, 'category' => 'biaya_lainnya', 'name' => 'Biaya Administrasi GAPEKNAS', 'biaya' => 75000]);

        // ========== BIAYA ITEMS — Konsultan ==========
        BiayaItem::create(['sbu_type_id' => $konsultan->id, 'category' => 'kualifikasi', 'name' => 'Kualifikasi Konsultan Kecil', 'biaya' => 400000]);
        BiayaItem::create(['sbu_type_id' => $konsultan->id, 'category' => 'kualifikasi', 'name' => 'Kualifikasi Konsultan Menengah', 'biaya' => 2500000]);
        BiayaItem::create(['sbu_type_id' => $konsultan->id, 'category' => 'biaya_setor', 'name' => 'Kualifikasi Konsultan Kecil', 'biaya' => 800000]);
        BiayaItem::create(['sbu_type_id' => $konsultan->id, 'category' => 'biaya_setor', 'name' => 'Kualifikasi Konsultan Menengah', 'biaya' => 3000000]);
        BiayaItem::create(['sbu_type_id' => $konsultan->id, 'category' => 'biaya_lainnya', 'name' => 'Biaya Admin Konsultan', 'biaya' => 60000]);

        // ========== BIAYA ITEMS — SKK ==========
        BiayaItem::create(['sbu_type_id' => $skk->id, 'category' => 'kualifikasi', 'name' => 'Jenjang SKK 1', 'biaya' => 600000]);
        BiayaItem::create(['sbu_type_id' => $skk->id, 'category' => 'kualifikasi', 'name' => 'Jenjang SKK 2', 'biaya' => 3500000]);
        BiayaItem::create(['sbu_type_id' => $skk->id, 'category' => 'biaya_setor', 'name' => 'Jenjang SKK 1', 'biaya' => 1200000]);
        BiayaItem::create(['sbu_type_id' => $skk->id, 'category' => 'biaya_setor', 'name' => 'Jenjang SKK 2', 'biaya' => 5000000]);
        BiayaItem::create(['sbu_type_id' => $skk->id, 'category' => 'biaya_lainnya', 'name' => 'Biaya Admin SKK', 'biaya' => 80000]);

        // ========== BIAYA ITEMS — SMAP / SIMPK / Notaris ==========
        BiayaItem::create(['sbu_type_id' => $smap->id, 'category' => 'biaya_setor', 'name' => 'Dokumen SMAP', 'biaya' => 0]);
        BiayaItem::create(['sbu_type_id' => $simpk->id, 'category' => 'biaya_setor', 'name' => 'Akun SIMPK dan Alat', 'biaya' => 0]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'biaya_setor', 'name' => 'Notaris', 'biaya' => 0]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'kualifikasi', 'name' => 'Kualifikasi Notaris 1', 'biaya' => 500000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'kualifikasi', 'name' => 'Kualifikasi Notaris 2', 'biaya' => 1000000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'biaya_lainnya', 'name' => 'Biaya Admin Notaris', 'biaya' => 100000]);

        // ========== SAMPLE SUBMISSIONS ==========
        Submission::create(['company_name' => 'PT. Maju Bersama', 'marketing_name' => 'Hidayatullah', 'input_date' => '2025-08-01', 'submitted_by_id' => $admin->id, 'certificate_type' => 'SBU Konstruksi', 'sbu_type' => 'konstruksi', 'selected_sub' => ['id' => $p3sm->id, 'name' => 'P3SM'], 'selected_klasifikasi' => ['id' => 'konstruksi-umum-gedung', 'name' => 'UMUM BANGUNAN GEDUNG', 'subKlasifikasi' => ['Sub 1', 'Sub 2', 'Sub 3']], 'selected_sub_klasifikasi' => 'Sub 1', 'selected_kualifikasi' => ['id' => 'p3sm-kual-1', 'name' => 'Kecil (K) BUJKN', 'biaya' => 315000], 'selected_biaya_lainnya' => null, 'biaya_setor_kantor' => 750000, 'keuntungan' => 435000]);
        Submission::create(['company_name' => 'CV. Jaya Abadi', 'marketing_name' => 'Nuzul Arifin', 'input_date' => '2025-08-03', 'submitted_by_id' => $admin->id, 'certificate_type' => 'SBU Konstruksi', 'sbu_type' => 'konstruksi', 'selected_sub' => ['id' => $gapeknas->id, 'name' => 'GAPEKNAS'], 'selected_klasifikasi' => ['id' => 'konstruksi-umum-sipil', 'name' => 'UMUM BANGUNAN SIPIL', 'subKlasifikasi' => ['Sub A', 'Sub B']], 'selected_sub_klasifikasi' => 'Sub A', 'selected_kualifikasi' => ['id' => 'gpk-kual-1', 'name' => 'Kualifikasi GAPEKNAS 1', 'biaya' => 500000], 'selected_biaya_lainnya' => null, 'biaya_setor_kantor' => 1000000, 'keuntungan' => 500000]);
        Submission::create(['company_name' => 'PT. Manager Corp', 'marketing_name' => 'Abdul Fatahurahman', 'input_date' => '2025-08-05', 'submitted_by_id' => $manager->id, 'certificate_type' => 'SBU Konstruksi', 'sbu_type' => 'konstruksi', 'selected_sub' => ['id' => $p3sm->id, 'name' => 'P3SM'], 'selected_klasifikasi' => ['id' => 'konstruksi-umum-gedung', 'name' => 'UMUM BANGUNAN GEDUNG', 'subKlasifikasi' => ['Sub 1', 'Sub 2', 'Sub 3']], 'selected_sub_klasifikasi' => 'Sub 2', 'selected_kualifikasi' => ['id' => 'p3sm-kual-2', 'name' => 'Menengah (M) BUJKN', 'biaya' => 2257500], 'selected_biaya_lainnya' => null, 'biaya_setor_kantor' => 3000000, 'keuntungan' => 742500]);
        Submission::create(['company_name' => 'CV. Karyawan Sejahtera', 'marketing_name' => 'Nuzul Arifin', 'input_date' => '2025-08-06', 'submitted_by_id' => $karyawan->id, 'certificate_type' => 'Dokumen SMAP', 'sbu_type' => 'smap', 'selected_kualifikasi' => ['id' => 'smap-kual-1', 'name' => 'SMAP Standar', 'biaya' => 200000], 'biaya_setor_kantor' => 400000, 'keuntungan' => 200000]);
        Submission::create(['company_name' => 'PT. Konsultan Hebat', 'marketing_name' => 'Hidayatullah', 'input_date' => '2025-08-08', 'submitted_by_id' => $admin->id, 'certificate_type' => 'SBU Konsultan', 'sbu_type' => 'konsultan', 'selected_sub' => ['id' => $inkindo->id, 'name' => 'INKINDO'], 'selected_klasifikasi' => ['id' => 'konsultan-rekayasa', 'name' => 'Layanan Jasa Inspeksi Rekayasa', 'subKlasifikasi' => ['Sub Konsultan 1', 'Sub Konsultan 2']], 'selected_sub_klasifikasi' => 'Sub Konsultan 1', 'selected_kualifikasi' => ['id' => 'konsultan-kual-1', 'name' => 'Kualifikasi Konsultan Kecil', 'biaya' => 400000], 'biaya_setor_kantor' => 800000, 'keuntungan' => 400000]);
        Submission::create(['company_name' => 'PT. Marketing Solutions', 'marketing_name' => 'Hidayatullah', 'input_date' => '2025-08-09', 'submitted_by_id' => $marketing->id, 'certificate_type' => 'Akun SIMPK dan Alat', 'sbu_type' => 'simpk', 'selected_kualifikasi' => ['id' => 'simpk-kual-1', 'name' => 'SIMPK Basic', 'biaya' => 150000], 'biaya_setor_kantor' => 300000, 'keuntungan' => 150000]);
        Submission::create(['company_name' => 'CV. Mitra Terpercaya', 'marketing_name' => 'Abdul Fatahurahman', 'input_date' => '2025-08-10', 'submitted_by_id' => $mitra->id, 'certificate_type' => 'Notaris', 'sbu_type' => 'notaris', 'selected_kualifikasi' => ['id' => 'notaris-kual-1', 'name' => 'Kualifikasi Notaris 1', 'biaya' => 500000], 'biaya_setor_kantor' => 750000, 'keuntungan' => 250000]);

        // ========== SAMPLE TRANSACTIONS ==========
        Transaction::create(['transaction_date' => '2025-08-05', 'transaction_name' => 'kas operasional', 'cost' => 25000, 'transaction_type' => 'Keluar', 'submitted_by_id' => $admin->id, 'proof' => 'bukti-kas.pdf']);
        Transaction::create(['transaction_date' => '2025-08-01', 'transaction_name' => 'tabungan ruko', 'cost' => 5000000, 'transaction_type' => 'Tabungan', 'submitted_by_id' => $admin->id]);
        Transaction::create(['transaction_date' => '2025-08-02', 'transaction_name' => 'Beli ATK', 'cost' => 150000, 'transaction_type' => 'Keluar', 'submitted_by_id' => $admin->id]);
    }
}
