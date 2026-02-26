import { useEffect, useState } from 'react';
import { useAppDispatch, useAppSelector } from '@/app/hooks';
import { fetchCertificates, createCertificate, updateCertificate, deleteCertificate } from '@/features/certificates/certificatesSlice';
import { Modal } from '@/components/common/Modal';
import { ConfirmDialog } from '@/components/common/ConfirmDialog';
import { SbuAdminModal } from '@/components/certificates/SbuAdminModal';
import type { SbuType, MenuConfig } from '@/types';
import { DEFAULT_MENU_CONFIG } from '@/types';

const CertificatesPage = () => {
    const dispatch = useAppDispatch();
    const { list, status } = useAppSelector((s) => s.certificates);

    const [modalOpen, setModalOpen] = useState(false);
    const [editId, setEditId] = useState<string | null>(null);
    const [name, setName] = useState('');
    const [menuConfig, setMenuConfig] = useState<MenuConfig>({ ...DEFAULT_MENU_CONFIG });
    const [deleteId, setDeleteId] = useState<string | null>(null);

    const [sbuAdminOpen, setSbuAdminOpen] = useState(false);
    const [sbuAdminType, setSbuAdminType] = useState<SbuType>('konstruksi');
    const [sbuAdminMenuConfig, setSbuAdminMenuConfig] = useState<MenuConfig>({ ...DEFAULT_MENU_CONFIG });

    useEffect(() => {
        if (status === 'idle') dispatch(fetchCertificates());
    }, [status, dispatch]);

    const openCreate = () => {
        setEditId(null);
        setName('');
        setMenuConfig({ ...DEFAULT_MENU_CONFIG });
        setModalOpen(true);
    };

    const openEdit = (id: string, n: string, mc: MenuConfig | null) => {
        setEditId(id);
        setName(n);
        setMenuConfig(mc ?? { ...DEFAULT_MENU_CONFIG });
        setModalOpen(true);
    };

    const openSbuAdmin = (type: SbuType, mc: MenuConfig | null) => {
        setSbuAdminType(type);
        setSbuAdminMenuConfig(mc ?? { ...DEFAULT_MENU_CONFIG });
        setSbuAdminOpen(true);
    };

    const handleSubmit = async () => {
        if (!name.trim()) return;
        if (editId) {
            await dispatch(updateCertificate({ id: editId, name, menuConfig }));
        } else {
            await dispatch(createCertificate({ name, menuConfig }));
            await dispatch(fetchCertificates());
        }
        setModalOpen(false);
    };

    const handleDelete = async () => {
        if (!deleteId) return;
        await dispatch(deleteCertificate(deleteId));
        setDeleteId(null);
    };

    const toggleConfig = (key: keyof Omit<MenuConfig, 'kualifikasiLabel'>) => {
        setMenuConfig((prev) => ({ ...prev, [key]: !prev[key] }));
    };

    // Checkbox styling helper
    const checkboxCls = "w-5 h-5 text-indigo-600 bg-white dark:bg-slate-700 border-gray-300 dark:border-slate-500 rounded focus:ring-indigo-500 cursor-pointer";
    const labelCls = "ml-3 text-sm font-medium text-gray-700 dark:text-slate-300 cursor-pointer select-none";

    // Summarize active menus for the card description
    const getMenuSummary = (mc: MenuConfig | null): string => {
        if (!mc) return 'Konfigurasi menu belum ditentukan';
        const parts: string[] = [];
        if (mc.asosiasi) parts.push('Asosiasi');
        if (mc.klasifikasi) parts.push('Klasifikasi');
        if (mc.kualifikasi) parts.push(mc.kualifikasiLabel || 'Kualifikasi');
        if (mc.biayaSetor) parts.push('Biaya Setor Kantor');
        if (mc.biayaLainnya) parts.push('Biaya Lainnya');
        if (mc.kodeField?.enabled) parts.push(mc.kodeField.label || 'Kode');
        return parts.length > 0 ? parts.join(', ') : 'Tidak ada menu aktif';
    };

    return (
        <div>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <h1 className="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Manajemen Sertifikat</h1>
                <button onClick={openCreate} className="bg-primary-600 dark:bg-indigo-600 text-white px-6 py-3 rounded-2xl font-semibold hover:bg-primary-700 dark:hover:bg-indigo-700 transition shadow-md">
                    + Tambah Sertifikat
                </button>
            </div>

            {status === 'loading' && <p className="text-gray-500 dark:text-slate-400">Memuat data...</p>}

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                {list.map((cert) => {
                    const sbuType = cert.sbuTypeSlug;
                    const isAdvanced = !!sbuType;

                    return (
                        <div key={cert.id} className="bg-white dark:bg-slate-800 rounded-2xl shadow-md hover:shadow-lg transition p-5 sm:p-6 border border-gray-100 dark:border-slate-700">
                            <div className="flex justify-between items-start mb-3">
                                <h3 className="text-lg font-semibold text-gray-800 dark:text-white">{cert.name}</h3>
                                {isAdvanced && (
                                    <span className="bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 text-xs font-medium px-2 py-1 rounded-lg">Menu Bertingkat</span>
                                )}
                            </div>

                            <p className="text-sm text-gray-500 dark:text-slate-400 mb-4">
                                {isAdvanced ? getMenuSummary(cert.menuConfig) : cert.subMenus.length > 0 ? cert.subMenus.join(', ') : 'Tidak ada sub-menu'}
                            </p>

                            <div className="flex flex-wrap gap-2">
                                {isAdvanced ? (
                                    <button
                                        onClick={() => openSbuAdmin(sbuType, cert.menuConfig)}
                                        className="flex-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition"
                                    >
                                        Kelola Menu
                                    </button>
                                ) : (
                                    <button
                                        onClick={() => openEdit(cert.id, cert.name, cert.menuConfig)}
                                        className="flex-1 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-gray-200 dark:hover:bg-slate-600 transition"
                                    >
                                        Edit
                                    </button>
                                )}

                                {isAdvanced && (
                                    <button
                                        onClick={() => openEdit(cert.id, cert.name, cert.menuConfig)}
                                        className="bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-gray-200 dark:hover:bg-slate-600 transition"
                                    >
                                        Edit
                                    </button>
                                )}

                                <button
                                    onClick={() => setDeleteId(cert.id)}
                                    className="bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-red-100 dark:hover:bg-red-900/50 transition"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
                    );
                })}
            </div>

            {/* Create / Edit Modal with Menu Config */}
            <Modal isOpen={modalOpen} onClose={() => setModalOpen(false)} title={editId ? 'Edit Sertifikat' : 'Tambah Sertifikat'}>
                <div className="space-y-5">
                    {/* Name */}
                    <div>
                        <label className="block text-gray-700 dark:text-slate-300 font-medium mb-2">Nama Sertifikat</label>
                        <input
                            type="text"
                            value={name}
                            onChange={(e) => setName(e.target.value)}
                            className="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"
                            placeholder="Masukkan nama sertifikat"
                        />
                    </div>

                    {/* Menu Config Section */}
                    <div>
                        <label className="block text-gray-700 dark:text-slate-300 font-medium mb-3">Menu Bertingkat</label>
                        <p className="text-xs text-gray-400 dark:text-slate-500 mb-3">Pilih menu yang akan tersedia di "Kelola Menu" untuk sertifikat ini.</p>

                        <div className="space-y-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 border border-gray-200 dark:border-slate-600">
                            {/* Asosiasi */}
                            <label className="flex items-center">
                                <input type="checkbox" checked={menuConfig.asosiasi} onChange={() => toggleConfig('asosiasi')} className={checkboxCls} />
                                <span className={labelCls}>Asosiasi</span>
                            </label>

                            {/* Klasifikasi */}
                            <label className="flex items-center">
                                <input type="checkbox" checked={menuConfig.klasifikasi} onChange={() => toggleConfig('klasifikasi')} className={checkboxCls} />
                                <span className={labelCls}>Klasifikasi</span>
                            </label>

                            {/* Kualifikasi + label + partner label */}
                            <div>
                                <label className="flex items-center">
                                    <input type="checkbox" checked={menuConfig.kualifikasi} onChange={() => toggleConfig('kualifikasi')} className={checkboxCls} />
                                    <span className={labelCls}>Kualifikasi / Jenjang</span>
                                </label>
                                {menuConfig.kualifikasi && (
                                    <div className="ml-8 mt-2 space-y-2">
                                        <div>
                                            <label className="text-xs text-gray-500 dark:text-slate-400 mb-1 block">Label kustom:</label>
                                            <input
                                                type="text"
                                                value={menuConfig.kualifikasiLabel}
                                                onChange={(e) => setMenuConfig((prev) => ({ ...prev, kualifikasiLabel: e.target.value }))}
                                                className="w-full px-3 py-2 border border-gray-300 dark:border-slate-500 bg-white dark:bg-slate-600 text-gray-900 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"
                                                placeholder="mis. Kualifikasi, Jenjang, Ekuitas KAP"
                                            />
                                        </div>
                                        <div>
                                            <label className="text-xs text-gray-500 dark:text-slate-400 mb-1 block">Label nama partner (biaya di kualifikasi):</label>
                                            <input
                                                type="text"
                                                value={menuConfig.biayaSetorLabel || ''}
                                                onChange={(e) => setMenuConfig((prev) => ({ ...prev, biayaSetorLabel: e.target.value }))}
                                                className="w-full px-3 py-2 border border-gray-300 dark:border-slate-500 bg-white dark:bg-slate-600 text-gray-900 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"
                                                placeholder="mis. Stor Ke RIA, Stor Ke P3SM, Stor Ke Alam"
                                            />
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* Biaya Setor Kantor */}
                            <label className="flex items-center">
                                <input type="checkbox" checked={menuConfig.biayaSetor} onChange={() => toggleConfig('biayaSetor')} className={checkboxCls} />
                                <span className={labelCls}>Biaya Setor Kantor</span>
                            </label>

                            {/* Biaya Lainnya */}
                            <label className="flex items-center">
                                <input type="checkbox" checked={menuConfig.biayaLainnya} onChange={() => toggleConfig('biayaLainnya')} className={checkboxCls} />
                                <span className={labelCls}>Biaya Lainnya</span>
                            </label>

                            {/* Kode / Extra text field */}
                            <div className="border-t border-gray-200 dark:border-slate-600 pt-3 mt-1">
                                <label className="flex items-center">
                                    <input
                                        type="checkbox"
                                        checked={menuConfig.kodeField?.enabled || false}
                                        onChange={() => setMenuConfig((prev) => ({
                                            ...prev,
                                            kodeField: {
                                                enabled: !prev.kodeField?.enabled,
                                                label: prev.kodeField?.label || 'Kode',
                                            },
                                        }))}
                                        className={checkboxCls}
                                    />
                                    <span className={labelCls}>Extra Text Field (Kode / Keterangan)</span>
                                </label>
                                {menuConfig.kodeField?.enabled && (
                                    <div className="ml-8 mt-2">
                                        <label className="text-xs text-gray-500 dark:text-slate-400 mb-1 block">Label field:</label>
                                        <input
                                            type="text"
                                            value={menuConfig.kodeField?.label || ''}
                                            onChange={(e) => setMenuConfig((prev) => ({
                                                ...prev,
                                                kodeField: { enabled: true, label: e.target.value },
                                            }))}
                                            className="w-full px-3 py-2 border border-gray-300 dark:border-slate-500 bg-white dark:bg-slate-600 text-gray-900 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"
                                            placeholder="mis. Kode, Keterangan"
                                        />
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="flex justify-end gap-3 pt-2">
                        <button onClick={() => setModalOpen(false)} className="px-6 py-3 rounded-xl font-semibold bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200 hover:bg-gray-300 dark:hover:bg-slate-500 transition">
                            Batal
                        </button>
                        <button onClick={handleSubmit} className="px-6 py-3 rounded-xl font-semibold bg-primary-600 dark:bg-indigo-600 text-white hover:bg-primary-700 dark:hover:bg-indigo-700 transition shadow-md">
                            {editId ? 'Simpan' : 'Tambah'}
                        </button>
                    </div>
                </div>
            </Modal>

            <ConfirmDialog
                isOpen={!!deleteId}
                onClose={() => setDeleteId(null)}
                onConfirm={handleDelete}
                title="Hapus Sertifikat"
                message="Apakah Anda yakin ingin menghapus sertifikat ini?"
            />

            <SbuAdminModal
                isOpen={sbuAdminOpen}
                onClose={() => setSbuAdminOpen(false)}
                sbuType={sbuAdminType}
                menuConfig={sbuAdminMenuConfig}
            />
        </div>
    );
};

export default CertificatesPage;
