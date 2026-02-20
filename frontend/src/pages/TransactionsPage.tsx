import { useEffect, useState, useMemo } from 'react';
import { useAppSelector, useAppDispatch } from '@/app/hooks';
import { fetchTransactions, createTransaction, deleteTransaction, setPage, setItemsPerPage, setSearchTerm } from '@/features/transactions/transactionsSlice';
import { Modal } from '@/components/common/Modal';
import { Pagination } from '@/components/common/Pagination';
import { ConfirmDialog } from '@/components/common/ConfirmDialog';
import { formatCurrency, formatDate } from '@/utils/formatters';
import type { TransactionType } from '@/types';

const TRANSACTION_TYPES: TransactionType[] = ['Keluar', 'Tabungan', 'Kas'];

export const TransactionsPage = () => {
    const dispatch = useAppDispatch();
    const { user } = useAppSelector((s) => s.auth);
    const { list, status, pagination } = useAppSelector((s) => s.transactions);
    const [isModalOpen, setModalOpen] = useState(false);
    const [deleteId, setDeleteId] = useState<string | null>(null);
    const [form, setForm] = useState({
        transactionDate: new Date().toISOString().split('T')[0],
        transactionName: '',
        cost: 0,
        transactionType: 'Keluar' as TransactionType,
    });

    useEffect(() => { dispatch(fetchTransactions()); }, [dispatch]);

    const isAllView = user?.role === 'Super admin' || user?.role === 'admin' || user?.role === 'manager';

    const filtered = useMemo(() => {
        const userFiltered = isAllView ? list : list.filter((t) => t.submittedById === user?.id);
        if (!pagination.searchTerm) return userFiltered;
        const term = pagination.searchTerm.toLowerCase();
        return userFiltered.filter((t) =>
            t.transactionName.toLowerCase().includes(term) ||
            t.transactionType.toLowerCase().includes(term)
        );
    }, [list, user, isAllView, pagination.searchTerm]);

    const totalItems = filtered.length;
    const startIdx = (pagination.currentPage - 1) * pagination.itemsPerPage;
    const paged = filtered.slice(startIdx, startIdx + pagination.itemsPerPage);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        await dispatch(createTransaction({
            ...form,
            submittedById: user?.id || '',
            proof: null,
        }));
        setModalOpen(false);
        setForm({ transactionDate: new Date().toISOString().split('T')[0], transactionName: '', cost: 0, transactionType: 'Keluar' });
    };

    const handleDelete = async () => {
        if (deleteId) { await dispatch(deleteTransaction(deleteId)); setDeleteId(null); }
    };

    const typeBadge = (type: TransactionType) => {
        const cls = type === 'Keluar'
            ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'
            : type === 'Tabungan'
                ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400'
                : 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400';
        return <span className={`px-2 py-1 rounded-full text-xs font-semibold ${cls}`}>{type}</span>;
    };

    return (
        <div>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h1 className="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Entri Transaksi</h1>
                    <p className="text-gray-500 dark:text-slate-400 mt-1">Kelola pengeluaran, tabungan dan kas</p>
                </div>
                <div className="flex gap-3 w-full sm:w-auto">
                    <input
                        type="text"
                        placeholder="Cari..."
                        value={pagination.searchTerm}
                        onChange={(e) => dispatch(setSearchTerm(e.target.value))}
                        className="px-4 py-2 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-800 dark:text-white rounded-xl text-sm flex-1 sm:flex-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:outline-none transition placeholder-gray-400 dark:placeholder-slate-500"
                    />
                    <button onClick={() => setModalOpen(true)} className="bg-primary-600 dark:bg-indigo-600 text-white font-semibold px-5 py-2 rounded-xl hover:bg-primary-700 dark:hover:bg-indigo-700 transition shadow-md whitespace-nowrap">
                        + Tambah Transaksi
                    </button>
                </div>
            </div>

            <div className="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden transition-colors">
                {status === 'loading' ? (
                    <div className="text-center py-12 text-gray-400 dark:text-slate-500">Memuat...</div>
                ) : (
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead className="bg-gray-50 dark:bg-slate-700/50">
                                <tr>
                                    <th className="text-left py-3 px-4 font-semibold text-gray-600 dark:text-slate-400">#</th>
                                    <th className="text-left py-3 px-4 font-semibold text-gray-600 dark:text-slate-400">Nama Transaksi</th>
                                    <th className="text-left py-3 px-4 font-semibold text-gray-600 dark:text-slate-400 hidden sm:table-cell">Tanggal</th>
                                    <th className="text-left py-3 px-4 font-semibold text-gray-600 dark:text-slate-400">Tipe</th>
                                    <th className="text-right py-3 px-4 font-semibold text-gray-600 dark:text-slate-400">Biaya</th>
                                    <th className="text-right py-3 px-4 font-semibold text-gray-600 dark:text-slate-400">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {paged.map((t, i) => (
                                    <tr key={t.id} className="border-t border-gray-100 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                                        <td className="py-3 px-4 text-gray-600 dark:text-slate-400">{startIdx + i + 1}</td>
                                        <td className="py-3 px-4 font-medium text-gray-800 dark:text-white">{t.transactionName}</td>
                                        <td className="py-3 px-4 text-gray-600 dark:text-slate-400 hidden sm:table-cell">{formatDate(t.transactionDate)}</td>
                                        <td className="py-3 px-4">{typeBadge(t.transactionType)}</td>
                                        <td className="py-3 px-4 text-right font-semibold text-gray-800 dark:text-white">{formatCurrency(t.cost)}</td>
                                        <td className="py-3 px-4 text-right">
                                            <button onClick={() => setDeleteId(t.id)} className="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-medium text-sm">Hapus</button>
                                        </td>
                                    </tr>
                                ))}
                                {paged.length === 0 && (
                                    <tr><td colSpan={6} className="text-center py-8 text-gray-400 dark:text-slate-500">Belum ada data</td></tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                )}
                <div className="px-4 pb-4">
                    <Pagination
                        currentPage={pagination.currentPage}
                        totalItems={totalItems}
                        itemsPerPage={pagination.itemsPerPage}
                        onPageChange={(p) => dispatch(setPage(p))}
                        onItemsPerPageChange={(n) => dispatch(setItemsPerPage(n))}
                    />
                </div>
            </div>

            {/* Add Transaction Modal */}
            <Modal isOpen={isModalOpen} onClose={() => setModalOpen(false)} title="Tambah Transaksi">
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Nama Transaksi</label>
                        <input type="text" value={form.transactionName} onChange={(e) => setForm({ ...form, transactionName: e.target.value })}
                            className="w-full px-5 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition" required />
                    </div>
                    <div>
                        <label className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Tanggal</label>
                        <input type="date" value={form.transactionDate} onChange={(e) => setForm({ ...form, transactionDate: e.target.value })}
                            className="w-full px-5 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition" required />
                    </div>
                    <div>
                        <label className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Tipe Transaksi</label>
                        <select value={form.transactionType} onChange={(e) => setForm({ ...form, transactionType: e.target.value as TransactionType })}
                            className="w-full px-5 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            {TRANSACTION_TYPES.map((t) => <option key={t} value={t}>{t}</option>)}
                        </select>
                    </div>
                    <div>
                        <label className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Biaya (Rp)</label>
                        <input type="number" value={form.cost} onChange={(e) => setForm({ ...form, cost: Number(e.target.value) })}
                            className="w-full px-5 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition" min={0} required />
                    </div>
                    <div className="flex justify-end gap-2 mt-6">
                        <button type="button" onClick={() => setModalOpen(false)} className="px-6 py-3 rounded-xl font-semibold bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200 hover:bg-gray-300 dark:hover:bg-slate-500 transition">Batal</button>
                        <button type="submit" className="px-6 py-3 rounded-xl font-semibold bg-primary-600 dark:bg-indigo-600 text-white hover:bg-primary-700 dark:hover:bg-indigo-700 transition">Simpan</button>
                    </div>
                </form>
            </Modal>

            <ConfirmDialog isOpen={!!deleteId} onClose={() => setDeleteId(null)} onConfirm={handleDelete} message="Transaksi akan dihapus permanen." />
        </div>
    );
};
