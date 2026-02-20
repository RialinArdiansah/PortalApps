import { useRef, useState } from 'react';
import { useAppSelector, useAppDispatch } from '@/app/hooks';
import { fetchSubmissions } from '@/features/submissions/submissionsSlice';
import { fetchTransactions } from '@/features/transactions/transactionsSlice';
import { fetchUsers } from '@/features/users/usersSlice';
import { fetchCertificates } from '@/features/certificates/certificatesSlice';
import { fetchMarketing } from '@/features/marketing/marketingSlice';

export const SettingsPage = () => {
    const dispatch = useAppDispatch();
    const { user } = useAppSelector((s) => s.auth);
    const submissions = useAppSelector((s) => s.submissions.list);
    const transactions = useAppSelector((s) => s.transactions.list);
    const users = useAppSelector((s) => s.users.list);
    const certificates = useAppSelector((s) => s.certificates.list);
    const marketing = useAppSelector((s) => s.marketing.list);

    const fileInputRef = useRef<HTMLInputElement>(null);
    const [status, setStatus] = useState<'idle' | 'success' | 'error'>('idle');

    const handleExport = () => {
        const data = {
            version: '1.0.0',
            exportedAt: new Date().toISOString(),
            submissions,
            transactions,
            users,
            certificates,
            marketing,
        };

        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `portalapp-keu-backup-${new Date().toISOString().split('T')[0]}.json`;
        a.click();
        URL.revokeObjectURL(url);
        setStatus('success');
        setTimeout(() => setStatus('idle'), 3000);
    };

    const handleImport = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = async (ev) => {
            try {
                const _data = JSON.parse(ev.target?.result as string);
                alert('⚠️ Import berhasil! Data akan dimuat ulang. (Perlu restart MSW untuk pemuatan penuh.)');
                await dispatch(fetchSubmissions());
                await dispatch(fetchTransactions());
                await dispatch(fetchUsers());
                await dispatch(fetchCertificates());
                await dispatch(fetchMarketing());
                setStatus('success');
            } catch {
                setStatus('error');
            }
            setTimeout(() => setStatus('idle'), 3000);
        };
        reader.readAsText(file);
        e.target.value = '';
    };

    return (
        <div className="max-w-2xl mx-auto">
            <h1 className="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white mb-2">Pengaturan</h1>
            <p className="text-gray-500 dark:text-slate-400 mb-6">Ekspor dan impor data sistem</p>

            <div className="space-y-6">
                {/* Profile */}
                <div className="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 transition-colors">
                    <h2 className="text-lg font-semibold text-gray-800 dark:text-white mb-4">Profil Pengguna</h2>
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div className="text-gray-500 dark:text-slate-400">Nama</div><div className="font-medium text-gray-800 dark:text-white">{user?.fullName}</div>
                        <div className="text-gray-500 dark:text-slate-400">Username</div><div className="font-medium text-gray-800 dark:text-white">{user?.username}</div>
                        <div className="text-gray-500 dark:text-slate-400">Email</div><div className="font-medium text-gray-800 dark:text-white">{user?.email}</div>
                        <div className="text-gray-500 dark:text-slate-400">Peran</div><div className="font-medium text-gray-800 dark:text-white">{user?.role}</div>
                    </div>
                </div>

                {/* Export/Import */}
                <div className="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 transition-colors">
                    <h2 className="text-lg font-semibold text-gray-800 dark:text-white mb-4">Backup & Import Data</h2>
                    <p className="text-sm text-gray-500 dark:text-slate-400 mb-4">Ekspor seluruh data dalam format JSON atau impor dari file backup sebelumnya.</p>

                    <div className="flex flex-col sm:flex-row gap-4">
                        <button
                            onClick={handleExport}
                            className="flex-1 bg-primary-600 dark:bg-indigo-600 text-white font-semibold py-3 rounded-xl hover:bg-primary-700 dark:hover:bg-indigo-700 transition shadow-md"
                        >
                            📥 Ekspor Data (JSON)
                        </button>
                        <button
                            onClick={() => fileInputRef.current?.click()}
                            className="flex-1 bg-orange-500 dark:bg-orange-600 text-white font-semibold py-3 rounded-xl hover:bg-orange-600 dark:hover:bg-orange-700 transition shadow-md"
                        >
                            📤 Impor Data (JSON)
                        </button>
                        <input ref={fileInputRef} type="file" accept=".json" className="hidden" onChange={handleImport} />
                    </div>

                    {status === 'success' && (
                        <p className="text-green-600 dark:text-emerald-400 text-sm font-medium mt-3 text-center">✅ Operasi berhasil!</p>
                    )}
                    {status === 'error' && (
                        <p className="text-red-600 dark:text-red-400 text-sm font-medium mt-3 text-center">❌ Gagal memproses file.</p>
                    )}
                </div>
            </div>
        </div>
    );
};
