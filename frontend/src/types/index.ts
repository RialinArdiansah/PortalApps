// =====================================================
// PortalApp_Keu — TypeScript Type Definitions
// All types match legacy data structures exactly
// =====================================================

// --- Roles ---
export type UserRole = 'Super admin' | 'admin' | 'manager' | 'karyawan' | 'marketing' | 'mitra';

export const ROLE_HIERARCHY: UserRole[] = ['Super admin', 'admin', 'manager', 'karyawan', 'marketing', 'mitra'];

// --- User ---
export interface User {
    id: string;
    fullName: string;
    username: string;
    email: string;
    password: string; // base64 encoded
    role: UserRole;
}

// --- Certificate ---
export interface Certificate {
    id: string;
    name: string;
    subMenus: string[];
}

// --- Marketing ---
export interface MarketingName {
    id: string;
    name: string;
}

// --- SBU / Asosiasi Data ---
export interface SbuData {
    id: string;
    name: string;
    subKlasifikasi?: string[];
}

// --- Klasifikasi ---
export interface KlasifikasiData {
    id: string;
    name: string;
    subKlasifikasi: string[];
    kualifikasi?: string[];
    subBidang?: string[];
}

// --- Kualifikasi / Biaya ---
export interface BiayaData {
    id: string;
    name: string;
    biaya: number;
}

// --- SBU Type Union ---
export type SbuType = 'konstruksi' | 'skk' | 'konsultan' | 'smap' | 'simpk' | 'notaris';

// --- Submission ---
export interface Submission {
    id: string;
    companyName: string;
    marketingName: string;
    inputDate: string; // YYYY-MM-DD
    submittedById: string;
    certificateType: string;
    sbuType: SbuType;
    selectedSub: SbuData | null;
    selectedKlasifikasi: KlasifikasiData | null;
    selectedSubKlasifikasi: string | null;
    selectedKualifikasi: BiayaData | null;
    selectedBiayaLainnya: BiayaData | null;
    biayaSetorKantor: number;
    keuntungan: number;
}

// --- Transaction ---
export type TransactionType = 'Keluar' | 'Tabungan' | 'Kas';

export interface Transaction {
    id: string;
    transactionDate: string; // YYYY-MM-DD
    transactionName: string;
    cost: number;
    transactionType: TransactionType;
    submittedById: string;
    proof: string | null;
}

// --- Fee P3SM ---
export interface FeeP3SM {
    id: string;
    cost: number;
    month: number;
    year: number;
}

// --- Dashboard Filter ---
export type DashboardFilterType = 'all' | 'range' | 'last3' | 'last6' | 'last12';

export interface DashboardFilter {
    type: DashboardFilterType;
    month: number;
    year: number;
}

// --- Dashboard Summary ---
export interface DashboardSummary {
    totalKeuntungan: number;
    totalPemasukan: number;
    totalSertifikat: number;
    totalPengeluaran: number;
    totalTabungan: number;
}

// --- Pagination ---
export interface PaginationState {
    currentPage: number;
    itemsPerPage: number;
    searchTerm: string;
}

// --- Admin Titles ---
export interface AdminTitles {
    users: string;
    certificates: string;
    marketing: string;
    submissions: string;
    mySubmissions: string;
    transactions: string;
    financialReport: string;
}

// --- API Response ---
export interface ApiResponse<T> {
    data: T;
    message?: string;
    success: boolean;
}

// --- Login Credentials ---
export interface LoginCredentials {
    username: string;
    password: string;
}

// --- Auth State ---
export interface AuthState {
    user: User | null;
    token: string | null;
    status: 'idle' | 'loading' | 'succeeded' | 'failed';
    error: string | null;
}

// --- Pagination Options ---
export const ITEMS_PER_PAGE_OPTIONS = [10, 20, 30, 40, 50, 100] as const;
export const DEFAULT_ITEMS_PER_PAGE = 10;
export const MAX_VISIBLE_PAGES = 5;
