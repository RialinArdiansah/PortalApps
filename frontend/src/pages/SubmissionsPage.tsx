import { useEffect, useMemo } from 'react';
import { Link } from 'react-router-dom';
import { useAppSelector, useAppDispatch } from '@/app/hooks';
import { fetchSubmissions, deleteSubmission, setPage, setItemsPerPage, setSearchTerm } from '@/features/submissions/submissionsSlice';
import { Pagination } from '@/components/common/Pagination';
import { ConfirmDialog } from '@/components/common/ConfirmDialog';
import { formatCurrency, formatDate, capitalizeWords } from '@/utils/formatters';
import { useState } from 'react';

export const SubmissionsPage = () => {
    const dispatch = useAppDispatch();
    const { user } = useAppSelector((s) => s.auth);
    const { list, status, pagination } = useAppSelector((s) => s.submissions);
    const [deleteId, setDeleteId] = useState<string | null>(null);

    useEffect(() => { dispatch(fetchSubmissions()); }, [dispatch]);

    const isAllView = user?.role === 'Super admin' || user?.role === 'admin' || user?.role === 'manager';

    const filteredByUser = useMemo(() => {
        const userFiltered = isAllView ? list : list.filter((s) => s.submittedById === user?.id);
        if (!pagination.searchTerm) return userFiltered;
        const term = pagination.searchTerm.toLowerCase();
        return userFiltered.filter((s) =>
            s.companyName.toLowerCase().includes(term) ||
            s.marketingName.toLowerCase().includes(term) ||
            s.certificateType.toLowerCase().includes(term)
        );
    }, [list, isAllView, user, pagination.searchTerm]);

    const totalItems = filteredByUser.length;
    const startIdx = (pagination.currentPage - 1) * pagination.itemsPerPage;
    const paged = filteredByUser.slice(startIdx, startIdx + pagination.itemsPerPage);

    const handleDelete = async () => {
        if (deleteId) {
            await dispatch(deleteSubmission(deleteId));
            setDeleteId(null);
        }
    };

    return (
        <div>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h1 className="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">{isAllView ? 'Data Input Pengguna' : 'Data Input Saya'}</h1>
                    <p className="text-gray-500 dark:text-slate-400 mt-1">Riwayat data sertifikasi yang telah diinput</p>
                </div>
                <div className="flex gap-3 w-full sm:w-auto">
                    <input
                        type="text"
                        placeholder="Cari..."
                        value={pagination.searchTerm}
                        onChange={(e) => dispatch(setSearchTerm(e.target.value))}
                        className="px-4 py-2 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-800 dark:text-white rounded-xl text-sm flex-1 sm:flex-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:outline-none transition placeholder-gray-400 dark:placeholder-slate-500"
                    />
                    <Link to="/submissions/new" className="bg-primary-600 dark:bg-indigo-600 text-white font-semibold px-5 py-2 rounded-xl hover:bg-primary-700 dark:hover:bg-indigo-700 transition shadow-md whitespace-nowrap">
                        + Input Baru
                    </Link>
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
                                    <th className="text-left py-3 px-4 font-semibold text-gray-600 dark:text-slate-400">Nama Perusahaan</th>
                                    <th className="text-left py-3 px-4 font-semibold text-gray-600 dark:text-slate-400 hidden sm:table-cell">Marketing</th>
                                    <th className="text-left py-3 px-4 font-semibold text-gray-600 dark:text-slate-400 hidden md:table-cell">Jenis</th>
                                    <th className="text-left py-3 px-4 font-semibold text-gray-600 dark:text-slate-400 hidden lg:table-cell">Tanggal</th>
                                    <th className="text-right py-3 px-4 font-semibold text-gray-600 dark:text-slate-400">Keuntungan</th>
                                    <th className="text-right py-3 px-4 font-semibold text-gray-600 dark:text-slate-400">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {paged.map((s, i) => (
                                    <tr key={s.id} className="border-t border-gray-100 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                                        <td className="py-3 px-4 text-gray-600 dark:text-slate-400">{startIdx + i + 1}</td>
                                        <td className="py-3 px-4 font-medium text-gray-800 dark:text-white">{capitalizeWords(s.companyName)}</td>
                                        <td className="py-3 px-4 text-gray-600 dark:text-slate-400 hidden sm:table-cell">{s.marketingName}</td>
                                        <td className="py-3 px-4 text-gray-600 dark:text-slate-400 hidden md:table-cell">{s.certificateType}</td>
                                        <td className="py-3 px-4 text-gray-600 dark:text-slate-400 hidden lg:table-cell">{formatDate(s.inputDate)}</td>
                                        <td className="py-3 px-4 text-right font-semibold text-green-700 dark:text-emerald-400">{formatCurrency(s.keuntungan)}</td>
                                        <td className="py-3 px-4 text-right">
                                            <button onClick={() => setDeleteId(s.id)} className="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-medium text-sm">Hapus</button>
                                        </td>
                                    </tr>
                                ))}
                                {paged.length === 0 && (
                                    <tr><td colSpan={7} className="text-center py-8 text-gray-400 dark:text-slate-500">Belum ada data</td></tr>
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

            <ConfirmDialog isOpen={!!deleteId} onClose={() => setDeleteId(null)} onConfirm={handleDelete} message="Data input akan dihapus permanen." />
        </div>
    );
};
