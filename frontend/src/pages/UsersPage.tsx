import { useEffect, useState } from 'react';
import { useAppSelector, useAppDispatch } from '@/app/hooks';
import { fetchUsers, createUser, updateUser, deleteUser } from '@/features/users/usersSlice';
import { Modal } from '@/components/common/Modal';
import { ConfirmDialog } from '@/components/common/ConfirmDialog';
import { RoleBadge } from '@/components/common/RoleBadge';
import type { UserRole } from '@/types';

const ROLES: UserRole[] = ['Super admin', 'admin', 'manager', 'karyawan', 'marketing', 'mitra'];

export const UsersPage = () => {
    const dispatch = useAppDispatch();
    const { list: users, status } = useAppSelector((s) => s.users);
    const [isModalOpen, setModalOpen] = useState(false);
    const [editingId, setEditingId] = useState<string | null>(null);
    const [deleteId, setDeleteId] = useState<string | null>(null);
    const [form, setForm] = useState({ fullName: '', username: '', email: '', password: '', role: 'karyawan' as UserRole });

    useEffect(() => { dispatch(fetchUsers()); }, [dispatch]);

    const openAdd = () => {
        setEditingId(null);
        setForm({ fullName: '', username: '', email: '', password: '', role: 'karyawan' });
        setModalOpen(true);
    };

    const openEdit = (id: string) => {
        const user = users.find((u) => u.id === id);
        if (!user) return;
        setEditingId(id);
        setForm({ fullName: user.fullName, username: user.username, email: user.email, password: '', role: user.role });
        setModalOpen(true);
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (editingId) {
            await dispatch(updateUser({ id: editingId, ...form }));
        } else {
            await dispatch(createUser(form));
        }
        setModalOpen(false);
    };

    const handleDelete = async () => {
        if (deleteId) {
            await dispatch(deleteUser(deleteId));
            setDeleteId(null);
        }
    };

    return (
        <div>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h1 className="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Daftar Pengguna</h1>
                    <p className="text-gray-500 dark:text-slate-400 mt-1">Kelola pengguna sistem</p>
                </div>
                <button onClick={openAdd} className="bg-primary-600 dark:bg-indigo-600 text-white font-semibold px-5 py-3 rounded-xl hover:bg-primary-700 dark:hover:bg-indigo-700 transition shadow-md">
                    + Tambah Pengguna
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
                                    <th className="text-left py-3 px-4 font-semibold text-gray-600 dark:text-slate-400">Nama</th>
                                    <th className="text-left py-3 px-4 font-semibold text-gray-600 dark:text-slate-400 hidden sm:table-cell">Username</th>
                                    <th className="text-left py-3 px-4 font-semibold text-gray-600 dark:text-slate-400 hidden md:table-cell">Email</th>
                                    <th className="text-left py-3 px-4 font-semibold text-gray-600 dark:text-slate-400">Peran</th>
                                    <th className="text-right py-3 px-4 font-semibold text-gray-600 dark:text-slate-400">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {users.map((u) => (
                                    <tr key={u.id} className="border-t border-gray-100 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                                        <td className="py-3 px-4 font-medium text-gray-800 dark:text-white">{u.fullName}</td>
                                        <td className="py-3 px-4 text-gray-600 dark:text-slate-400 hidden sm:table-cell">{u.username}</td>
                                        <td className="py-3 px-4 text-gray-600 dark:text-slate-400 hidden md:table-cell">{u.email}</td>
                                        <td className="py-3 px-4"><RoleBadge role={u.role} /></td>
                                        <td className="py-3 px-4 text-right space-x-2">
                                            <button onClick={() => openEdit(u.id)} className="text-primary-600 dark:text-indigo-400 hover:text-primary-800 dark:hover:text-indigo-300 font-medium">Edit</button>
                                            <button onClick={() => setDeleteId(u.id)} className="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-medium">Hapus</button>
                                        </td>
                                    </tr>
                                ))}
                                {users.length === 0 && (
                                    <tr><td colSpan={5} className="text-center py-8 text-gray-400 dark:text-slate-500">Belum ada data pengguna</td></tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>

            {/* User Modal */}
            <Modal isOpen={isModalOpen} onClose={() => setModalOpen(false)} title={editingId ? 'Edit Pengguna' : 'Tambah Pengguna'}>
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Nama Lengkap</label>
                        <input type="text" value={form.fullName} onChange={(e) => setForm({ ...form, fullName: e.target.value })}
                            className="w-full px-5 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition" required />
                    </div>
                    <div>
                        <label className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Username</label>
                        <input type="text" value={form.username} onChange={(e) => setForm({ ...form, username: e.target.value })}
                            className="w-full px-5 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition" required />
                    </div>
                    <div>
                        <label className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Email</label>
                        <input type="email" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })}
                            className="w-full px-5 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition" required />
                    </div>
                    <div>
                        <label className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Password</label>
                        <input type="password" value={form.password} onChange={(e) => setForm({ ...form, password: e.target.value })}
                            className="w-full px-5 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition" placeholder={editingId ? 'Biarkan kosong untuk password lama' : ''} required={!editingId} />
                    </div>
                    <div>
                        <label className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Peran</label>
                        <select value={form.role} onChange={(e) => setForm({ ...form, role: e.target.value as UserRole })}
                            className="w-full px-5 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            {ROLES.map((r) => <option key={r} value={r}>{r}</option>)}
                        </select>
                    </div>
                    <div className="flex justify-end gap-2 mt-6">
                        <button type="button" onClick={() => setModalOpen(false)} className="px-6 py-3 rounded-xl font-semibold bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200 hover:bg-gray-300 dark:hover:bg-slate-500 transition">Batal</button>
                        <button type="submit" className="px-6 py-3 rounded-xl font-semibold bg-primary-600 dark:bg-indigo-600 text-white hover:bg-primary-700 dark:hover:bg-indigo-700 transition">Simpan</button>
                    </div>
                </form>
            </Modal>

            <ConfirmDialog isOpen={!!deleteId} onClose={() => setDeleteId(null)} onConfirm={handleDelete} message="Pengguna akan dihapus permanen." />
        </div>
    );
};
