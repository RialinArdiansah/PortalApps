import { useEffect, useMemo } from 'react';
import { useAppSelector, useAppDispatch } from '@/app/hooks';
import { fetchSubmissions } from '@/features/submissions/submissionsSlice';
import { fetchTransactions } from '@/features/transactions/transactionsSlice';
import { setFilter, setActiveTab } from '@/features/dashboard/dashboardSlice';
import { formatCurrency, getGreeting, MONTH_NAMES } from '@/utils/formatters';
import {
    calculateDashboardSummary, filterByDateRange, groupByMarketing,
    calculateMonthlyProfits, calculateMarketingRanking,
} from '@/utils/calculations';
import type { DashboardFilterType } from '@/types';
import {
    AreaChart, Area, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer,
    PieChart, Pie, Cell, Legend,
} from 'recharts';

const CHART_COLORS = ['#6366f1', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];

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
                <TabButton label="Ringkasan Kinerja" id="ringkasan" />
                <TabButton label="Pencapaian Marketing" id="pencapaianMarketing" />
            </div>

            {/* Tab Panels */}
            {activeTab === 'ringkasan' && (
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 animate-fade-in-up">
                    <div className="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-100 dark:border-slate-700 transition-colors">
                        <div className="flex justify-between items-center mb-6">
                            <h3 className="text-base sm:text-lg font-bold text-slate-800 dark:text-white">Tren Keuntungan Bulanan</h3>
                            <button className="text-indigo-600 dark:text-indigo-400 text-sm font-medium hover:underline">Lihat Detail</button>
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
                                    <Tooltip contentStyle={{ borderRadius: '12px', border: 'none', boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)' }} formatter={(value: number) => [formatCurrency(value), 'Keuntungan']} />
                                    <Area type="monotone" dataKey="keuntungan" stroke="#6366f1" strokeWidth={3} fillOpacity={1} fill="url(#colorProfit)" activeDot={{ r: 6, strokeWidth: 0, fill: '#4f46e5' }} />
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
                                    <Tooltip contentStyle={{ borderRadius: '12px', border: 'none', boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)' }} />
                                </PieChart>
                            </ResponsiveContainer>
                        </div>
                    </div>
                </div>
            )}

            {activeTab === 'pencapaianMarketing' && (
                <div className="bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden animate-fade-in-up transition-colors">
                    <div className="p-4 sm:p-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                        <h3 className="text-base sm:text-lg font-bold text-slate-800 dark:text-white">Peringkat Kinerja Marketing</h3>
                        <button className="text-sm text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium">Download Report</button>
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
