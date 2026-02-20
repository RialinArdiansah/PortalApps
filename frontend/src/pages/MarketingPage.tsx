import { useEffect, useState } from 'react';
import { useAppSelector, useAppDispatch } from '@/app/hooks';
import { fetchMarketing, createMarketing, updateMarketing, deleteMarketing } from '@/features/marketing/marketingSlice';
import { Modal } from '@/components/common/Modal';
import { ConfirmDialog } from '@/components/common/ConfirmDialog';

export const MarketingPage = () => {
    const dispatch = useAppDispatch();
    const { list: marketing, status } = useAppSelector((s) => s.marketing);
    const [isModalOpen, setModalOpen] = useState(false);
    const [editingId, setEditingId] = useState<string | null>(null);
    const [deleteId, setDeleteId] = useState<string | null>(null);
    const [name, setName] = useState('');

    useEffect(() => { dispatch(fetchMarketing()); }, [dispatch]);

    const openAdd = () => { setEditingId(null); setName(''); setModalOpen(true); };
    const openEdit = (id: string) => {
        const m = marketing.find((m) => m.id === id);
        if (!m) return;
        setEditingId(id); setName(m.name); setModalOpen(true);
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (editingId) await dispatch(updateMarketing({ id: editingId, name }));
        else await dispatch(createMarketing({ name }));
        setModalOpen(false);
    };

    const handleDelete = async () => {
        if (deleteId) { await dispatch(deleteMarketing(deleteId)); setDeleteId(null); }
    };

    return (
        <div>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h1 className="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Manajemen Marketing</h1>
                    <p className="text-gray-500 dark:text-slate-400 mt-1">Kelola nama-nama marketing</p>
                </div>
                <button onClick={openAdd} className="bg-primary-600 dark:bg-indigo-600 text-white font-semibold px-5 py-3 rounded-xl hover:bg-primary-700 dark:hover:bg-indigo-700 transition shadow-md">
                    + Tambah Marketing
                </button>
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
                                    <th className="text-left py-3 px-4 font-semibold text-gray-600 dark:text-slate-400">Nama Marketing</th>
                                    <th className="text-right py-3 px-4 font-semibold text-gray-600 dark:text-slate-400">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {marketing.map((m, i) => (
                                    <tr key={m.id} className="border-t border-gray-100 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                                        <td className="py-3 px-4 text-gray-600 dark:text-slate-400">{i + 1}</td>
                                        <td className="py-3 px-4 font-medium text-gray-800 dark:text-white">{m.name}</td>
                                        <td className="py-3 px-4 text-right space-x-2">
                                            <button onClick={() => openEdit(m.id)} className="text-primary-600 dark:text-indigo-400 hover:text-primary-800 dark:hover:text-indigo-300 font-medium">Edit</button>
                                            <button onClick={() => setDeleteId(m.id)} className="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-medium">Hapus</button>
                                        </td>
                                    </tr>
                                ))}
                                {marketing.length === 0 && (
                                    <tr><td colSpan={3} className="text-center py-8 text-gray-400 dark:text-slate-500">Belum ada data marketing</td></tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>

            <Modal isOpen={isModalOpen} onClose={() => setModalOpen(false)} title={editingId ? 'Edit Marketing' : 'Tambah Marketing'}>
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Nama Marketing</label>
                        <input type="text" value={name} onChange={(e) => setName(e.target.value)}
                            className="w-full px-5 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition" required />
                    </div>
                    <div className="flex justify-end gap-2 mt-6">
                        <button type="button" onClick={() => setModalOpen(false)} className="px-6 py-3 rounded-xl font-semibold bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200 hover:bg-gray-300 dark:hover:bg-slate-500 transition">Batal</button>
                        <button type="submit" className="px-6 py-3 rounded-xl font-semibold bg-primary-600 dark:bg-indigo-600 text-white hover:bg-primary-700 dark:hover:bg-indigo-700 transition">Simpan</button>
                    </div>
                </form>
            </Modal>

            <ConfirmDialog isOpen={!!deleteId} onClose={() => setDeleteId(null)} onConfirm={handleDelete} message="Marketing akan dihapus permanen." />
        </div>
    );
};
