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
        // =========================================================
        // USERS
        // =========================================================
        $superAdmin = User::create(['full_name' => 'Super Admin', 'username' => 'superadmin', 'email' => 'superadmin@sultangroup.com', 'password' => 'superadmin123', 'role' => 'Super admin']);
        $admin = User::create(['full_name' => 'Admin', 'username' => 'admin', 'email' => 'admin@sultangroup.com', 'password' => 'admin123', 'role' => 'admin']);
        $manager = User::create(['full_name' => 'Manager', 'username' => 'manager', 'email' => 'manager@sultangroup.com', 'password' => 'manager123', 'role' => 'manager']);
        $karyawan = User::create(['full_name' => 'Karyawan', 'username' => 'karyawan', 'email' => 'karyawan@sultangroup.com', 'password' => 'karyawan123', 'role' => 'karyawan']);
        $marketing = User::create(['full_name' => 'Marketing', 'username' => 'marketing', 'email' => 'marketing@sultangroup.com', 'password' => 'marketing123', 'role' => 'marketing']);
        $mitra = User::create(['full_name' => 'Mitra', 'username' => 'mitra', 'email' => 'mitra@sultangroup.com', 'password' => 'mitra123', 'role' => 'mitra']);

        // =========================================================
        // CERTIFICATES
        // =========================================================
        Certificate::create(['name' => 'SBU Konstruksi', 'sub_menus' => [], 'sbu_type_slug' => 'konstruksi']);
        Certificate::create(['name' => 'SKK Konstruksi', 'sub_menus' => [], 'sbu_type_slug' => 'skk']);
        Certificate::create(['name' => 'SBU Konsultan', 'sub_menus' => [], 'sbu_type_slug' => 'konsultan']);
        Certificate::create(['name' => 'Dokumen SMAP', 'sub_menus' => [], 'sbu_type_slug' => 'smap']);
        Certificate::create(['name' => 'Akun SIMPK dan Alat', 'sub_menus' => [], 'sbu_type_slug' => 'simpk']);
        Certificate::create(['name' => 'Notaris', 'sub_menus' => [], 'sbu_type_slug' => 'notaris']);
        Certificate::create(['name' => 'Sewa SKK Tenaga Ahli', 'sub_menus' => [], 'sbu_type_slug' => 'sewa-skk']);
        Certificate::create(['name' => 'SMK3 Perusahaan (Kemenaker)', 'sub_menus' => [], 'sbu_type_slug' => 'smk3']);
        Certificate::create(['name' => 'AK3 Umum Kemenaker', 'sub_menus' => [], 'sbu_type_slug' => 'ak3']);
        Certificate::create(['name' => 'ISO Lokal', 'sub_menus' => [], 'sbu_type_slug' => 'iso-lokal']);
        Certificate::create(['name' => 'ISO UAF (Americo)', 'sub_menus' => [], 'sbu_type_slug' => 'iso-uaf']);
        Certificate::create(['name' => 'ISO KAN (P3SM)', 'sub_menus' => [], 'sbu_type_slug' => 'iso-kan']);
        Certificate::create(['name' => 'KAP Non Barcode (Alam)', 'sub_menus' => [], 'sbu_type_slug' => 'kap-nonbarcode']);
        Certificate::create(['name' => 'KAP Barcode Tidak Audit', 'sub_menus' => [], 'sbu_type_slug' => 'kap-barcode']);
        Certificate::create(['name' => 'KAP Barcode P3SM (Sistem Audit)', 'sub_menus' => [], 'sbu_type_slug' => 'kap-barcode-p3sm']);

        // =========================================================
        // MARKETING NAMES
        // =========================================================
        MarketingName::create(['name' => 'Hidayatullah']);
        MarketingName::create(['name' => 'Abdul Fatahurahman']);
        MarketingName::create(['name' => 'Nuzul Arifin']);

        // =========================================================
        // SBU TYPES
        // =========================================================
        $konstruksi = SbuType::create([
            'slug' => 'konstruksi',
            'name' => 'SBU Konstruksi',
            'menu_config' => ['asosiasi' => true, 'klasifikasi' => true, 'kualifikasi' => true, 'kualifikasiLabel' => 'Kualifikasi', 'biayaSetor' => true, 'biayaLainnya' => true],
        ]);
        $konsultan = SbuType::create([
            'slug' => 'konsultan',
            'name' => 'SBU Konsultan',
            'menu_config' => ['asosiasi' => false, 'klasifikasi' => true, 'kualifikasi' => true, 'kualifikasiLabel' => 'Kualifikasi', 'biayaSetor' => true, 'biayaLainnya' => true],
        ]);
        $skk = SbuType::create([
            'slug' => 'skk',
            'name' => 'SKK Konstruksi',
            'menu_config' => ['asosiasi' => true, 'klasifikasi' => true, 'kualifikasi' => true, 'kualifikasiLabel' => 'Jenjang', 'biayaSetor' => true, 'biayaLainnya' => true],
        ]);
        $smap = SbuType::create([
            'slug' => 'smap',
            'name' => 'Dokumen SMAP',
            'menu_config' => ['asosiasi' => false, 'klasifikasi' => false, 'kualifikasi' => false, 'kualifikasiLabel' => 'Kualifikasi', 'biayaSetor' => true, 'biayaLainnya' => false],
        ]);
        $simpk = SbuType::create([
            'slug' => 'simpk',
            'name' => 'Akun SIMPK dan Alat',
            'menu_config' => ['asosiasi' => false, 'klasifikasi' => false, 'kualifikasi' => false, 'kualifikasiLabel' => 'Kualifikasi', 'biayaSetor' => true, 'biayaLainnya' => false],
        ]);
        $notaris = SbuType::create([
            'slug' => 'notaris',
            'name' => 'Notaris',
            'menu_config' => ['asosiasi' => false, 'klasifikasi' => false, 'kualifikasi' => true, 'kualifikasiLabel' => 'Akta', 'biayaSetor' => true, 'biayaLainnya' => false, 'biayaSetorLabel' => 'Stor Ke Randy', 'kodeField' => ['enabled' => true, 'label' => 'Paket']],
        ]);

        // ── 9 New Certificate Types ──
        $sewaSkk = SbuType::create([
            'slug' => 'sewa-skk',
            'name' => 'Sewa SKK Tenaga Ahli',
            'menu_config' => ['asosiasi' => false, 'klasifikasi' => false, 'kualifikasi' => true, 'kualifikasiLabel' => 'Jenjang', 'biayaSetor' => true, 'biayaLainnya' => false, 'biayaSetorLabel' => 'Stor Ke RIA'],
        ]);
        $smk3 = SbuType::create([
            'slug' => 'smk3',
            'name' => 'SMK3 Perusahaan (Kemenaker)',
            'menu_config' => ['asosiasi' => false, 'klasifikasi' => false, 'kualifikasi' => true, 'kualifikasiLabel' => 'Sertifikat', 'biayaSetor' => true, 'biayaLainnya' => false, 'biayaSetorLabel' => 'Stor Ke P3SM', 'kodeField' => ['enabled' => true, 'label' => 'Keterangan']],
        ]);
        $ak3 = SbuType::create([
            'slug' => 'ak3',
            'name' => 'AK3 Umum Kemenaker',
            'menu_config' => ['asosiasi' => false, 'klasifikasi' => false, 'kualifikasi' => true, 'kualifikasiLabel' => 'Sertifikat', 'biayaSetor' => true, 'biayaLainnya' => false, 'biayaSetorLabel' => 'Stor Ke P3SM'],
        ]);
        $isoLokal = SbuType::create([
            'slug' => 'iso-lokal',
            'name' => 'ISO Lokal',
            'menu_config' => ['asosiasi' => false, 'klasifikasi' => false, 'kualifikasi' => true, 'kualifikasiLabel' => 'Deskripsi ISO', 'biayaSetor' => true, 'biayaLainnya' => false, 'biayaSetorLabel' => 'Stor Ke SmartSolution', 'kodeField' => ['enabled' => true, 'label' => 'Kode']],
        ]);
        $isoUaf = SbuType::create([
            'slug' => 'iso-uaf',
            'name' => 'ISO UAF (Americo)',
            'menu_config' => ['asosiasi' => false, 'klasifikasi' => false, 'kualifikasi' => true, 'kualifikasiLabel' => 'Deskripsi ISO', 'biayaSetor' => true, 'biayaLainnya' => false, 'biayaSetorLabel' => 'Stor Ke Americo', 'kodeField' => ['enabled' => true, 'label' => 'Kode']],
        ]);
        $isoKan = SbuType::create([
            'slug' => 'iso-kan',
            'name' => 'ISO KAN (P3SM)',
            'menu_config' => ['asosiasi' => false, 'klasifikasi' => false, 'kualifikasi' => true, 'kualifikasiLabel' => 'Deskripsi ISO', 'biayaSetor' => true, 'biayaLainnya' => false, 'biayaSetorLabel' => 'Stor Ke P3SM', 'kodeField' => ['enabled' => true, 'label' => 'Kode']],
        ]);
        $kapNonBarcode = SbuType::create([
            'slug' => 'kap-nonbarcode',
            'name' => 'KAP Non Barcode (Alam)',
            'menu_config' => ['asosiasi' => false, 'klasifikasi' => false, 'kualifikasi' => true, 'kualifikasiLabel' => 'Ekuitas KAP', 'biayaSetor' => true, 'biayaLainnya' => false, 'biayaSetorLabel' => 'Stor Ke Alam'],
        ]);
        $kapBarcode = SbuType::create([
            'slug' => 'kap-barcode',
            'name' => 'KAP Barcode Tidak Audit',
            'menu_config' => ['asosiasi' => false, 'klasifikasi' => false, 'kualifikasi' => true, 'kualifikasiLabel' => 'Ekuitas KAP', 'biayaSetor' => true, 'biayaLainnya' => false, 'biayaSetorLabel' => 'Stor'],
        ]);
        $kapBarcodeP3sm = SbuType::create([
            'slug' => 'kap-barcode-p3sm',
            'name' => 'KAP Barcode P3SM (Sistem Audit)',
            'menu_config' => ['asosiasi' => false, 'klasifikasi' => false, 'kualifikasi' => true, 'kualifikasiLabel' => 'Ekuitas KAP', 'biayaSetor' => true, 'biayaLainnya' => false, 'biayaSetorLabel' => 'Stor Ke P3SM'],
        ]);

        // =========================================================
        // ASOSIASI
        // =========================================================
        // SBU Konstruksi
        $p3sm = Asosiasi::create(['sbu_type_id' => $konstruksi->id, 'name' => 'P3SM']);
        $gapeknas = Asosiasi::create(['sbu_type_id' => $konstruksi->id, 'name' => 'GAPEKNAS']);

        // SKK Konstruksi
        $lpjkSkk = Asosiasi::create(['sbu_type_id' => $skk->id, 'name' => 'LPJK']);


        // =========================================================
        // KLASIFIKASI — SBU Konstruksi (KBLI 2020)
        // =========================================================
        $subBangunanGedung = [
            'BG001 – Konstruksi Gedung Hunian',
            'BG002 – Konstruksi Gedung Perkantoran',
            'BG003 – Konstruksi Gedung Industri dan Gudang',
            'BG004 – Konstruksi Gedung Perbelanjaan',
            'BG005 – Konstruksi Gedung Kesehatan',
            'BG006 – Konstruksi Gedung Pendidikan',
            'BG007 – Konstruksi Gedung Penginapan',
            'BG008 – Konstruksi Gedung Lainnya',
            'BG009 – Konstruksi Gedung Tempat Hiburan, Kesenian, dan Olahraga',
        ];
        $subBangunanSipil = [
            'BS001 – Konstruksi Jalan dan Jembatan',
            'BS002 – Konstruksi Jalan Rel',
            'BS003 – Konstruksi Irigasi, Saluran Air, dan Drainase',
            'BS004 – Konstruksi Jaringan Transmisi dan Distribusi',
            'BS005 – Konstruksi Bangunan Elektrikal',
            'BS006 – Konstruksi Bangunan Sipil Lainnya',
            'BS007 – Konstruksi Terowongan',
            'BS008 – Konstruksi Pelabuhan dan Bandara',
        ];
        $subInstalasi = [
            'IN001 – Instalasi Sistem Mekanikal dan Elektrikal Bangunan Gedung',
            'IN002 – Instalasi Sistem Tata Udara / HVAC',
            'IN003 – Instalasi Sistem Pemadam Kebakaran',
            'IN004 – Instalasi Lift dan Eskalator',
            'IN005 – Instalasi Sistem Kelistrikan Gedung',
            'IN006 – Instalasi Sistem Plumbing dan Sanitasi',
            'IN007 – Instalasi Jaringan Telekomunikasi Dalam Gedung',
        ];
        $subPenyelesaian = [
            'PY001 – Konstruksi Khusus Fondasi dan Pondasi',
            'PY002 – Penyelesaian Konstruksi Bangunan',
            'PY003 – Demolisi dan Persiapan Lahan',
            'PY004 – Pemasangan Perancah (Scaffolding)',
        ];

        Klasifikasi::create(['sbu_type_id' => $konstruksi->id, 'name' => 'BG – Bangunan Gedung', 'sub_klasifikasi' => $subBangunanGedung]);
        Klasifikasi::create(['sbu_type_id' => $konstruksi->id, 'name' => 'BS – Bangunan Sipil', 'sub_klasifikasi' => $subBangunanSipil]);
        Klasifikasi::create(['sbu_type_id' => $konstruksi->id, 'name' => 'IN – Instalasi', 'sub_klasifikasi' => $subInstalasi]);
        Klasifikasi::create(['sbu_type_id' => $konstruksi->id, 'name' => 'PY – Penyelesaian Bangunan', 'sub_klasifikasi' => $subPenyelesaian]);

        // =========================================================
        // KLASIFIKASI — SBU Konsultan (KBLI 2020)
        // =========================================================
        $subArsitek = [
            'AR001 – Aktivitas Arsitektur',
            'AR002 – Aktivitas Desain Interior',
            'AR003 – Aktivitas Desain Perkotaan dan Arsitektur Lanskap',
        ];
        $subRekayasa = [
            'RE001 – Aktivitas Rekayasa dan Rancang Bangun',
            'RE002 – Kegiatan Teknik Bangunan Gedung',
            'RE003 – Kegiatan Teknik Sipil',
            'RE004 – Kegiatan Teknik Mekanikal',
            'RE005 – Kegiatan Rekayasa Mekanikal Industri',
            'RE006 – Aktivitas Keinsinyuran dan Konsultasi Teknis',
        ];
        $subManajemen = [
            'MK001 – Manajemen Konstruksi',
            'MK002 – Manajemen Proyek',
            'MK003 – Pengawasan Teknik Konstruksi',
            'MK004 – Pengujian dan Analisis Teknis',
        ];
        $subSurvei = [
            'SP001 – Aktivitas Survei dan Pemetaan',
            'SP002 – Survei Topografi dan Kadaster',
            'SP003 – Eksplorasi Geofisika dan Geologi',
        ];

        Klasifikasi::create(['sbu_type_id' => $konsultan->id, 'name' => 'AR – Arsitektur', 'sub_klasifikasi' => $subArsitek]);
        Klasifikasi::create(['sbu_type_id' => $konsultan->id, 'name' => 'RE – Rekayasa', 'sub_klasifikasi' => $subRekayasa]);
        Klasifikasi::create(['sbu_type_id' => $konsultan->id, 'name' => 'MK – Manajemen Konstruksi', 'sub_klasifikasi' => $subManajemen]);
        Klasifikasi::create(['sbu_type_id' => $konsultan->id, 'name' => 'SP – Survei dan Pemetaan', 'sub_klasifikasi' => $subSurvei]);

        // =========================================================
        // KLASIFIKASI — SKK Konstruksi (per Jenjang)
        // =========================================================
        Klasifikasi::create(['sbu_type_id' => $skk->id, 'name' => 'Teknisi / Analis (Jenjang 4)', 'sub_klasifikasi' => []]);
        Klasifikasi::create(['sbu_type_id' => $skk->id, 'name' => 'Teknisi / Analis (Jenjang 5)', 'sub_klasifikasi' => []]);
        Klasifikasi::create(['sbu_type_id' => $skk->id, 'name' => 'Teknisi / Analis (Jenjang 6)', 'sub_klasifikasi' => []]);
        Klasifikasi::create(['sbu_type_id' => $skk->id, 'name' => 'Ahli Muda (Jenjang 7)', 'sub_klasifikasi' => []]);
        Klasifikasi::create(['sbu_type_id' => $skk->id, 'name' => 'Ahli Madya (Jenjang 8)', 'sub_klasifikasi' => []]);
        Klasifikasi::create(['sbu_type_id' => $skk->id, 'name' => 'Ahli Utama (Jenjang 9)', 'sub_klasifikasi' => []]);

        // =========================================================
        // BIAYA ITEMS — SBU Konstruksi / P3SM
        // =========================================================
        // Kualifikasi (basis harga biaya admin/sertifikat ke asosiasi)
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $p3sm->id, 'category' => 'kualifikasi', 'name' => 'Kecil (K) BUJKN', 'biaya' => 315000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $p3sm->id, 'category' => 'kualifikasi', 'name' => 'Menengah (M) BUJKN', 'biaya' => 2257500]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $p3sm->id, 'category' => 'kualifikasi', 'name' => 'Besar (B1) BUJKN', 'biaya' => 9450000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $p3sm->id, 'category' => 'kualifikasi', 'name' => 'Besar (B2) BUJKN', 'biaya' => 12600000]);

        // Biaya Setor Kantor (tagihan ke klien)
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $p3sm->id, 'category' => 'biaya_setor', 'name' => 'Kecil (K) BUJKN', 'biaya' => 750000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $p3sm->id, 'category' => 'biaya_setor', 'name' => 'Menengah (M) BUJKN', 'biaya' => 2650000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $p3sm->id, 'category' => 'biaya_setor', 'name' => 'Besar (B1) BUJKN', 'biaya' => 11000000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $p3sm->id, 'category' => 'biaya_setor', 'name' => 'Besar (B2) BUJKN', 'biaya' => 14000000]);

        // Biaya Lainnya P3SM
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $p3sm->id, 'category' => 'biaya_lainnya', 'name' => 'Materai', 'biaya' => 10000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $p3sm->id, 'category' => 'biaya_lainnya', 'name' => 'Biaya Admin P3SM', 'biaya' => 50000]);

        // =========================================================
        // BIAYA ITEMS — SBU Konstruksi / GAPEKNAS
        // =========================================================
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $gapeknas->id, 'category' => 'kualifikasi', 'name' => 'Kecil (K)', 'biaya' => 400000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $gapeknas->id, 'category' => 'kualifikasi', 'name' => 'Menengah (M)', 'biaya' => 2500000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $gapeknas->id, 'category' => 'kualifikasi', 'name' => 'Besar (B1)', 'biaya' => 8000000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $gapeknas->id, 'category' => 'kualifikasi', 'name' => 'Besar (B2)', 'biaya' => 10000000]);

        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $gapeknas->id, 'category' => 'biaya_setor', 'name' => 'Kecil (K)', 'biaya' => 900000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $gapeknas->id, 'category' => 'biaya_setor', 'name' => 'Menengah (M)', 'biaya' => 3000000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $gapeknas->id, 'category' => 'biaya_setor', 'name' => 'Besar (B1)', 'biaya' => 10000000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $gapeknas->id, 'category' => 'biaya_setor', 'name' => 'Besar (B2)', 'biaya' => 13000000]);

        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $gapeknas->id, 'category' => 'biaya_lainnya', 'name' => 'Materai', 'biaya' => 10000]);
        BiayaItem::create(['sbu_type_id' => $konstruksi->id, 'asosiasi_id' => $gapeknas->id, 'category' => 'biaya_lainnya', 'name' => 'Biaya Admin GAPEKNAS', 'biaya' => 75000]);

        // =========================================================
        // BIAYA ITEMS — SBU Konsultan
        // Kualifikasi Konsultan (K = Kecil, M = Menengah, B = Besar)
        // =========================================================
        BiayaItem::create(['sbu_type_id' => $konsultan->id, 'category' => 'kualifikasi', 'name' => 'Kualifikasi Kecil (K)', 'biaya' => 450000]);
        BiayaItem::create(['sbu_type_id' => $konsultan->id, 'category' => 'kualifikasi', 'name' => 'Kualifikasi Menengah (M)', 'biaya' => 2200000]);
        BiayaItem::create(['sbu_type_id' => $konsultan->id, 'category' => 'kualifikasi', 'name' => 'Kualifikasi Besar (B)', 'biaya' => 7500000]);

        BiayaItem::create(['sbu_type_id' => $konsultan->id, 'category' => 'biaya_setor', 'name' => 'Kualifikasi Kecil (K)', 'biaya' => 1000000]);
        BiayaItem::create(['sbu_type_id' => $konsultan->id, 'category' => 'biaya_setor', 'name' => 'Kualifikasi Menengah (M)', 'biaya' => 3000000]);
        BiayaItem::create(['sbu_type_id' => $konsultan->id, 'category' => 'biaya_setor', 'name' => 'Kualifikasi Besar (B)', 'biaya' => 10000000]);

        BiayaItem::create(['sbu_type_id' => $konsultan->id, 'category' => 'biaya_lainnya', 'name' => 'Materai', 'biaya' => 10000]);
        BiayaItem::create(['sbu_type_id' => $konsultan->id, 'category' => 'biaya_lainnya', 'name' => 'Biaya Admin Konsultan', 'biaya' => 60000]);

        // =========================================================
        // BIAYA ITEMS — SKK Konstruksi (per Jenjang via LPJK)
        // =========================================================
        BiayaItem::create(['sbu_type_id' => $skk->id, 'asosiasi_id' => $lpjkSkk->id, 'category' => 'kualifikasi', 'name' => 'Jenjang 4 (Teknisi Muda)', 'biaya' => 525000]);
        BiayaItem::create(['sbu_type_id' => $skk->id, 'asosiasi_id' => $lpjkSkk->id, 'category' => 'kualifikasi', 'name' => 'Jenjang 5 (Teknisi Madya)', 'biaya' => 630000]);
        BiayaItem::create(['sbu_type_id' => $skk->id, 'asosiasi_id' => $lpjkSkk->id, 'category' => 'kualifikasi', 'name' => 'Jenjang 6 (Teknisi Utama)', 'biaya' => 735000]);
        BiayaItem::create(['sbu_type_id' => $skk->id, 'asosiasi_id' => $lpjkSkk->id, 'category' => 'kualifikasi', 'name' => 'Jenjang 7 (Ahli Muda)', 'biaya' => 1050000]);
        BiayaItem::create(['sbu_type_id' => $skk->id, 'asosiasi_id' => $lpjkSkk->id, 'category' => 'kualifikasi', 'name' => 'Jenjang 8 (Ahli Madya)', 'biaya' => 1365000]);
        BiayaItem::create(['sbu_type_id' => $skk->id, 'asosiasi_id' => $lpjkSkk->id, 'category' => 'kualifikasi', 'name' => 'Jenjang 9 (Ahli Utama)', 'biaya' => 1680000]);

        BiayaItem::create(['sbu_type_id' => $skk->id, 'asosiasi_id' => $lpjkSkk->id, 'category' => 'biaya_setor', 'name' => 'Jenjang 4 (Teknisi Muda)', 'biaya' => 1200000]);
        BiayaItem::create(['sbu_type_id' => $skk->id, 'asosiasi_id' => $lpjkSkk->id, 'category' => 'biaya_setor', 'name' => 'Jenjang 5 (Teknisi Madya)', 'biaya' => 1500000]);
        BiayaItem::create(['sbu_type_id' => $skk->id, 'asosiasi_id' => $lpjkSkk->id, 'category' => 'biaya_setor', 'name' => 'Jenjang 6 (Teknisi Utama)', 'biaya' => 1800000]);
        BiayaItem::create(['sbu_type_id' => $skk->id, 'asosiasi_id' => $lpjkSkk->id, 'category' => 'biaya_setor', 'name' => 'Jenjang 7 (Ahli Muda)', 'biaya' => 2500000]);
        BiayaItem::create(['sbu_type_id' => $skk->id, 'asosiasi_id' => $lpjkSkk->id, 'category' => 'biaya_setor', 'name' => 'Jenjang 8 (Ahli Madya)', 'biaya' => 3500000]);
        BiayaItem::create(['sbu_type_id' => $skk->id, 'asosiasi_id' => $lpjkSkk->id, 'category' => 'biaya_setor', 'name' => 'Jenjang 9 (Ahli Utama)', 'biaya' => 5000000]);

        BiayaItem::create(['sbu_type_id' => $skk->id, 'asosiasi_id' => $lpjkSkk->id, 'category' => 'biaya_lainnya', 'name' => 'Materai', 'biaya' => 10000]);
        BiayaItem::create(['sbu_type_id' => $skk->id, 'asosiasi_id' => $lpjkSkk->id, 'category' => 'biaya_lainnya', 'name' => 'Biaya Admin LPJK', 'biaya' => 100000]);

        // =========================================================
        // BIAYA ITEMS — Dokumen SMAP
        // =========================================================
        BiayaItem::create(['sbu_type_id' => $smap->id, 'category' => 'biaya_setor', 'name' => 'Dokumen SMAP Standar', 'biaya' => 3000000]);
        BiayaItem::create(['sbu_type_id' => $smap->id, 'category' => 'biaya_setor', 'name' => 'Dokumen SMAP Lengkap', 'biaya' => 5000000]);

        // =========================================================
        // BIAYA ITEMS — Akun SIMPK dan Alat
        // =========================================================
        BiayaItem::create(['sbu_type_id' => $simpk->id, 'category' => 'biaya_setor', 'name' => 'Akun SIMPK Basic', 'biaya' => 500000]);
        BiayaItem::create(['sbu_type_id' => $simpk->id, 'category' => 'biaya_setor', 'name' => 'Akun SIMPK + 1 Alat', 'biaya' => 1000000]);
        BiayaItem::create(['sbu_type_id' => $simpk->id, 'category' => 'biaya_setor', 'name' => 'Akun SIMPK + 3 Alat', 'biaya' => 1500000]);
        BiayaItem::create(['sbu_type_id' => $simpk->id, 'category' => 'biaya_setor', 'name' => 'Alat Saja', 'biaya' => 300000]);

        // =========================================================
        // BIAYA ITEMS — Notaris (from JSON: Data Harga PENDIRIAN CV DAN PT)
        // =========================================================
        // kualifikasi.biaya = Stor Ke Randy (Column7), biaya_setor = Stor Kantor (Column6), kode = Paket (Column5)
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'kualifikasi', 'name' => 'Pendirian PT (Modal Dasar Max 1M)', 'kode' => 'PAKET DASAR', 'biaya' => 3200000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'kualifikasi', 'name' => 'Pendirian PT (Modal Dasar Max 1M)', 'kode' => 'PAKET LENGKAP', 'biaya' => 3300000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'kualifikasi', 'name' => 'Pendirian PT (Modal Dasar di Atas Max 1M)', 'kode' => 'PAKET DASAR', 'biaya' => 3700000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'kualifikasi', 'name' => 'Pendirian PT (Modal Dasar di Atas Max 1M)', 'kode' => 'PAKET LENGKAP', 'biaya' => 3800000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'kualifikasi', 'name' => 'Pendirian CV', 'kode' => 'PAKET DASAR', 'biaya' => 2400000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'kualifikasi', 'name' => 'Pendirian CV', 'kode' => 'PAKET LENGKAP', 'biaya' => 2500000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'kualifikasi', 'name' => 'Pendirian Yayasan', 'kode' => 'PAKET DASAR', 'biaya' => 3200000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'kualifikasi', 'name' => 'Pendirian Yayasan', 'kode' => 'PAKET LENGKAP', 'biaya' => 3300000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'kualifikasi', 'name' => 'Pendirian Persekutuan Perdata', 'kode' => 'PAKET DASAR', 'biaya' => 2900000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'kualifikasi', 'name' => 'Pendirian Persekutuan Perdata', 'kode' => 'PAKET LENGKAP', 'biaya' => 3000000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'kualifikasi', 'name' => 'Pendirian Firma', 'kode' => 'PAKET DASAR', 'biaya' => 2900000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'kualifikasi', 'name' => 'Pendirian Firma', 'kode' => 'PAKET LENGKAP', 'biaya' => 3000000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'kualifikasi', 'name' => 'Perkumpulan/ Komunitas', 'kode' => 'PAKET DASAR', 'biaya' => 3900000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'kualifikasi', 'name' => 'Perkumpulan/ Komunitas', 'kode' => 'PAKET LENGKAP', 'biaya' => 4000000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'kualifikasi', 'name' => 'Pendirian PT PMA', 'kode' => 'PAKET DASAR', 'biaya' => 5200000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'kualifikasi', 'name' => 'Pendirian PT PMA', 'kode' => 'PAKET LENGKAP', 'biaya' => 5300000]);

        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'biaya_setor', 'name' => 'Pendirian PT (Modal Dasar Max 1M)', 'kode' => 'PAKET DASAR', 'biaya' => 3800000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'biaya_setor', 'name' => 'Pendirian PT (Modal Dasar Max 1M)', 'kode' => 'PAKET LENGKAP', 'biaya' => 4100000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'biaya_setor', 'name' => 'Pendirian PT (Modal Dasar di Atas Max 1M)', 'kode' => 'PAKET DASAR', 'biaya' => 4600000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'biaya_setor', 'name' => 'Pendirian PT (Modal Dasar di Atas Max 1M)', 'kode' => 'PAKET LENGKAP', 'biaya' => 4800000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'biaya_setor', 'name' => 'Pendirian CV', 'kode' => 'PAKET DASAR', 'biaya' => 2900000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'biaya_setor', 'name' => 'Pendirian CV', 'kode' => 'PAKET LENGKAP', 'biaya' => 3200000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'biaya_setor', 'name' => 'Pendirian Yayasan', 'kode' => 'PAKET DASAR', 'biaya' => 3800000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'biaya_setor', 'name' => 'Pendirian Yayasan', 'kode' => 'PAKET LENGKAP', 'biaya' => 4200000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'biaya_setor', 'name' => 'Pendirian Persekutuan Perdata', 'kode' => 'PAKET DASAR', 'biaya' => 3500000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'biaya_setor', 'name' => 'Pendirian Persekutuan Perdata', 'kode' => 'PAKET LENGKAP', 'biaya' => 3700000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'biaya_setor', 'name' => 'Pendirian Firma', 'kode' => 'PAKET DASAR', 'biaya' => 3500000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'biaya_setor', 'name' => 'Pendirian Firma', 'kode' => 'PAKET LENGKAP', 'biaya' => 3700000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'biaya_setor', 'name' => 'Perkumpulan/ Komunitas', 'kode' => 'PAKET DASAR', 'biaya' => 4500000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'biaya_setor', 'name' => 'Perkumpulan/ Komunitas', 'kode' => 'PAKET LENGKAP', 'biaya' => 4700000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'biaya_setor', 'name' => 'Pendirian PT PMA', 'kode' => 'PAKET DASAR', 'biaya' => 7200000]);
        BiayaItem::create(['sbu_type_id' => $notaris->id, 'category' => 'biaya_setor', 'name' => 'Pendirian PT PMA', 'kode' => 'PAKET LENGKAP', 'biaya' => 8300000]);

        // =========================================================
        // BIAYA ITEMS — Sewa SKK Tenaga Ahli (from Excel)
        // kualifikasi.biaya = Stor Ke RIA, biaya_setor = Stor Kantor
        // =========================================================
        BiayaItem::create(['sbu_type_id' => $sewaSkk->id, 'category' => 'kualifikasi', 'name' => 'Sembilan (9)', 'biaya' => 0]);
        BiayaItem::create(['sbu_type_id' => $sewaSkk->id, 'category' => 'kualifikasi', 'name' => 'Delapan (8)', 'biaya' => 7300000]);
        BiayaItem::create(['sbu_type_id' => $sewaSkk->id, 'category' => 'kualifikasi', 'name' => 'Tujuh (7)', 'biaya' => 5300000]);
        BiayaItem::create(['sbu_type_id' => $sewaSkk->id, 'category' => 'kualifikasi', 'name' => 'Enam (6)', 'biaya' => 3800000]);
        BiayaItem::create(['sbu_type_id' => $sewaSkk->id, 'category' => 'kualifikasi', 'name' => 'Lima (5)', 'biaya' => 2200000]);
        BiayaItem::create(['sbu_type_id' => $sewaSkk->id, 'category' => 'kualifikasi', 'name' => 'Empat (4)', 'biaya' => 2100000]);

        BiayaItem::create(['sbu_type_id' => $sewaSkk->id, 'category' => 'biaya_setor', 'name' => 'Sembilan (9)', 'biaya' => 0]);
        BiayaItem::create(['sbu_type_id' => $sewaSkk->id, 'category' => 'biaya_setor', 'name' => 'Delapan (8)', 'biaya' => 8800000]);
        BiayaItem::create(['sbu_type_id' => $sewaSkk->id, 'category' => 'biaya_setor', 'name' => 'Tujuh (7)', 'biaya' => 6300000]);
        BiayaItem::create(['sbu_type_id' => $sewaSkk->id, 'category' => 'biaya_setor', 'name' => 'Enam (6)', 'biaya' => 4500000]);
        BiayaItem::create(['sbu_type_id' => $sewaSkk->id, 'category' => 'biaya_setor', 'name' => 'Lima (5)', 'biaya' => 2800000]);
        BiayaItem::create(['sbu_type_id' => $sewaSkk->id, 'category' => 'biaya_setor', 'name' => 'Empat (4)', 'biaya' => 2600000]);

        // =========================================================
        // BIAYA ITEMS — SMK3 Perusahaan Kemenaker (from Excel)
        // kualifikasi.biaya = Stor P3SM, biaya_setor = Stor Kantor, kode = Keterangan
        // =========================================================
        BiayaItem::create(['sbu_type_id' => $smk3->id, 'category' => 'kualifikasi', 'name' => 'SMK3 PERUSAHAAN', 'kode' => 'TIDAK PAKET DENGAN K3 UMUM', 'biaya' => 27000000]);
        BiayaItem::create(['sbu_type_id' => $smk3->id, 'category' => 'kualifikasi', 'name' => 'SMK3 PERUSAHAAN', 'kode' => 'PAKET DENGAN K3 UMUM', 'biaya' => 29000000]);

        BiayaItem::create(['sbu_type_id' => $smk3->id, 'category' => 'biaya_setor', 'name' => 'SMK3 PERUSAHAAN', 'kode' => 'TIDAK PAKET DENGAN K3 UMUM', 'biaya' => 29000000]);
        BiayaItem::create(['sbu_type_id' => $smk3->id, 'category' => 'biaya_setor', 'name' => 'SMK3 PERUSAHAAN', 'kode' => 'PAKET DENGAN K3 UMUM', 'biaya' => 32000000]);

        // =========================================================
        // BIAYA ITEMS — AK3 Umum Kemenaker (from Excel)
        // kualifikasi.biaya = Stor P3SM, biaya_setor = Stor Kantor
        // =========================================================
        BiayaItem::create(['sbu_type_id' => $ak3->id, 'category' => 'kualifikasi', 'name' => 'AK3 UMUM', 'biaya' => 3420000]);

        BiayaItem::create(['sbu_type_id' => $ak3->id, 'category' => 'biaya_setor', 'name' => 'AK3 UMUM', 'biaya' => 4500000]);

        // =========================================================
        // BIAYA ITEMS — ISO Lokal (from Excel)
        // kualifikasi.biaya = Stor Ke SmartSolution, biaya_setor = Stor Kantor, kode = Kode ISO
        // =========================================================
        BiayaItem::create(['sbu_type_id' => $isoLokal->id, 'category' => 'kualifikasi', 'name' => 'SISTEM MANAJEMEN MUTU', 'kode' => '9001 (2015)', 'biaya' => 2000000]);
        BiayaItem::create(['sbu_type_id' => $isoLokal->id, 'category' => 'kualifikasi', 'name' => 'SISTEM MANAJEMEN LINGKUNGAN', 'kode' => '14001 (2018)', 'biaya' => 2000000]);
        BiayaItem::create(['sbu_type_id' => $isoLokal->id, 'category' => 'kualifikasi', 'name' => 'SISTEM MANAJEMEN KESEHATAN DAN KESELAMATAN', 'kode' => '45001 (2015)', 'biaya' => 2000000]);
        BiayaItem::create(['sbu_type_id' => $isoLokal->id, 'category' => 'kualifikasi', 'name' => 'PAKET 3 ISO', 'kode' => '9001, 14001, 45001', 'biaya' => 6000000]);

        BiayaItem::create(['sbu_type_id' => $isoLokal->id, 'category' => 'biaya_setor', 'name' => 'SISTEM MANAJEMEN MUTU', 'kode' => '9001 (2015)', 'biaya' => 2500000]);
        BiayaItem::create(['sbu_type_id' => $isoLokal->id, 'category' => 'biaya_setor', 'name' => 'SISTEM MANAJEMEN LINGKUNGAN', 'kode' => '14001 (2018)', 'biaya' => 2500000]);
        BiayaItem::create(['sbu_type_id' => $isoLokal->id, 'category' => 'biaya_setor', 'name' => 'SISTEM MANAJEMEN KESEHATAN DAN KESELAMATAN', 'kode' => '45001 (2015)', 'biaya' => 2500000]);
        BiayaItem::create(['sbu_type_id' => $isoLokal->id, 'category' => 'biaya_setor', 'name' => 'PAKET 3 ISO', 'kode' => '9001, 14001, 45001', 'biaya' => 7500000]);

        // =========================================================
        // BIAYA ITEMS — ISO UAF Americo (from Excel)
        // kualifikasi.biaya = Stor Ke Americo, biaya_setor = Stor Kantor
        // =========================================================
        BiayaItem::create(['sbu_type_id' => $isoUaf->id, 'category' => 'kualifikasi', 'name' => 'SISTEM MANAJEMEN MUTU', 'kode' => '9001 (2015)', 'biaya' => 4000000]);
        BiayaItem::create(['sbu_type_id' => $isoUaf->id, 'category' => 'kualifikasi', 'name' => 'SISTEM MANAJEMEN LINGKUNGAN', 'kode' => '14001 (2018)', 'biaya' => 4000000]);
        BiayaItem::create(['sbu_type_id' => $isoUaf->id, 'category' => 'kualifikasi', 'name' => 'SISTEM MANAJEMEN KESEHATAN DAN KESELAMATAN', 'kode' => '45001 (2015)', 'biaya' => 4000000]);
        BiayaItem::create(['sbu_type_id' => $isoUaf->id, 'category' => 'kualifikasi', 'name' => 'SISTEM MANAJEMEN ANTI PENYUAPAN (SMAP)', 'kode' => '37001 (2016)', 'biaya' => 8000000]);
        BiayaItem::create(['sbu_type_id' => $isoUaf->id, 'category' => 'kualifikasi', 'name' => 'PAKET 3 ISO', 'kode' => '9001, 14001, 45001', 'biaya' => 10000000]);

        BiayaItem::create(['sbu_type_id' => $isoUaf->id, 'category' => 'biaya_setor', 'name' => 'SISTEM MANAJEMEN MUTU', 'kode' => '9001 (2015)', 'biaya' => 5000000]);
        BiayaItem::create(['sbu_type_id' => $isoUaf->id, 'category' => 'biaya_setor', 'name' => 'SISTEM MANAJEMEN LINGKUNGAN', 'kode' => '14001 (2018)', 'biaya' => 5000000]);
        BiayaItem::create(['sbu_type_id' => $isoUaf->id, 'category' => 'biaya_setor', 'name' => 'SISTEM MANAJEMEN KESEHATAN DAN KESELAMATAN', 'kode' => '45001 (2015)', 'biaya' => 5000000]);
        BiayaItem::create(['sbu_type_id' => $isoUaf->id, 'category' => 'biaya_setor', 'name' => 'SISTEM MANAJEMEN ANTI PENYUAPAN (SMAP)', 'kode' => '37001 (2016)', 'biaya' => 11000000]);
        BiayaItem::create(['sbu_type_id' => $isoUaf->id, 'category' => 'biaya_setor', 'name' => 'PAKET 3 ISO', 'kode' => '9001, 14001, 45001', 'biaya' => 15000000]);

        // =========================================================
        // BIAYA ITEMS — ISO KAN P3SM (from Excel)
        // kualifikasi.biaya = Stor Ke P3SM, biaya_setor = Stor Kantor
        // =========================================================
        BiayaItem::create(['sbu_type_id' => $isoKan->id, 'category' => 'kualifikasi', 'name' => 'SISTEM MANAJEMEN MUTU', 'kode' => '9001 (2015)', 'biaya' => 17000000]);
        BiayaItem::create(['sbu_type_id' => $isoKan->id, 'category' => 'kualifikasi', 'name' => 'SISTEM MANAJEMEN LINGKUNGAN', 'kode' => '14001 (2018)', 'biaya' => 18000000]);
        BiayaItem::create(['sbu_type_id' => $isoKan->id, 'category' => 'kualifikasi', 'name' => 'SISTEM MANAJEMEN KESEHATAN DAN KESELAMATAN', 'kode' => '45001 (2015)', 'biaya' => 19000000]);
        BiayaItem::create(['sbu_type_id' => $isoKan->id, 'category' => 'kualifikasi', 'name' => 'SISTEM MANAJEMEN ANTI PENYUAPAN (SMAP)', 'kode' => '37001 (2016)', 'biaya' => 27000000]);

        BiayaItem::create(['sbu_type_id' => $isoKan->id, 'category' => 'biaya_setor', 'name' => 'SISTEM MANAJEMEN MUTU', 'kode' => '9001 (2015)', 'biaya' => 18000000]);
        BiayaItem::create(['sbu_type_id' => $isoKan->id, 'category' => 'biaya_setor', 'name' => 'SISTEM MANAJEMEN LINGKUNGAN', 'kode' => '14001 (2018)', 'biaya' => 19000000]);
        BiayaItem::create(['sbu_type_id' => $isoKan->id, 'category' => 'biaya_setor', 'name' => 'SISTEM MANAJEMEN KESEHATAN DAN KESELAMATAN', 'kode' => '45001 (2015)', 'biaya' => 25000000]);
        BiayaItem::create(['sbu_type_id' => $isoKan->id, 'category' => 'biaya_setor', 'name' => 'SISTEM MANAJEMEN ANTI PENYUAPAN (SMAP)', 'kode' => '37001 (2016)', 'biaya' => 38000000]);

        // =========================================================
        // BIAYA ITEMS — KAP Non Barcode Alam (from Excel)
        // kualifikasi.biaya = Stor Ke Alam, biaya_setor = Stor Kantor
        // =========================================================
        BiayaItem::create(['sbu_type_id' => $kapNonBarcode->id, 'category' => 'kualifikasi', 'name' => '1 M - 5 M', 'biaya' => 4000000]);
        BiayaItem::create(['sbu_type_id' => $kapNonBarcode->id, 'category' => 'kualifikasi', 'name' => '5 M - 10 M', 'biaya' => 6500000]);
        BiayaItem::create(['sbu_type_id' => $kapNonBarcode->id, 'category' => 'kualifikasi', 'name' => '10 M - 20 M', 'biaya' => 8500000]);
        BiayaItem::create(['sbu_type_id' => $kapNonBarcode->id, 'category' => 'kualifikasi', 'name' => '20 M - 50 M', 'biaya' => 8500000]);
        BiayaItem::create(['sbu_type_id' => $kapNonBarcode->id, 'category' => 'kualifikasi', 'name' => '100 M - 200 M', 'biaya' => 8500000]);
        BiayaItem::create(['sbu_type_id' => $kapNonBarcode->id, 'category' => 'kualifikasi', 'name' => '200 M - 400 M', 'biaya' => 8500000]);
        BiayaItem::create(['sbu_type_id' => $kapNonBarcode->id, 'category' => 'kualifikasi', 'name' => '400 M - 600 M', 'biaya' => 8500000]);

        BiayaItem::create(['sbu_type_id' => $kapNonBarcode->id, 'category' => 'biaya_setor', 'name' => '1 M - 5 M', 'biaya' => 6000000]);
        BiayaItem::create(['sbu_type_id' => $kapNonBarcode->id, 'category' => 'biaya_setor', 'name' => '5 M - 10 M', 'biaya' => 8500000]);
        BiayaItem::create(['sbu_type_id' => $kapNonBarcode->id, 'category' => 'biaya_setor', 'name' => '10 M - 20 M', 'biaya' => 11000000]);
        BiayaItem::create(['sbu_type_id' => $kapNonBarcode->id, 'category' => 'biaya_setor', 'name' => '20 M - 50 M', 'biaya' => 12000000]);
        BiayaItem::create(['sbu_type_id' => $kapNonBarcode->id, 'category' => 'biaya_setor', 'name' => '100 M - 200 M', 'biaya' => 13000000]);
        BiayaItem::create(['sbu_type_id' => $kapNonBarcode->id, 'category' => 'biaya_setor', 'name' => '200 M - 400 M', 'biaya' => 14500000]);
        BiayaItem::create(['sbu_type_id' => $kapNonBarcode->id, 'category' => 'biaya_setor', 'name' => '400 M - 600 M', 'biaya' => 15000000]);

        // =========================================================
        // BIAYA ITEMS — KAP Barcode Tidak Audit (from Excel)
        // kualifikasi.biaya = Stor, biaya_setor = Stor Kantor
        // =========================================================
        BiayaItem::create(['sbu_type_id' => $kapBarcode->id, 'category' => 'kualifikasi', 'name' => '0 M - 10 M', 'biaya' => 19500000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcode->id, 'category' => 'kualifikasi', 'name' => '10 M - 20 M', 'biaya' => 20000000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcode->id, 'category' => 'kualifikasi', 'name' => '20 M - 30 M', 'biaya' => 22500000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcode->id, 'category' => 'kualifikasi', 'name' => '30 M - 40 M', 'biaya' => 25500000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcode->id, 'category' => 'kualifikasi', 'name' => '40 M - 50 M', 'biaya' => 29000000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcode->id, 'category' => 'kualifikasi', 'name' => '50 M - 60 M', 'biaya' => 44500000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcode->id, 'category' => 'kualifikasi', 'name' => '60 M - 65 M', 'biaya' => 46000000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcode->id, 'category' => 'kualifikasi', 'name' => '65 M - 70 M', 'biaya' => 49000000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcode->id, 'category' => 'kualifikasi', 'name' => '70 M - 75 M', 'biaya' => 51000000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcode->id, 'category' => 'kualifikasi', 'name' => '75 M - 80 M', 'biaya' => 53500000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcode->id, 'category' => 'kualifikasi', 'name' => '85 M - 90 M', 'biaya' => 55500000]);

        BiayaItem::create(['sbu_type_id' => $kapBarcode->id, 'category' => 'biaya_setor', 'name' => '0 M - 10 M', 'biaya' => 20500000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcode->id, 'category' => 'biaya_setor', 'name' => '10 M - 20 M', 'biaya' => 21000000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcode->id, 'category' => 'biaya_setor', 'name' => '20 M - 30 M', 'biaya' => 23500000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcode->id, 'category' => 'biaya_setor', 'name' => '30 M - 40 M', 'biaya' => 26500000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcode->id, 'category' => 'biaya_setor', 'name' => '40 M - 50 M', 'biaya' => 30000000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcode->id, 'category' => 'biaya_setor', 'name' => '50 M - 60 M', 'biaya' => 46500000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcode->id, 'category' => 'biaya_setor', 'name' => '60 M - 65 M', 'biaya' => 48000000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcode->id, 'category' => 'biaya_setor', 'name' => '65 M - 70 M', 'biaya' => 51000000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcode->id, 'category' => 'biaya_setor', 'name' => '70 M - 75 M', 'biaya' => 53000000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcode->id, 'category' => 'biaya_setor', 'name' => '75 M - 80 M', 'biaya' => 55500000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcode->id, 'category' => 'biaya_setor', 'name' => '85 M - 90 M', 'biaya' => 57500000]);

        // =========================================================
        // BIAYA ITEMS — KAP Barcode P3SM Sistem Audit (from Excel)
        // kualifikasi.biaya = Stor Ke P3SM, biaya_setor = Stor Kantor
        // =========================================================
        BiayaItem::create(['sbu_type_id' => $kapBarcodeP3sm->id, 'category' => 'kualifikasi', 'name' => '0 M - 10 M', 'biaya' => 17500000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcodeP3sm->id, 'category' => 'kualifikasi', 'name' => '10 M - 20 M', 'biaya' => 21000000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcodeP3sm->id, 'category' => 'kualifikasi', 'name' => '20 M - 30 M', 'biaya' => 22000000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcodeP3sm->id, 'category' => 'kualifikasi', 'name' => '30 M - 40 M', 'biaya' => 25000000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcodeP3sm->id, 'category' => 'kualifikasi', 'name' => '40 M - 50 M', 'biaya' => 27500000]);

        BiayaItem::create(['sbu_type_id' => $kapBarcodeP3sm->id, 'category' => 'biaya_setor', 'name' => '0 M - 10 M', 'biaya' => 18500000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcodeP3sm->id, 'category' => 'biaya_setor', 'name' => '10 M - 20 M', 'biaya' => 22500000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcodeP3sm->id, 'category' => 'biaya_setor', 'name' => '20 M - 30 M', 'biaya' => 24000000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcodeP3sm->id, 'category' => 'biaya_setor', 'name' => '30 M - 40 M', 'biaya' => 27000000]);
        BiayaItem::create(['sbu_type_id' => $kapBarcodeP3sm->id, 'category' => 'biaya_setor', 'name' => '40 M - 50 M', 'biaya' => 30000000]);

        // =========================================================
        // SAMPLE SUBMISSIONS
        // =========================================================
        Submission::create([
            'company_name' => 'PT. Maju Bersama',
            'marketing_name' => 'Hidayatullah',
            'input_date' => '2025-08-01',
            'submitted_by_id' => $admin->id,
            'certificate_type' => 'SBU Konstruksi',
            'sbu_type' => 'konstruksi',
            'selected_sub' => ['id' => $p3sm->id, 'name' => 'P3SM'],
            'selected_klasifikasi' => ['id' => 'bg', 'name' => 'BG – Bangunan Gedung', 'subKlasifikasi' => $subBangunanGedung],
            'selected_sub_klasifikasi' => 'BG001 – Konstruksi Gedung Hunian',
            'selected_kualifikasi' => ['id' => 'p3sm-k', 'name' => 'Kecil (K) BUJKN', 'biaya' => 315000],
            'selected_biaya_lainnya' => null,
            'biaya_setor_kantor' => 750000,
            'keuntungan' => 435000,
        ]);
        Submission::create([
            'company_name' => 'CV. Jaya Abadi',
            'marketing_name' => 'Nuzul Arifin',
            'input_date' => '2025-08-03',
            'submitted_by_id' => $admin->id,
            'certificate_type' => 'SBU Konstruksi',
            'sbu_type' => 'konstruksi',
            'selected_sub' => ['id' => $gapeknas->id, 'name' => 'GAPEKNAS'],
            'selected_klasifikasi' => ['id' => 'bs', 'name' => 'BS – Bangunan Sipil', 'subKlasifikasi' => $subBangunanSipil],
            'selected_sub_klasifikasi' => 'BS001 – Konstruksi Jalan dan Jembatan',
            'selected_kualifikasi' => ['id' => 'gpk-m', 'name' => 'Menengah (M)', 'biaya' => 2500000],
            'selected_biaya_lainnya' => null,
            'biaya_setor_kantor' => 3000000,
            'keuntungan' => 500000,
        ]);
        Submission::create([
            'company_name' => 'PT. Konstraktor Handal',
            'marketing_name' => 'Abdul Fatahurahman',
            'input_date' => '2025-08-05',
            'submitted_by_id' => $manager->id,
            'certificate_type' => 'SBU Konstruksi',
            'sbu_type' => 'konstruksi',
            'selected_sub' => ['id' => $p3sm->id, 'name' => 'P3SM'],
            'selected_klasifikasi' => ['id' => 'in', 'name' => 'IN – Instalasi', 'subKlasifikasi' => $subInstalasi],
            'selected_sub_klasifikasi' => 'IN001 – Instalasi Sistem Mekanikal dan Elektrikal Bangunan Gedung',
            'selected_kualifikasi' => ['id' => 'p3sm-m', 'name' => 'Menengah (M) BUJKN', 'biaya' => 2257500],
            'selected_biaya_lainnya' => ['id' => 'materai', 'name' => 'Materai', 'biaya' => 10000],
            'biaya_setor_kantor' => 2650000,
            'keuntungan' => 382500,
        ]);
        Submission::create([
            'company_name' => 'PT. Konsultan Prima',
            'marketing_name' => 'Hidayatullah',
            'input_date' => '2025-08-07',
            'submitted_by_id' => $admin->id,
            'certificate_type' => 'SBU Konsultan',
            'sbu_type' => 'konsultan',
            'selected_sub' => null,
            'selected_klasifikasi' => ['id' => 're', 'name' => 'RE – Rekayasa', 'subKlasifikasi' => $subRekayasa],
            'selected_sub_klasifikasi' => 'RE001 – Aktivitas Rekayasa dan Rancang Bangun',
            'selected_kualifikasi' => ['id' => 'kon-k', 'name' => 'Kualifikasi Kecil (K)', 'biaya' => 450000],
            'selected_biaya_lainnya' => null,
            'biaya_setor_kantor' => 1000000,
            'keuntungan' => 550000,
        ]);
        Submission::create([
            'company_name' => 'Ir. Budi Santoso',
            'marketing_name' => 'Nuzul Arifin',
            'input_date' => '2025-08-09',
            'submitted_by_id' => $karyawan->id,
            'certificate_type' => 'SKK Konstruksi',
            'sbu_type' => 'skk',
            'selected_sub' => ['id' => $lpjkSkk->id, 'name' => 'LPJK'],
            'selected_klasifikasi' => ['id' => 'j8', 'name' => 'Ahli Madya (Jenjang 8)', 'subKlasifikasi' => []],
            'selected_sub_klasifikasi' => null,
            'selected_kualifikasi' => ['id' => 'skk-j8', 'name' => 'Jenjang 8 (Ahli Madya)', 'biaya' => 1365000],
            'selected_biaya_lainnya' => ['id' => 'lpjk-admin', 'name' => 'Biaya Admin LPJK', 'biaya' => 100000],
            'biaya_setor_kantor' => 3500000,
            'keuntungan' => 2035000,
        ]);
        Submission::create([
            'company_name' => 'CV. Aman Sejahtera',
            'marketing_name' => 'Abdul Fatahurahman',
            'input_date' => '2025-08-11',
            'submitted_by_id' => $admin->id,
            'certificate_type' => 'Dokumen SMAP',
            'sbu_type' => 'smap',
            'selected_sub' => null,
            'selected_klasifikasi' => null,
            'selected_sub_klasifikasi' => null,
            'selected_kualifikasi' => null,
            'selected_biaya_lainnya' => null,
            'biaya_setor_kantor' => 5000000,
            'keuntungan' => 2000000,
        ]);
        Submission::create([
            'company_name' => 'PT. Notaris Terpercaya',
            'marketing_name' => 'Hidayatullah',
            'input_date' => '2025-08-13',
            'submitted_by_id' => $admin->id,
            'certificate_type' => 'Notaris',
            'sbu_type' => 'notaris',
            'selected_sub' => null,
            'selected_klasifikasi' => null,
            'selected_sub_klasifikasi' => null,
            'selected_kualifikasi' => ['id' => 'not-paket', 'name' => 'Paket Lengkap Pendirian', 'biaya' => 3000000],
            'selected_biaya_lainnya' => ['id' => 'not-materai', 'name' => 'Materai', 'biaya' => 10000],
            'biaya_setor_kantor' => 5000000,
            'keuntungan' => 1990000,
        ]);

        // =========================================================
        // SAMPLE TRANSACTIONS
        // =========================================================
        Transaction::create(['transaction_date' => '2025-08-05', 'transaction_name' => 'Kas Operasional', 'cost' => 25000, 'transaction_type' => 'Keluar', 'submitted_by_id' => $admin->id, 'proof' => null]);
        Transaction::create(['transaction_date' => '2025-08-01', 'transaction_name' => 'Tabungan Ruko', 'cost' => 5000000, 'transaction_type' => 'Tabungan', 'submitted_by_id' => $admin->id, 'proof' => null]);
        Transaction::create(['transaction_date' => '2025-08-02', 'transaction_name' => 'Beli ATK', 'cost' => 150000, 'transaction_type' => 'Keluar', 'submitted_by_id' => $admin->id, 'proof' => null]);
        Transaction::create(['transaction_date' => '2025-08-10', 'transaction_name' => 'Tabungan Bulanan', 'cost' => 2000000, 'transaction_type' => 'Tabungan', 'submitted_by_id' => $admin->id, 'proof' => null]);
        Transaction::create(['transaction_date' => '2025-08-15', 'transaction_name' => 'Bayar Listrik', 'cost' => 350000, 'transaction_type' => 'Keluar', 'submitted_by_id' => $admin->id, 'proof' => null]);
        Transaction::create(['transaction_date' => '2025-08-20', 'transaction_name' => 'Kas Harian', 'cost' => 50000, 'transaction_type' => 'Kas', 'submitted_by_id' => $admin->id, 'proof' => null]);
    }
}
