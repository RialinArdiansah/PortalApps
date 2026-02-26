// =====================================================
// Business Calculation Utilities
// =====================================================

import type { Submission, Transaction, FeeP3SM, DashboardFilter, DashboardSummary } from '@/types';

/**
 * Calculate keuntungan (profit) for a submission
 * Formula: biayaSetorKantor - biayaKualifikasi - (biayaLainnya || 0)
 */
export const calculateKeuntungan = (
    biayaSetorKantor: number,
    biayaKualifikasi: number,
    biayaLainnya: number = 0
): number => {
    return biayaSetorKantor - biayaKualifikasi - biayaLainnya;
};

/**
 * Filter submissions by date range
 */
export const filterByDateRange = <T>(
    items: T[],
    dateField: keyof T,
    filter: DashboardFilter
): T[] => {
    if (filter.type === 'all') return items;

    const now = new Date();
    let startDate: Date;
    let endDate: Date;

    if (filter.type === 'range') {
        startDate = new Date(filter.year, filter.month - 1, 1);
        endDate = new Date(filter.year, filter.month, 1);
    } else {
        endDate = new Date(now.getFullYear(), now.getMonth() + 1, 1);
        switch (filter.type) {
            case 'last3':
                startDate = new Date(now.getFullYear(), now.getMonth() - 2, 1);
                break;
            case 'last6':
                startDate = new Date(now.getFullYear(), now.getMonth() - 5, 1);
                break;
            case 'last12':
                startDate = new Date(now.getFullYear() - 1, now.getMonth() + 1, 1);
                break;
            default:
                return items;
        }
    }

    return items.filter((item) => {
        const dateValue = item[dateField];
        if (!dateValue || typeof dateValue !== 'string') return false;
        const itemDate = new Date(dateValue as string);
        return itemDate >= startDate && itemDate < endDate;
    });
};

/**
 * Filter FeeP3SM by date range
 */
export const filterFeeP3SM = (fees: FeeP3SM[], filter: DashboardFilter): FeeP3SM[] => {
    if (filter.type === 'all') return fees;

    const now = new Date();
    let startDate: Date;
    let endDate: Date;

    if (filter.type === 'range') {
        startDate = new Date(filter.year, filter.month - 1, 1);
        endDate = new Date(filter.year, filter.month, 1);
    } else {
        endDate = new Date(now.getFullYear(), now.getMonth() + 1, 1);
        switch (filter.type) {
            case 'last3':
                startDate = new Date(now.getFullYear(), now.getMonth() - 2, 1);
                break;
            case 'last6':
                startDate = new Date(now.getFullYear(), now.getMonth() - 5, 1);
                break;
            case 'last12':
                startDate = new Date(now.getFullYear() - 1, now.getMonth() + 1, 1);
                break;
            default:
                return fees;
        }
    }

    return fees.filter((f) => {
        if (!f.month || !f.year) return false;
        const feeDate = new Date(f.year, f.month - 1, 1);
        return feeDate >= startDate && feeDate < endDate;
    });
};

/**
 * Calculate dashboard summary statistics
 */
export const calculateDashboardSummary = (
    submissions: Submission[],
    transactions: Transaction[],
    feeP3SM: FeeP3SM[]
): DashboardSummary => {
    const feeTotal = feeP3SM.reduce((acc, fee) => acc + (fee.cost || 0), 0);
    const totalKeuntungan = submissions.reduce((acc, sub) => acc + (sub.keuntungan || 0), 0) + feeTotal;
    const totalPemasukan = submissions.reduce((acc, sub) => acc + (sub.biayaSetorKantor || 0), 0);
    const totalSertifikat = submissions.length;
    const totalPengeluaran = transactions
        .filter((t) => t.transactionType === 'Keluar')
        .reduce((acc, t) => acc + (t.cost || 0), 0);
    const totalTabungan = transactions
        .filter((t) => t.transactionType === 'Tabungan')
        .reduce((acc, t) => acc + (t.cost || 0), 0);

    return {
        totalKeuntungan,
        totalPemasukan,
        totalSertifikat,
        totalPengeluaran,
        totalTabungan,
    };
};

/**
 * Group submissions by marketing name for donut chart
 */
export const groupByMarketing = (submissions: Submission[]): Record<string, number> => {
    return submissions.reduce((acc, sub) => {
        const name = sub.marketingName || 'Tidak Diketahui';
        acc[name] = (acc[name] || 0) + 1;
        return acc;
    }, {} as Record<string, number>);
};

/**
 * Calculate monthly profit data for line chart
 */
export const calculateMonthlyProfits = (
    submissions: Submission[]
): Array<{ month: string; keuntungan: number }> => {
    const monthlyMap = new Map<string, number>();

    submissions.forEach((sub) => {
        if (!sub.inputDate) return;
        const date = new Date(sub.inputDate);
        const key = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
        monthlyMap.set(key, (monthlyMap.get(key) || 0) + (sub.keuntungan || 0));
    });

    return Array.from(monthlyMap.entries())
        .sort(([a], [b]) => a.localeCompare(b))
        .map(([month, keuntungan]) => ({ month, keuntungan }));
};

/**
 * Calculate marketing ranking
 */
export const calculateMarketingRanking = (
    submissions: Submission[]
): Array<{ name: string; count: number; totalKeuntungan: number }> => {
    const rankMap = new Map<string, { count: number; totalKeuntungan: number }>();

    submissions.forEach((sub) => {
        const name = sub.marketingName;
        if (!name) return;
        const existing = rankMap.get(name) || { count: 0, totalKeuntungan: 0 };
        existing.count += 1;
        existing.totalKeuntungan += sub.keuntungan || 0;
        rankMap.set(name, existing);
    });

    return Array.from(rankMap.entries())
        .map(([name, data]) => ({ name, ...data }))
        .sort((a, b) => b.count - a.count);
};

// =====================================================
// Certificate Analytics
// =====================================================

/**
 * Group submissions by certificate type for analytics
 */
export const groupByCertificateType = (
    submissions: Submission[]
): Array<{ name: string; count: number; revenue: number; keuntungan: number }> => {
    const map = new Map<string, { count: number; revenue: number; keuntungan: number }>();

    submissions.forEach((sub) => {
        const name = sub.certificateType || 'Lainnya';
        const existing = map.get(name) || { count: 0, revenue: 0, keuntungan: 0 };
        existing.count += 1;
        existing.revenue += sub.biayaSetorKantor || 0;
        existing.keuntungan += sub.keuntungan || 0;
        map.set(name, existing);
    });

    return Array.from(map.entries())
        .map(([name, data]) => ({ name, ...data }))
        .sort((a, b) => b.revenue - a.revenue);
};

/**
 * Calculate monthly revenue vs expenses for composed chart
 */
export const calculateMonthlyRevenueVsExpenses = (
    submissions: Submission[],
    transactions: Transaction[]
): Array<{ month: string; pemasukan: number; pengeluaran: number; keuntungan: number }> => {
    const monthlyMap = new Map<string, { pemasukan: number; pengeluaran: number; keuntungan: number }>();

    submissions.forEach((sub) => {
        if (!sub.inputDate) return;
        const date = new Date(sub.inputDate);
        const key = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
        const existing = monthlyMap.get(key) || { pemasukan: 0, pengeluaran: 0, keuntungan: 0 };
        existing.pemasukan += sub.biayaSetorKantor || 0;
        existing.keuntungan += sub.keuntungan || 0;
        monthlyMap.set(key, existing);
    });

    transactions.forEach((t) => {
        if (!t.transactionDate || t.transactionType !== 'Keluar') return;
        const date = new Date(t.transactionDate);
        const key = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
        const existing = monthlyMap.get(key) || { pemasukan: 0, pengeluaran: 0, keuntungan: 0 };
        existing.pengeluaran += t.cost || 0;
        monthlyMap.set(key, existing);
    });

    return Array.from(monthlyMap.entries())
        .sort(([a], [b]) => a.localeCompare(b))
        .map(([month, data]) => ({ month, ...data }));
};

/**
 * Calculate financial KPIs
 */
export const calculateFinancialKPIs = (
    submissions: Submission[],
    transactions: Transaction[]
): {
    netMarginPct: number;
    avgRevenuePerCert: number;
    topRevenueSource: string;
    totalCertTypes: number;
    highestEarningType: string;
    highestEarningRevenue: number;
} => {
    const totalRevenue = submissions.reduce((acc, s) => acc + (s.biayaSetorKantor || 0), 0);
    const totalProfit = submissions.reduce((acc, s) => acc + (s.keuntungan || 0), 0);
    const totalExpenses = transactions
        .filter((t) => t.transactionType === 'Keluar')
        .reduce((acc, t) => acc + (t.cost || 0), 0);

    const netMarginPct = totalRevenue > 0 ? ((totalRevenue - totalExpenses) / totalRevenue) * 100 : 0;
    const avgRevenuePerCert = submissions.length > 0 ? totalRevenue / submissions.length : 0;

    // Group by cert type to find top revenue source and highest earning type
    const typeRevenue = groupByCertificateType(submissions);
    const topType = typeRevenue[0];

    return {
        netMarginPct: Math.round(netMarginPct * 10) / 10,
        avgRevenuePerCert,
        topRevenueSource: topType?.name || '-',
        totalCertTypes: new Set(submissions.map((s) => s.certificateType).filter(Boolean)).size,
        highestEarningType: topType?.name || '-',
        highestEarningRevenue: topType?.revenue || 0,
    };
};

