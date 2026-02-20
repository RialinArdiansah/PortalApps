// =====================================================
// PortalApp_Keu — Legacy Seed Data
// This is the EXACT data from the legacy system
// Structure MUST NOT be modified
// =====================================================

import type {
    User, Certificate, MarketingName, SbuData, KlasifikasiData,
    BiayaData, Submission, Transaction, AdminTitles, FeeP3SM,
} from '@/types';

// --- Users (passwords are base64-encoded) ---
export const initialUsers: User[] = [
    { id: 'user-super-admin-1', fullName: 'Super Admin', username: 'superadmin', email: 'superadmin@sultangroup.com', password: 'c3VwZXJhZG1pbjEyMw==', role: 'Super admin' },
    { id: 'user-admin-1', fullName: 'Admin', username: 'admin', email: 'admin@sultangroup.com', password: 'YWRtaW4xMjM=', role: 'admin' },
    { id: 'user-manager-1', fullName: 'Manager', username: 'manager', email: 'manager@sultangroup.com', password: 'bWFuYWdlcjEyMw==', role: 'manager' },
    { id: 'user-karyawan-1', fullName: 'Karyawan', username: 'karyawan', email: 'karyawan@sultangroup.com', password: 'a2FyeWF3YW4xMjM=', role: 'karyawan' },
    { id: 'user-marketing-1', fullName: 'Marketing', username: 'marketing', email: 'marketing@sultangroup.com', password: 'bWFya2V0aW5nMTIz', role: 'marketing' },
    { id: 'user-mitra-1', fullName: 'Mitra', username: 'mitra', email: 'mitra@sultangroup.com', password: 'bWl0cmExMjM=', role: 'mitra' },
];

// --- Certificates ---
export const initialCertificates: Certificate[] = [
    { id: 'cert-1', name: 'SBU Konstruksi', subMenus: [] },
    { id: 'cert-2', name: 'SKK Konstruksi', subMenus: [] },
    { id: 'cert-3', name: 'SBU Konsultan', subMenus: [] },
    { id: 'cert-4', name: 'Dokumen SMAP', subMenus: [] },
    { id: 'cert-5', name: 'Akun SIMPK dan Alat', subMenus: [] },
    { id: 'cert-6', name: 'Notaris', subMenus: [] },
];

// --- Marketing Names ---
export const initialMarketingNames: MarketingName[] = [
    { id: 'mkt-1', name: 'Hidayatullah' },
    { id: 'mkt-2', name: 'Abdul Fatahurahman' },
    { id: 'mkt-3', name: 'Nuzul Arifin' },
];

// --- Admin Titles ---
export const initialAdminTitles: AdminTitles = {
    users: 'Daftar Pengguna',
    certificates: 'Daftar Sertifikat',
    marketing: 'Manajemen Marketing',
    submissions: 'Data Input Pengguna',
    mySubmissions: 'Data Input Saya',
    transactions: 'Entri Transaksi Saya',
    financialReport: 'Laporan Keuangan Saya',
};

// =====================================================
// SBU KONSTRUKSI DATA
// =====================================================
export const initialSbuKonstruksiData: SbuData[] = [
    { id: 'p3sm-id', name: 'P3SM' },
    { id: 'gapeknas-id', name: 'GAPEKNAS' },
];

export const initialKonstruksiKlasifikasiData: KlasifikasiData[] = [
    { id: 'konstruksi-umum-gedung', name: 'UMUM BANGUNAN GEDUNG', subKlasifikasi: ['Sub 1', 'Sub 2', 'Sub 3'] },
    { id: 'konstruksi-umum-sipil', name: 'UMUM BANGUNAN SIPIL', subKlasifikasi: ['Sub A', 'Sub B'] },
];

// --- P3SM Costs ---
export const initialP3SMKualifikasiData: BiayaData[] = [
    { id: 'p3sm-kual-1', name: 'Kecil (K) BUJKN', biaya: 315000 },
    { id: 'p3sm-kual-2', name: 'Menengah (M) BUJKN', biaya: 2257500 },
    { id: 'p3sm-kual-3', name: 'Besar (B) BUJKN', biaya: 9450000 },
];

export const initialP3SMBiayaSetorData: BiayaData[] = [
    { id: 'p3sm-bs-1', name: 'Kecil (K) BUJKN', biaya: 750000 },
    { id: 'p3sm-bs-2', name: 'Menengah (M) BUJKN', biaya: 2650000 },
    { id: 'p3sm-bs-3', name: 'Besar (B) BUJKN', biaya: 12000000 },
];

export const initialP3SMBiayaLainnyaData: BiayaData[] = [
    { id: 'p3sm-bl-1', name: 'Biaya Materai P3SM', biaya: 10000 },
    { id: 'p3sm-bl-2', name: 'Biaya Administrasi P3SM', biaya: 50000 },
];

// --- GAPEKNAS Costs ---
export const initialGapeknasKualifikasiData: BiayaData[] = [
    { id: 'gpk-kual-1', name: 'Kualifikasi GAPEKNAS 1', biaya: 500000 },
    { id: 'gpk-kual-2', name: 'Kualifikasi GAPEKNAS 2', biaya: 3000000 },
];

export const initialGapeknasBiayaSetorData: BiayaData[] = [
    { id: 'gpk-bs-1', name: 'Kualifikasi GAPEKNAS 1', biaya: 1000000 },
    { id: 'gpk-bs-2', name: 'Kualifikasi GAPEKNAS 2', biaya: 4000000 },
];

export const initialGapeknasBiayaLainnyaData: BiayaData[] = [
    { id: 'gpk-bl-1', name: 'Biaya Administrasi GAPEKNAS', biaya: 75000 },
];

// =====================================================
// SBU KONSULTAN DATA
// =====================================================
export const initialSbuKonsultanData: SbuData[] = [
    { id: 'inkindo-id', name: 'INKINDO' },
    { id: 'perkindo-id', name: 'PERKINDO' },
];

export const initialKonsultanKlasifikasiData: KlasifikasiData[] = [
    { id: 'konsultan-rekayasa', name: 'Layanan Jasa Inspeksi Rekayasa', subKlasifikasi: ['Sub Konsultan 1', 'Sub Konsultan 2'] },
    { id: 'konsultan-non-rekayasa', name: 'Layanan Jasa Inspeksi Non-Rekayasa', subKlasifikasi: ['Sub Konsultan A', 'Sub Konsultan B'] },
];

export const initialKonsultanKualifikasiData: BiayaData[] = [
    { id: 'konsultan-kual-1', name: 'Kualifikasi Konsultan Kecil', biaya: 400000 },
    { id: 'konsultan-kual-2', name: 'Kualifikasi Konsultan Menengah', biaya: 2500000 },
];

export const initialKonsultanBiayaSetorData: BiayaData[] = [
    { id: 'konsultan-bs-1', name: 'Kualifikasi Konsultan Kecil', biaya: 800000 },
    { id: 'konsultan-bs-2', name: 'Kualifikasi Konsultan Menengah', biaya: 3000000 },
];

export const initialKonsultanBiayaLainnyaData: BiayaData[] = [
    { id: 'konsultan-bl-1', name: 'Biaya Admin Konsultan', biaya: 60000 },
];

// =====================================================
// SKK KONSTRUKSI DATA
// =====================================================
export const initialSkkKonstruksiData: SbuData[] = [
    { id: 'lpjk-id', name: 'LPJK' },
];

export const initialSkkKlasifikasiData: KlasifikasiData[] = [
    { id: 'skk-ahli-utama', name: 'Ahli Utama', subKlasifikasi: ['Sub SKK 1', 'Sub SKK 2'], kualifikasi: ['Kualifikasi SKK A', 'Kualifikasi SKK B'], subBidang: ['Sub Bidang 1', 'Sub Bidang 2'] },
    { id: 'skk-ahli-madya', name: 'Ahli Madya', subKlasifikasi: ['Sub SKK A', 'Sub SKK B'], kualifikasi: [], subBidang: [] },
];

export const initialSkkKualifikasiData: BiayaData[] = [
    { id: 'skk-kual-1', name: 'Jenjang SKK 1', biaya: 600000 },
    { id: 'skk-kual-2', name: 'Jenjang SKK 2', biaya: 3500000 },
];

export const initialSkkBiayaSetorData: BiayaData[] = [
    { id: 'skk-bs-1', name: 'Jenjang SKK 1', biaya: 1200000 },
    { id: 'skk-bs-2', name: 'Jenjang SKK 2', biaya: 5000000 },
];

export const initialSkkBiayaLainnyaData: BiayaData[] = [
    { id: 'skk-bl-1', name: 'Biaya Admin SKK', biaya: 80000 },
];

// =====================================================
// SMAP / SIMPK / NOTARIS DATA
// =====================================================
export const initialSmapBiayaSetorData: BiayaData[] = [
    { id: 'smap-bs-1', name: 'Dokumen SMAP', biaya: 0 },
];

export const initialSimpkBiayaSetorData: BiayaData[] = [
    { id: 'simpk-bs-1', name: 'Akun SIMPK dan Alat', biaya: 0 },
];

export const initialNotarisBiayaSetorData: BiayaData[] = [
    { id: 'notaris-bs-1', name: 'Notaris', biaya: 0 },
];

export const initialNotarisKualifikasiData: BiayaData[] = [
    { id: 'notaris-kual-1', name: 'Kualifikasi Notaris 1', biaya: 500000 },
    { id: 'notaris-kual-2', name: 'Kualifikasi Notaris 2', biaya: 1000000 },
];

export const initialNotarisBiayaLainnyaData: BiayaData[] = [
    { id: 'notaris-bl-1', name: 'Biaya Admin Notaris', biaya: 100000 },
];

// =====================================================
// SAMPLE SUBMISSIONS
// =====================================================
export const initialUserSubmissions: Submission[] = [
    { id: 'sub-1', companyName: 'PT. Maju Bersama', marketingName: 'Hidayatullah', inputDate: '2025-08-01', submittedById: 'user-admin-1', certificateType: 'SBU Konstruksi', sbuType: 'konstruksi', selectedSub: { id: 'p3sm-id', name: 'P3SM' }, selectedKlasifikasi: { id: 'konstruksi-umum-gedung', name: 'UMUM BANGUNAN GEDUNG', subKlasifikasi: ['Sub 1', 'Sub 2', 'Sub 3'] }, selectedSubKlasifikasi: 'Sub 1', selectedKualifikasi: { id: 'p3sm-kual-1', name: 'Kecil (K) BUJKN', biaya: 315000 }, selectedBiayaLainnya: null, biayaSetorKantor: 750000, keuntungan: 435000 },
    { id: 'sub-2', companyName: 'CV. Jaya Abadi', marketingName: 'Nuzul Arifin', inputDate: '2025-08-03', submittedById: 'user-admin-1', certificateType: 'SBU Konstruksi', sbuType: 'konstruksi', selectedSub: { id: 'gapeknas-id', name: 'GAPEKNAS' }, selectedKlasifikasi: { id: 'konstruksi-umum-sipil', name: 'UMUM BANGUNAN SIPIL', subKlasifikasi: ['Sub A', 'Sub B'] }, selectedSubKlasifikasi: 'Sub A', selectedKualifikasi: { id: 'gpk-kual-1', name: 'Kualifikasi GAPEKNAS 1', biaya: 500000 }, selectedBiayaLainnya: null, biayaSetorKantor: 1000000, keuntungan: 500000 },
    { id: 'sub-3', companyName: 'PT. Manager Corp', marketingName: 'Abdul Fatahurahman', inputDate: '2025-08-05', submittedById: 'user-manager-1', certificateType: 'SBU Konstruksi', sbuType: 'konstruksi', selectedSub: { id: 'p3sm-id', name: 'P3SM' }, selectedKlasifikasi: { id: 'konstruksi-umum-gedung', name: 'UMUM BANGUNAN GEDUNG', subKlasifikasi: ['Sub 1', 'Sub 2', 'Sub 3'] }, selectedSubKlasifikasi: 'Sub 2', selectedKualifikasi: { id: 'p3sm-kual-2', name: 'Menengah (M) BUJKN', biaya: 2257500 }, selectedBiayaLainnya: null, biayaSetorKantor: 3000000, keuntungan: 742500 },
    { id: 'sub-4', companyName: 'CV. Karyawan Sejahtera', marketingName: 'Nuzul Arifin', inputDate: '2025-08-06', submittedById: 'user-karyawan-1', certificateType: 'Dokumen SMAP', sbuType: 'smap', selectedSub: null, selectedKlasifikasi: null, selectedSubKlasifikasi: null, selectedKualifikasi: { id: 'smap-kual-1', name: 'SMAP Standar', biaya: 200000 }, selectedBiayaLainnya: null, biayaSetorKantor: 400000, keuntungan: 200000 },
    { id: 'sub-5', companyName: 'PT. Konsultan Hebat', marketingName: 'Hidayatullah', inputDate: '2025-08-08', submittedById: 'user-admin-1', certificateType: 'SBU Konsultan', sbuType: 'konsultan', selectedSub: { id: 'inkindo-id', name: 'INKINDO' }, selectedKlasifikasi: { id: 'konsultan-rekayasa', name: 'Layanan Jasa Inspeksi Rekayasa', subKlasifikasi: ['Sub Konsultan 1', 'Sub Konsultan 2'] }, selectedSubKlasifikasi: 'Sub Konsultan 1', selectedKualifikasi: { id: 'konsultan-kual-1', name: 'Kualifikasi Konsultan Kecil', biaya: 400000 }, selectedBiayaLainnya: null, biayaSetorKantor: 800000, keuntungan: 400000 },
    { id: 'sub-6', companyName: 'PT. Marketing Solutions', marketingName: 'Hidayatullah', inputDate: '2025-08-09', submittedById: 'user-marketing-1', certificateType: 'Akun SIMPK dan Alat', sbuType: 'simpk', selectedSub: null, selectedKlasifikasi: null, selectedSubKlasifikasi: null, selectedKualifikasi: { id: 'simpk-kual-1', name: 'SIMPK Basic', biaya: 150000 }, selectedBiayaLainnya: null, biayaSetorKantor: 300000, keuntungan: 150000 },
    { id: 'sub-7', companyName: 'CV. Mitra Terpercaya', marketingName: 'Abdul Fatahurahman', inputDate: '2025-08-10', submittedById: 'user-mitra-1', certificateType: 'Notaris', sbuType: 'notaris', selectedSub: null, selectedKlasifikasi: null, selectedSubKlasifikasi: null, selectedKualifikasi: { id: 'notaris-kual-1', name: 'Kualifikasi Notaris 1', biaya: 500000 }, selectedBiayaLainnya: null, biayaSetorKantor: 750000, keuntungan: 250000 },
];

// =====================================================
// SAMPLE TRANSACTIONS
// =====================================================
export const initialTransactions: Transaction[] = [
    { id: 'tran-1', transactionDate: '2025-08-05', transactionName: 'kas operasional', cost: 25000, transactionType: 'Keluar', submittedById: 'user-admin-1', proof: 'bukti-kas.pdf' },
    { id: 'tran-2', transactionDate: '2025-08-01', transactionName: 'tabungan ruko', cost: 5000000, transactionType: 'Tabungan', submittedById: 'user-admin-1', proof: null },
    { id: 'tran-3', transactionDate: '2025-08-02', transactionName: 'Beli ATK', cost: 150000, transactionType: 'Keluar', submittedById: 'user-admin-1', proof: null },
];

// =====================================================
// FEE P3SM (empty initial)
// =====================================================
export const initialFeeP3SMData: FeeP3SM[] = [];
