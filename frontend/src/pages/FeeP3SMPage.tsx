import { useEffect, useState } from 'react';
import { useAppDispatch, useAppSelector } from '@/app/hooks';
import { fetchFeeP3SM, createFeeP3SM, updateFeeP3SM, deleteFeeP3SM } from '@/features/feeP3sm/feeP3smSlice';
import { Modal } from '@/components/common/Modal';
import { ConfirmDialog } from '@/components/common/ConfirmDialog';
import { Pagination } from '@/components/common/Pagination';
import { formatCurrency } from '@/utils/formatters';
import type { FeeP3SM } from '@/types';

const MONTH_NAMES = [
    '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
];

const FeeP3SMPage = () => {
    const dispatch = useAppDispatch();
    const { list, status } = useAppSelector((s) => s.feeP3sm);

    const [search, setSearch] = useState('');
    const [currentPage, setCurrentPage] = useState(1);
    const itemsPerPage = 10;

    const [modalOpen, setModalOpen] = useState(false);
    const [editingFee, setEditingFee] = useState<FeeP3SM | null>(null);
    const [cost, setCost] = useState(0);
    const [month, setMonth] = useState(1);
    const [year, setYear] = useState(new Date().getFullYear());
    const [deleteId, setDeleteId] = useState<string | null>(null);

    useEffect(() => {
        if (status === 'idle') dispatch(fetchFeeP3SM());
    }, [status, dispatch]);

    const filtered = list.filter((fee) => {
        const term = search.toLowerCase();
        return (
            MONTH_NAMES[fee.month]?.toLowerCase().includes(term) ||
            String(fee.year).includes(term) ||
            String(fee.cost).includes(term)
        );
    });

    const paginated = filtered.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

    const openCreate = () => {
        setEditingFee(null);
        setCost(0);
        setMonth(1);
        setYear(new Date().getFullYear());
        setModalOpen(true);
    };

    const openEdit = (fee: FeeP3SM) => {
        setEditingFee(fee);
        setCost(fee.cost);
        setMonth(fee.month);
        setYear(fee.year);
        setModalOpen(true);
    };

    const handleSubmit = async () => {
        if (editingFee) {
            await dispatch(updateFeeP3SM({ id: editingFee.id, cost, month, year }));
        } else {
            await dispatch(createFeeP3SM({ cost, month, year }));
        }
        setModalOpen(false);
    };

    const handleDelete = async () => {
        if (!deleteId) return;
        await dispatch(deleteFeeP3SM(deleteId));
        setDeleteId(null);
    };

    return (
        <div>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <h1 className="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Fee P3SM</h1>
                <button onClick={openCreate} className="bg-primary-600 dark:bg-indigo-600 text-white px-6 py-3 rounded-2xl font-semibold hover:bg-primary-700 dark:hover:bg-indigo-700 transition shadow-md">
                    + Tambah Fee P3SM
                </button>
            </div>

            <div className="bg-white dark:bg-slate-800 rounded-2xl shadow-md border border-gray-100 dark:border-slate-700 overflow-hidden transition-colors">
                <div className="p-4 border-b border-gray-100 dark:border-slate-700">
                    <input
                        type="text"
                        placeholder="Cari data fee..."
                        value={search}
                        onChange={(e) => { setSearch(e.target.value); setCurrentPage(1); }}
                        className="w-full md:w-80 px-4 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition placeholder-gray-400 dark:placeholder-slate-500"
                    />
                </div>

                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead className="bg-gray-50 dark:bg-slate-700/50">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">Biaya</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">Bulan</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase hidden sm:table-cell">Tahun</th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                            {status === 'loading' && (
                                <tr><td colSpan={4} className="text-center py-8 text-gray-400 dark:text-slate-500">Memuat data...</td></tr>
                            )}
                            {paginated.length === 0 && status !== 'loading' ? (
                                <tr><td colSpan={4} className="text-center py-8 text-gray-400 dark:text-slate-500">Belum ada data fee P3SM</td></tr>
                            ) : paginated.map((fee) => (
                                <tr key={fee.id} className="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                                    <td className="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700 dark:text-white">{formatCurrency(fee.cost)}</td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">{MONTH_NAMES[fee.month]}</td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400 hidden sm:table-cell">{fee.year}</td>
                                    <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                        <button onClick={() => openEdit(fee)} className="text-primary-600 dark:text-indigo-400 hover:text-primary-800 dark:hover:text-indigo-300">Edit</button>
                                        <button onClick={() => setDeleteId(fee.id)} className="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">Hapus</button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                <div className="p-4 border-t border-gray-100 dark:border-slate-700">
                    <Pagination
                        currentPage={currentPage}
                        totalItems={filtered.length}
                        itemsPerPage={itemsPerPage}
                        onPageChange={setCurrentPage}
                        onItemsPerPageChange={() => { }}
                    />
                </div>
            </div>

            <Modal isOpen={modalOpen} onClose={() => setModalOpen(false)} title={editingFee ? 'Edit Fee P3SM' : 'Tambah Fee P3SM Baru'}>
                <div className="space-y-4">
                    <div>
                        <label className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Biaya (Rp)</label>
                        <div className="relative">
                            <span className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-slate-500 font-medium">Rp</span>
                            <input
                                type="text"
                                inputMode="numeric"
                                value={cost === 0 ? '' : new Intl.NumberFormat('id-ID').format(cost)}
                                onChange={(e) => { const raw = e.target.value.replace(/\D/g, ''); setCost(raw === '' ? 0 : Number(raw)); }}
                                className="w-full pl-12 pr-5 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"
                                placeholder="0"
                            />
                        </div>
                    </div>
                    <div>
                        <label className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Bulan</label>
                        <select
                            value={month}
                            onChange={(e) => setMonth(Number(e.target.value))}
                            className="w-full px-5 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"
                        >
                            <option value="">-- Pilih Bulan --</option>
                            {MONTH_NAMES.slice(1).map((name, i) => (
                                <option key={i + 1} value={i + 1}>{name}</option>
                            ))}
                        </select>
                    </div>
                    <div>
                        <label className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Tahun</label>
                        <input
                            type="number"
                            value={year}
                            onChange={(e) => setYear(Number(e.target.value))}
                            className="w-full px-5 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"
                            placeholder="Contoh: 2025"
                            min={2020}
                            max={2030}
                        />
                    </div>
                    <div className="flex justify-end gap-3 pt-2">
                        <button onClick={() => setModalOpen(false)} className="px-6 py-3 rounded-xl font-semibold bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200 hover:bg-gray-300 dark:hover:bg-slate-500 transition">
                            Batal
                        </button>
                        <button onClick={handleSubmit} className="px-6 py-3 rounded-xl font-semibold bg-primary-600 dark:bg-indigo-600 text-white hover:bg-primary-700 dark:hover:bg-indigo-700 transition shadow-md">
                            Simpan
                        </button>
                    </div>
                </div>
            </Modal>

            <ConfirmDialog
                isOpen={!!deleteId}
                onClose={() => setDeleteId(null)}
                onConfirm={handleDelete}
                title="Hapus Fee P3SM"
                message="Apakah Anda yakin ingin menghapus fee P3SM ini?"
            />
        </div>
    );
};

export default FeeP3SMPage;
