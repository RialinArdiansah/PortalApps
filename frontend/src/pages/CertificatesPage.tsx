import { useEffect, useState } from 'react';
import { useAppDispatch, useAppSelector } from '@/app/hooks';
import { fetchCertificates, createCertificate, updateCertificate, deleteCertificate } from '@/features/certificates/certificatesSlice';
import { Modal } from '@/components/common/Modal';
import { ConfirmDialog } from '@/components/common/ConfirmDialog';
import { SbuAdminModal } from '@/components/certificates/SbuAdminModal';
import type { SbuType } from '@/types';

const CERT_TYPE_MAP: Record<string, SbuType> = {
    'SBU Konstruksi': 'konstruksi',
    'SKK Konstruksi': 'skk',
    'SBU Konsultan': 'konsultan',
    'Dokumen SMAP': 'smap',
    'Akun SIMPK dan Alat': 'simpk',
    'Notaris': 'notaris',
};

const PROTECTED_NAMES = new Set(Object.keys(CERT_TYPE_MAP));

const CertificatesPage = () => {
    const dispatch = useAppDispatch();
    const { list, status } = useAppSelector((s) => s.certificates);

    const [modalOpen, setModalOpen] = useState(false);
    const [editId, setEditId] = useState<string | null>(null);
    const [name, setName] = useState('');
    const [deleteId, setDeleteId] = useState<string | null>(null);

    const [sbuAdminOpen, setSbuAdminOpen] = useState(false);
    const [sbuAdminType, setSbuAdminType] = useState<SbuType>('konstruksi');

    useEffect(() => {
        if (status === 'idle') dispatch(fetchCertificates());
    }, [status, dispatch]);

    const openCreate = () => { setEditId(null); setName(''); setModalOpen(true); };
    const openEdit = (id: string, n: string) => { setEditId(id); setName(n); setModalOpen(true); };
    const openSbuAdmin = (type: SbuType) => { setSbuAdminType(type); setSbuAdminOpen(true); };

    const handleSubmit = async () => {
        if (!name.trim()) return;
        if (editId) {
            await dispatch(updateCertificate({ id: editId, name }));
        } else {
            await dispatch(createCertificate({ name }));
        }
        setModalOpen(false);
    };

    const handleDelete = async () => {
        if (!deleteId) return;
        await dispatch(deleteCertificate(deleteId));
        setDeleteId(null);
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
                    const sbuType = CERT_TYPE_MAP[cert.name];
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
                                {isAdvanced ? 'Kelola asosiasi, klasifikasi, dan biaya' : cert.subMenus.length > 0 ? cert.subMenus.join(', ') : 'Tidak ada sub-menu'}
                            </p>

                            <div className="flex flex-wrap gap-2">
                                {isAdvanced ? (
                                    <button
                                        onClick={() => openSbuAdmin(sbuType)}
                                        className="flex-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition"
                                    >
                                        Kelola Menu
                                    </button>
                                ) : (
                                    <button
                                        onClick={() => openEdit(cert.id, cert.name)}
                                        className="flex-1 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-gray-200 dark:hover:bg-slate-600 transition"
                                    >
                                        Edit
                                    </button>
                                )}

                                {isAdvanced && (
                                    <button
                                        onClick={() => openEdit(cert.id, cert.name)}
                                        className="bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-gray-200 dark:hover:bg-slate-600 transition"
                                    >
                                        Edit Nama
                                    </button>
                                )}

                                {!PROTECTED_NAMES.has(cert.name) && (
                                    <button
                                        onClick={() => setDeleteId(cert.id)}
                                        className="bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-red-100 dark:hover:bg-red-900/50 transition"
                                    >
                                        Hapus
                                    </button>
                                )}
                            </div>
                        </div>
                    );
                })}
            </div>

            {/* Create / Edit Name Modal */}
            <Modal isOpen={modalOpen} onClose={() => setModalOpen(false)} title={editId ? 'Edit Sertifikat' : 'Tambah Sertifikat'}>
                <div className="space-y-4">
                    <div>
                        <label className="block text-gray-700 dark:text-slate-300 font-medium mb-2">Nama Sertifikat</label>
                        <input
                            type="text"
                            value={name}
                            onChange={(e) => setName(e.target.value)}
                            className="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"
                            placeholder="Masukkan nama sertifikat"
                            onKeyDown={(e) => e.key === 'Enter' && handleSubmit()}
                        />
                    </div>
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
            />
        </div>
    );
};

export default CertificatesPage;
