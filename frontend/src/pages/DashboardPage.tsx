import { useEffect, useMemo } from 'react';
import { useAppSelector, useAppDispatch } from '@/app/hooks';
import { fetchSubmissions } from '@/features/submissions/submissionsSlice';
import { fetchTransactions } from '@/features/transactions/transactionsSlice';
import { setFilter, setActiveTab } from '@/features/dashboard/dashboardSlice';
import { formatCurrency, getGreeting, MONTH_NAMES } from '@/utils/formatters';
import {
    calculateDashboardSummary, filterByDateRange, groupByMarketing,
    calculateMonthlyProfits, calculateMarketingRanking,
    groupByCertificateType, calculateMonthlyRevenueVsExpenses, calculateFinancialKPIs,
} from '@/utils/calculations';
import type { DashboardFilterType } from '@/types';
import {
    AreaChart, Area, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer,
    PieChart, Pie, Cell, Legend, BarChart, Bar, ComposedChart, Line,
    RadialBarChart, RadialBar,
} from 'recharts';

const CHART_COLORS = ['#6366f1', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'];
const GRADIENT_PAIRS = [
    ['#6366f1', '#818cf8'],
    ['#22c55e', '#4ade80'],
    ['#f59e0b', '#fbbf24'],
    ['#ef4444', '#f87171'],
    ['#8b5cf6', '#a78bfa'],
    ['#ec4899', '#f472b6'],
    ['#14b8a6', '#2dd4bf'],
    ['#f97316', '#fb923c'],
];

// Custom tooltip for charts
const CustomTooltip = ({ active, payload, label, formatter }: any) => {
    if (!active || !payload?.length) return null;
    return (
        <div className="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 min-w-[180px]">
            <p className="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">{label}</p>
            {payload.map((entry: any, i: number) => (
                <div key={i} className="flex items-center gap-2 text-sm mb-1">
                    <div className="w-3 h-3 rounded-full" style={{ backgroundColor: entry.color }} />
                    <span className="text-slate-500 dark:text-slate-400">{entry.name}:</span>
                    <span className="font-bold text-slate-800 dark:text-white">
                        {formatter ? formatter(entry.value) : formatCurrency(entry.value)}
                    </span>
                </div>
            ))}
        </div>
    );
};

export const DashboardPage = () => {
    const dispatch = useAppDispatch();
    const { user } = useAppSelector((s) => s.auth);
    const { list: submissions } = useAppSelector((s) => s.submissions);
    const { list: transactions } = useAppSelector((s) => s.transactions);
    const { filter, activeTab } = useAppSelector((s) => s.dashboard);

    useEffect(() => {
        dispatch(fetchSubmissions());
        dispatch(fetchTransactions());
    }, [dispatch]);

    const greeting = getGreeting();

    const isAllView = user?.role === 'Super admin' || user?.role === 'admin' || user?.role === 'manager';
    const userSubs = isAllView ? submissions : submissions.filter((s) => s.submittedById === user?.id);
    const userTrans = isAllView ? transactions : transactions.filter((t) => t.submittedById === user?.id);

    const filteredSubs = useMemo(() => filterByDateRange(userSubs, 'inputDate', filter), [userSubs, filter]);
    const filteredTrans = useMemo(() => filterByDateRange(userTrans, 'transactionDate', filter), [userTrans, filter]);

    const summary = useMemo(() => calculateDashboardSummary(filteredSubs, filteredTrans, []), [filteredSubs, filteredTrans]);
    const monthlyProfits = useMemo(() => calculateMonthlyProfits(filteredSubs), [filteredSubs]);
    const marketingDist = useMemo(() => groupByMarketing(filteredSubs), [filteredSubs]);
    const ranking = useMemo(() => calculateMarketingRanking(filteredSubs), [filteredSubs]);

    // Analytics data
    const certByType = useMemo(() => groupByCertificateType(filteredSubs), [filteredSubs]);
    const monthlyRevExp = useMemo(() => calculateMonthlyRevenueVsExpenses(filteredSubs, filteredTrans), [filteredSubs, filteredTrans]);
    const financialKPIs = useMemo(() => calculateFinancialKPIs(filteredSubs, filteredTrans), [filteredSubs, filteredTrans]);

    // Radial bar data (top 5 cert types by revenue)
    const radialData = useMemo(() =>
        certByType.slice(0, 5).map((d, i) => ({
            ...d,
            fill: CHART_COLORS[i % CHART_COLORS.length],
        })), [certByType]);

    const donutData = Object.entries(marketingDist).map(([name, value]) => ({ name, value }));

    const handleFilterType = (type: DashboardFilterType) => {
        dispatch(setFilter({ type }));
    };

    const StatCard = ({ title, value, icon, colorClass, gradientClass }: { title: string, value: string, icon: string, colorClass: string, gradientClass: string }) => (
        <div className="relative overflow-hidden bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 hover:shadow-lg transition-all duration-300 group">
            <div className={`absolute top-0 right-0 w-24 h-24 ${gradientClass} opacity-10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110`} />
            <div className="p-4 sm:p-6 relative z-10">
                <div className="flex justify-between items-start mb-4">
                    <div className={`p-3 rounded-xl ${colorClass} bg-opacity-10 dark:bg-opacity-20 text-xl`}>
                        {icon}
                    </div>
                </div>
                <div>
                    <h3 className="text-slate-500 dark:text-slate-400 text-xs sm:text-sm font-medium mb-1">{title}</h3>
                    <p className="text-lg sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">{value}</p>
                </div>
            </div>
        </div>
    );

    const MiniStatCard = ({ title, value, icon, color }: { title: string; value: string; icon: string; color: string }) => (
        <div className="bg-slate-50 dark:bg-slate-700/50 rounded-2xl p-4 hover:bg-white dark:hover:bg-slate-700 transition-all border border-transparent hover:border-slate-200 dark:hover:border-slate-600 hover:shadow-md">
            <div className="flex items-center gap-3 mb-2">
                <span className={`text-lg p-2 rounded-xl bg-opacity-10 dark:bg-opacity-20 ${color}`}>{icon}</span>
                <span className="text-xs text-slate-500 dark:text-slate-400 font-medium">{title}</span>
            </div>
            <p className="text-xl font-bold text-slate-800 dark:text-white">{value}</p>
        </div>
    );

    const KPIBadge = ({ label, value, color }: { label: string; value: string; color: string }) => (
        <div className={`flex items-center gap-3 px-5 py-3 rounded-2xl bg-opacity-5 dark:bg-opacity-10 border backdrop-blur-sm ${color}`}>
            <span className="text-sm text-slate-500 dark:text-slate-400 font-medium">{label}</span>
            <span className="text-lg font-bold text-slate-800 dark:text-white">{value}</span>
        </div>
    );

    const TabButton = ({ label, id }: { label: string, id: typeof activeTab }) => (
        <button
            onClick={() => dispatch(setActiveTab(id))}
            className={`px-4 sm:px-6 py-2.5 text-sm font-medium rounded-full transition-all duration-200 whitespace-nowrap ${activeTab === id
                ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200 dark:shadow-indigo-900/50'
                : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-600'
                }`}
        >
            {label}
        </button>
    );

    return (
        <div className="space-y-6 sm:space-y-8 font-inter">
            {/* Hero Section */}
            <div className="relative bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl sm:rounded-3xl p-6 sm:p-10 text-white shadow-xl overflow-hidden">
                <div className="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/4 pointer-events-none" />
                <div className="absolute bottom-0 left-0 w-48 h-48 bg-indigo-400 opacity-10 rounded-full blur-2xl translate-y-1/3 -translate-x-1/4 pointer-events-none" />

                <div className="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 sm:gap-6">
                    <div>
                        <div className="flex items-center gap-3 mb-2">
                            <span className="text-2xl sm:text-3xl animate-bounce-slow">{greeting.icon}</span>
                            <h1 className="text-xl sm:text-3xl font-bold tracking-tight">
                                {greeting.text}, {user?.fullName || 'Admin'}!
                            </h1>
                        </div>
                        <p className="text-indigo-100 text-sm sm:text-lg opacity-90 max-w-lg">
                            Selamat datang di dashboard operasional Sulthan Group.
                        </p>
                    </div>
                </div>
            </div>

            {/* Filter Section */}
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-slate-800 p-3 sm:p-4 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 transition-colors">
                <div className="flex items-center gap-2 sm:gap-3">
                    <select
                        value={filter.month}
                        onChange={(e) => dispatch(setFilter({ type: 'range', month: Number(e.target.value) }))}
                        className="bg-slate-50 dark:bg-slate-700 border-none text-slate-700 dark:text-slate-200 text-sm font-semibold rounded-xl focus:ring-2 focus:ring-indigo-500 py-2.5 px-3 sm:px-4 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors"
                    >
                        {MONTH_NAMES.map((name, i) => (
                            <option key={i} value={i + 1}>{name}</option>
                        ))}
                    </select>
                    <input
                        type="number"
                        value={filter.year}
                        onChange={(e) => dispatch(setFilter({ type: 'range', year: Number(e.target.value) }))}
                        className="bg-slate-50 dark:bg-slate-700 border-none text-slate-700 dark:text-slate-200 text-sm font-semibold rounded-xl focus:ring-2 focus:ring-indigo-500 py-2.5 px-3 sm:px-4 w-20 sm:w-24 hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors"
                    />
                </div>

                <div className="bg-slate-50 dark:bg-slate-700 p-1 rounded-xl flex gap-1 overflow-x-auto w-full sm:w-auto">
                    {(['last3', 'last6', 'last12', 'all'] as DashboardFilterType[]).map((f) => (
                        <button
                            key={f}
                            onClick={() => handleFilterType(f)}
                            className={`px-3 sm:px-4 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 whitespace-nowrap ${filter.type === f
                                ? 'bg-white dark:bg-slate-600 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'
                                }`}
                        >
                            {f === 'last3' ? '3 Bln' : f === 'last6' ? '6 Bln' : f === 'last12' ? '1 Thn' : 'Semua'}
                        </button>
                    ))}
                </div>
            </div>

            {/* Stats Grid */}
            <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-5">
                <StatCard title="Total Keuntungan" value={formatCurrency(summary.totalKeuntungan)} icon="💰" colorClass="text-emerald-600 bg-emerald-500" gradientClass="bg-gradient-to-br from-emerald-400 to-emerald-600" />
                <StatCard title="Total Pemasukan" value={formatCurrency(summary.totalPemasukan)} icon="📥" colorClass="text-blue-600 bg-blue-500" gradientClass="bg-gradient-to-br from-blue-400 to-blue-600" />
                <StatCard title="Total Sertifikat" value={String(summary.totalSertifikat)} icon="📜" colorClass="text-violet-600 bg-violet-500" gradientClass="bg-gradient-to-br from-violet-400 to-violet-600" />
                <StatCard title="Total Pengeluaran" value={formatCurrency(summary.totalPengeluaran)} icon="📤" colorClass="text-rose-600 bg-rose-500" gradientClass="bg-gradient-to-br from-rose-400 to-rose-600" />
                <StatCard title="Total Tabungan" value={formatCurrency(summary.totalTabungan)} icon="🏦" colorClass="text-amber-600 bg-amber-500" gradientClass="bg-gradient-to-br from-amber-400 to-amber-600" />
            </div>

            {/* Content Tabs */}
            <div className="flex gap-3 border-b border-slate-200 dark:border-slate-700 pb-1 overflow-x-auto">
                <TabButton label="📊 Ringkasan" id="ringkasan" />
                <TabButton label="📜 Analisis Sertifikat" id="analisisSertifikat" />
                <TabButton label="💹 Laporan Keuangan" id="laporanKeuangan" />
                <TabButton label="🏆 Pencapaian Marketing" id="pencapaianMarketing" />
            </div>

            {/* ========== TAB: Ringkasan Kinerja ========== */}
            {activeTab === 'ringkasan' && (
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 animate-fade-in-up">
                    <div className="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-100 dark:border-slate-700 transition-colors">
                        <div className="flex justify-between items-center mb-6">
                            <h3 className="text-base sm:text-lg font-bold text-slate-800 dark:text-white">Tren Keuntungan Bulanan</h3>
                        </div>
                        <div className="h-[250px] sm:h-[350px] w-full">
                            <ResponsiveContainer width="100%" height="100%">
                                <AreaChart data={monthlyProfits}>
                                    <defs>
                                        <linearGradient id="colorProfit" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="5%" stopColor="#6366f1" stopOpacity={0.2} />
                                            <stop offset="95%" stopColor="#6366f1" stopOpacity={0} />
                                        </linearGradient>
                                    </defs>
                                    <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f1f5f9" className="dark:opacity-20" />
                                    <XAxis dataKey="month" axisLine={false} tickLine={false} tick={{ fill: '#64748b', fontSize: 12 }} dy={10} />
                                    <YAxis axisLine={false} tickLine={false} tick={{ fill: '#64748b', fontSize: 12 }} tickFormatter={(v) => `${(v / 1000000).toFixed(0)}jt`} />
                                    <Tooltip content={<CustomTooltip />} />
                                    <Area type="monotone" dataKey="keuntungan" name="Keuntungan" stroke="#6366f1" strokeWidth={3} fillOpacity={1} fill="url(#colorProfit)" activeDot={{ r: 6, strokeWidth: 0, fill: '#4f46e5' }} />
                                </AreaChart>
                            </ResponsiveContainer>
                        </div>
                    </div>

                    <div className="bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col transition-colors">
                        <h3 className="text-base sm:text-lg font-bold text-slate-800 dark:text-white mb-2">Distribusi Marketing</h3>
                        <p className="text-slate-500 dark:text-slate-400 text-sm mb-6">Persentase kontribusi sertifikat</p>
                        <div className="flex-1 min-h-[250px] sm:min-h-[300px]">
                            <ResponsiveContainer width="100%" height="100%">
                                <PieChart>
                                    <Pie data={donutData} cx="50%" cy="50%" innerRadius={60} outerRadius={90} paddingAngle={4} dataKey="value" cornerRadius={4}>
                                        {donutData.map((_, i) => (
                                            <Cell key={i} fill={CHART_COLORS[i % CHART_COLORS.length]} stroke="none" />
                                        ))}
                                    </Pie>
                                    <Legend layout="horizontal" verticalAlign="bottom" align="center" iconType="circle" wrapperStyle={{ fontSize: '12px', paddingTop: '20px' }} />
                                    <Tooltip content={<CustomTooltip formatter={(v: number) => `${v} sertifikat`} />} />
                                </PieChart>
                            </ResponsiveContainer>
                        </div>
                    </div>
                </div>
            )}

            {/* ========== TAB: Analisis Sertifikat ========== */}
            {activeTab === 'analisisSertifikat' && (
                <div className="space-y-6 sm:space-y-8 animate-fade-in-up">
                    {/* Mini Stats Row */}
                    <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <MiniStatCard title="Total Diproses" value={String(filteredSubs.length)} icon="📋" color="bg-indigo-500 text-indigo-600" />
                        <MiniStatCard title="Rata-rata Revenue" value={formatCurrency(financialKPIs.avgRevenuePerCert)} icon="📊" color="bg-emerald-500 text-emerald-600" />
                        <MiniStatCard title="Tipe Tertinggi" value={financialKPIs.highestEarningType} icon="🏆" color="bg-amber-500 text-amber-600" />
                        <MiniStatCard title="Jenis Sertifikat" value={String(financialKPIs.totalCertTypes)} icon="🗂️" color="bg-violet-500 text-violet-600" />
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-5 gap-6 sm:gap-8">
                        {/* Bar Chart: Certificate Distribution */}
                        <div className="lg:col-span-3 bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-100 dark:border-slate-700">
                            <h3 className="text-base sm:text-lg font-bold text-slate-800 dark:text-white mb-2">Distribusi Sertifikat per Tipe</h3>
                            <p className="text-slate-500 dark:text-slate-400 text-sm mb-6">Jumlah sertifikat yang diproses berdasarkan kategori</p>
                            <div className="h-[300px] sm:h-[400px] w-full">
                                <ResponsiveContainer width="100%" height="100%">
                                    <BarChart data={certByType} layout="vertical" margin={{ left: 20, right: 20 }}>
                                        <defs>
                                            {certByType.map((_, i) => (
                                                <linearGradient key={i} id={`barGrad${i}`} x1="0" y1="0" x2="1" y2="0">
                                                    <stop offset="0%" stopColor={GRADIENT_PAIRS[i % GRADIENT_PAIRS.length][0]} />
                                                    <stop offset="100%" stopColor={GRADIENT_PAIRS[i % GRADIENT_PAIRS.length][1]} />
                                                </linearGradient>
                                            ))}
                                        </defs>
                                        <CartesianGrid strokeDasharray="3 3" horizontal={false} stroke="#f1f5f9" className="dark:opacity-20" />
                                        <XAxis type="number" axisLine={false} tickLine={false} tick={{ fill: '#64748b', fontSize: 12 }} />
                                        <YAxis type="category" dataKey="name" axisLine={false} tickLine={false} tick={{ fill: '#64748b', fontSize: 11 }} width={130} />
                                        <Tooltip content={<CustomTooltip formatter={(v: number) => `${v} sertifikat`} />} />
                                        <Bar dataKey="count" name="Jumlah" radius={[0, 8, 8, 0]} barSize={22}>
                                            {certByType.map((_, i) => (
                                                <Cell key={i} fill={`url(#barGrad${i})`} />
                                            ))}
                                        </Bar>
                                    </BarChart>
                                </ResponsiveContainer>
                            </div>
                        </div>

                        {/* Radial Bar: Top 5 by Revenue */}
                        <div className="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-100 dark:border-slate-700">
                            <h3 className="text-base sm:text-lg font-bold text-slate-800 dark:text-white mb-2">Top 5 Revenue</h3>
                            <p className="text-slate-500 dark:text-slate-400 text-sm mb-4">Sertifikat dengan pendapatan tertinggi</p>
                            <div className="h-[280px] sm:h-[320px] w-full">
                                <ResponsiveContainer width="100%" height="100%">
                                    <RadialBarChart cx="50%" cy="50%" innerRadius="20%" outerRadius="90%" data={radialData} startAngle={180} endAngle={0}>
                                        <RadialBar dataKey="revenue" cornerRadius={6} background={{ fill: '#f1f5f9' }} />
                                        <Tooltip content={<CustomTooltip />} />
                                    </RadialBarChart>
                                </ResponsiveContainer>
                            </div>
                            {/* Legend for radial */}
                            <div className="space-y-2 mt-2">
                                {radialData.map((d, i) => (
                                    <div key={i} className="flex items-center justify-between text-sm">
                                        <div className="flex items-center gap-2">
                                            <div className="w-3 h-3 rounded-full" style={{ backgroundColor: d.fill }} />
                                            <span className="text-slate-600 dark:text-slate-300 truncate max-w-[150px]">{d.name}</span>
                                        </div>
                                        <span className="font-semibold text-slate-800 dark:text-white">{formatCurrency(d.revenue)}</span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Revenue by Type Horizontal Bar */}
                    <div className="bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-100 dark:border-slate-700">
                        <h3 className="text-base sm:text-lg font-bold text-slate-800 dark:text-white mb-2">Keuntungan per Tipe Sertifikat</h3>
                        <p className="text-slate-500 dark:text-slate-400 text-sm mb-6">Perbandingan keuntungan yang dihasilkan tiap tipe sertifikat</p>
                        <div className="h-[250px] sm:h-[300px] w-full">
                            <ResponsiveContainer width="100%" height="100%">
                                <BarChart data={certByType} margin={{ left: 20 }}>
                                    <defs>
                                        <linearGradient id="keuntunganGrad" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stopColor="#22c55e" stopOpacity={0.9} />
                                            <stop offset="100%" stopColor="#16a34a" stopOpacity={0.6} />
                                        </linearGradient>
                                        <linearGradient id="revenueGrad" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stopColor="#6366f1" stopOpacity={0.9} />
                                            <stop offset="100%" stopColor="#4f46e5" stopOpacity={0.6} />
                                        </linearGradient>
                                    </defs>
                                    <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f1f5f9" className="dark:opacity-20" />
                                    <XAxis dataKey="name" axisLine={false} tickLine={false} tick={{ fill: '#64748b', fontSize: 11 }} angle={-20} textAnchor="end" height={60} />
                                    <YAxis axisLine={false} tickLine={false} tick={{ fill: '#64748b', fontSize: 12 }} tickFormatter={(v) => `${(v / 1000000).toFixed(0)}jt`} />
                                    <Tooltip content={<CustomTooltip />} />
                                    <Legend iconType="circle" wrapperStyle={{ fontSize: '13px', paddingTop: '10px' }} />
                                    <Bar dataKey="revenue" name="Pemasukan" fill="url(#revenueGrad)" radius={[6, 6, 0, 0]} barSize={24} />
                                    <Bar dataKey="keuntungan" name="Keuntungan" fill="url(#keuntunganGrad)" radius={[6, 6, 0, 0]} barSize={24} />
                                </BarChart>
                            </ResponsiveContainer>
                        </div>
                    </div>
                </div>
            )}

            {/* ========== TAB: Laporan Keuangan ========== */}
            {activeTab === 'laporanKeuangan' && (
                <div className="space-y-6 sm:space-y-8 animate-fade-in-up">
                    {/* KPI Badges */}
                    <div className="flex flex-wrap gap-4">
                        <KPIBadge
                            label="Net Margin"
                            value={`${financialKPIs.netMarginPct}%`}
                            color={financialKPIs.netMarginPct >= 0
                                ? 'bg-emerald-500 border-emerald-200 dark:border-emerald-800'
                                : 'bg-red-500 border-red-200 dark:border-red-800'}
                        />
                        <KPIBadge
                            label="Rata-rata per Sertifikat"
                            value={formatCurrency(financialKPIs.avgRevenuePerCert)}
                            color="bg-indigo-500 border-indigo-200 dark:border-indigo-800"
                        />
                        <KPIBadge
                            label="Sumber Terbesar"
                            value={financialKPIs.topRevenueSource}
                            color="bg-amber-500 border-amber-200 dark:border-amber-800"
                        />
                    </div>

                    {/* Composed Chart: Revenue vs Expenses */}
                    <div className="bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-100 dark:border-slate-700">
                        <h3 className="text-base sm:text-lg font-bold text-slate-800 dark:text-white mb-2">Pemasukan vs Pengeluaran Bulanan</h3>
                        <p className="text-slate-500 dark:text-slate-400 text-sm mb-6">Tren arus kas masuk, keluar, dan keuntungan bersih</p>
                        <div className="h-[300px] sm:h-[400px] w-full">
                            <ResponsiveContainer width="100%" height="100%">
                                <ComposedChart data={monthlyRevExp} margin={{ left: 10, right: 10 }}>
                                    <defs>
                                        <linearGradient id="pemasukanFill" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stopColor="#6366f1" stopOpacity={0.8} />
                                            <stop offset="100%" stopColor="#6366f1" stopOpacity={0.4} />
                                        </linearGradient>
                                        <linearGradient id="pengeluaranFill" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stopColor="#ef4444" stopOpacity={0.8} />
                                            <stop offset="100%" stopColor="#ef4444" stopOpacity={0.4} />
                                        </linearGradient>
                                    </defs>
                                    <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f1f5f9" className="dark:opacity-20" />
                                    <XAxis dataKey="month" axisLine={false} tickLine={false} tick={{ fill: '#64748b', fontSize: 12 }} dy={10} />
                                    <YAxis axisLine={false} tickLine={false} tick={{ fill: '#64748b', fontSize: 12 }} tickFormatter={(v) => `${(v / 1000000).toFixed(0)}jt`} />
                                    <Tooltip content={<CustomTooltip />} />
                                    <Legend iconType="circle" wrapperStyle={{ fontSize: '13px', paddingTop: '10px' }} />
                                    <Bar dataKey="pemasukan" name="Pemasukan" fill="url(#pemasukanFill)" radius={[6, 6, 0, 0]} barSize={20} />
                                    <Bar dataKey="pengeluaran" name="Pengeluaran" fill="url(#pengeluaranFill)" radius={[6, 6, 0, 0]} barSize={20} />
                                    <Line type="monotone" dataKey="keuntungan" name="Keuntungan" stroke="#22c55e" strokeWidth={3} dot={{ fill: '#22c55e', strokeWidth: 0, r: 5 }} activeDot={{ r: 7, strokeWidth: 0, fill: '#16a34a' }} />
                                </ComposedChart>
                            </ResponsiveContainer>
                        </div>
                    </div>

                    {/* Revenue Breakdown by Certificate Type */}
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                        <div className="bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-100 dark:border-slate-700">
                            <h3 className="text-base sm:text-lg font-bold text-slate-800 dark:text-white mb-2">Pemasukan per Sertifikat</h3>
                            <p className="text-slate-500 dark:text-slate-400 text-sm mb-6">Breakdown pemasukan berdasarkan tipe</p>
                            <div className="h-[300px] w-full">
                                <ResponsiveContainer width="100%" height="100%">
                                    <PieChart>
                                        <defs>
                                            {certByType.map((_, i) => (
                                                <linearGradient key={i} id={`pieGrad${i}`} x1="0" y1="0" x2="1" y2="1">
                                                    <stop offset="0%" stopColor={GRADIENT_PAIRS[i % GRADIENT_PAIRS.length][0]} />
                                                    <stop offset="100%" stopColor={GRADIENT_PAIRS[i % GRADIENT_PAIRS.length][1]} />
                                                </linearGradient>
                                            ))}
                                        </defs>
                                        <Pie data={certByType} cx="50%" cy="50%" innerRadius={50} outerRadius={100} paddingAngle={3} dataKey="revenue" cornerRadius={6}>
                                            {certByType.map((_, i) => (
                                                <Cell key={i} fill={`url(#pieGrad${i})`} stroke="none" />
                                            ))}
                                        </Pie>
                                        <Legend layout="horizontal" verticalAlign="bottom" align="center" iconType="circle" wrapperStyle={{ fontSize: '11px', paddingTop: '16px' }} />
                                        <Tooltip content={<CustomTooltip />} />
                                    </PieChart>
                                </ResponsiveContainer>
                            </div>
                        </div>

                        {/* Financial Summary Cards */}
                        <div className="space-y-4">
                            <div className="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl sm:rounded-3xl p-6 text-white shadow-lg">
                                <p className="text-indigo-100 text-sm font-medium mb-1">Total Pemasukan</p>
                                <p className="text-3xl font-bold tracking-tight">{formatCurrency(summary.totalPemasukan)}</p>
                                <div className="w-full bg-white bg-opacity-20 rounded-full h-2 mt-4">
                                    <div className="bg-white rounded-full h-2 transition-all" style={{ width: `${Math.min((summary.totalKeuntungan / (summary.totalPemasukan || 1)) * 100, 100)}%` }} />
                                </div>
                                <p className="text-indigo-200 text-xs mt-2">Keuntungan: {formatCurrency(summary.totalKeuntungan)}</p>
                            </div>

                            <div className="bg-gradient-to-br from-rose-500 to-red-600 rounded-2xl sm:rounded-3xl p-6 text-white shadow-lg">
                                <p className="text-rose-100 text-sm font-medium mb-1">Total Pengeluaran</p>
                                <p className="text-3xl font-bold tracking-tight">{formatCurrency(summary.totalPengeluaran)}</p>
                                <div className="w-full bg-white bg-opacity-20 rounded-full h-2 mt-4">
                                    <div className="bg-white rounded-full h-2 transition-all" style={{ width: `${Math.min((summary.totalPengeluaran / (summary.totalPemasukan || 1)) * 100, 100)}%` }} />
                                </div>
                                <p className="text-rose-200 text-xs mt-2">Rasio: {summary.totalPemasukan > 0 ? ((summary.totalPengeluaran / summary.totalPemasukan) * 100).toFixed(1) : 0}% dari pemasukan</p>
                            </div>

                            <div className="bg-gradient-to-br from-emerald-500 to-green-600 rounded-2xl sm:rounded-3xl p-6 text-white shadow-lg">
                                <p className="text-emerald-100 text-sm font-medium mb-1">Total Tabungan</p>
                                <p className="text-3xl font-bold tracking-tight">{formatCurrency(summary.totalTabungan)}</p>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* ========== TAB: Pencapaian Marketing ========== */}
            {activeTab === 'pencapaianMarketing' && (
                <div className="bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden animate-fade-in-up transition-colors">
                    <div className="p-4 sm:p-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                        <h3 className="text-base sm:text-lg font-bold text-slate-800 dark:text-white">Peringkat Kinerja Marketing</h3>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead className="bg-slate-50/50 dark:bg-slate-700/50">
                                <tr>
                                    <th className="py-4 px-4 sm:px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Peringkat</th>
                                    <th className="py-4 px-4 sm:px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Marketing</th>
                                    <th className="py-4 px-4 sm:px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden sm:table-cell">Performa</th>
                                    <th className="py-4 px-4 sm:px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Total Profit</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-700">
                                {ranking.map((r, i) => (
                                    <tr key={r.name} className="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors group">
                                        <td className="py-4 px-4 sm:px-6">
                                            <div className={`w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-sm ${i === 0 ? 'bg-gradient-to-br from-yellow-300 to-amber-400 text-white ring-2 ring-yellow-100 dark:ring-yellow-900' :
                                                i === 1 ? 'bg-gradient-to-br from-slate-300 to-slate-400 text-white ring-2 ring-slate-100 dark:ring-slate-700' :
                                                    i === 2 ? 'bg-gradient-to-br from-orange-300 to-orange-400 text-white ring-2 ring-orange-100 dark:ring-orange-900' :
                                                        'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400'
                                                }`}>
                                                {i + 1}
                                            </div>
                                        </td>
                                        <td className="py-4 px-4 sm:px-6">
                                            <p className="font-semibold text-slate-800 dark:text-white">{r.name}</p>
                                            <p className="text-xs text-slate-400 dark:text-slate-500">Marketing Specialist</p>
                                        </td>
                                        <td className="py-4 px-4 sm:px-6 hidden sm:table-cell">
                                            <div className="flex items-center gap-3">
                                                <div className="flex-1 w-24 h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                                    <div className="h-full bg-indigo-500 rounded-full" style={{ width: `${Math.min(r.count * 2, 100)}%` }} />
                                                </div>
                                                <span className="text-sm font-medium text-slate-600 dark:text-slate-300">{r.count} Berkas</span>
                                            </div>
                                        </td>
                                        <td className="py-4 px-4 sm:px-6 text-right">
                                            <span className="font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-3 py-1 rounded-lg">
                                                {formatCurrency(r.totalKeuntungan)}
                                            </span>
                                        </td>
                                    </tr>
                                ))}
                                {ranking.length === 0 && (
                                    <tr>
                                        <td colSpan={4} className="py-12 text-center text-slate-400 dark:text-slate-500">
                                            Belum ada data marketing untuk periode ini.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            )}
        </div>
    );
};
