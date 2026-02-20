import { useState, useEffect, useMemo, useCallback } from 'react';
import { useAppSelector, useAppDispatch } from '@/app/hooks';
import { fetchCertificates } from '@/features/certificates/certificatesSlice';
import { Modal } from '@/components/common/Modal';
import { formatCurrency } from '@/utils/formatters';
import type { SbuType, SbuData, KlasifikasiData, BiayaData } from '@/types';

// ---------------------------------------------------------------------------
// Types
// ---------------------------------------------------------------------------
type TabName = 'klasifikasi' | 'kualifikasi' | 'biayaSetor' | 'biayaLainnya';

interface BiayaEditForm {
    id: string | null;
    name: string;
    biaya: number;
    category: TabName;
}

interface Props {
    isOpen: boolean;
    onClose: () => void;
    sbuType: SbuType;
}

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------
const deepClone = <T,>(d: T): T => JSON.parse(JSON.stringify(d));
const genId = () => `id-${Date.now()}-${Math.floor(Math.random() * 10000)}`;

const titleMap: Record<SbuType, string> = {
    konstruksi: 'SBU Konstruksi',
    konsultan: 'SBU Konsultan',
    skk: 'SKK Konstruksi',
    smap: 'Dokumen SMAP',
    simpk: 'Akun SIMPK dan Alat',
    notaris: 'Notaris',
};

// Shared input classes
const inputCls = "w-full px-4 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition";
const inputSmCls = "flex-grow px-3 py-2 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition";

// ---------------------------------------------------------------------------
// Component
// ---------------------------------------------------------------------------
export const SbuAdminModal = ({ isOpen, onClose, sbuType }: Props) => {
    const dispatch = useAppDispatch();
    const certs = useAppSelector((s) => s.certificates);

    const [tempSubs, setTempSubs] = useState<SbuData[]>([]);
    const [tempKlasifikasi, setTempKlasifikasi] = useState<KlasifikasiData[]>([]);
    const [tempKualifikasi, setTempKualifikasi] = useState<BiayaData[]>([]);
    const [tempBiayaSetor, setTempBiayaSetor] = useState<BiayaData[]>([]);
    const [tempBiayaLainnya, setTempBiayaLainnya] = useState<BiayaData[]>([]);

    const [selectedSubId, setSelectedSubId] = useState('');
    const [selectedKlasifikasiId, setSelectedKlasifikasiId] = useState('');
    const [activeTab, setActiveTab] = useState<TabName>('klasifikasi');
    const [newSubName, setNewSubName] = useState('');
    const [newKlasifikasiName, setNewKlasifikasiName] = useState('');
    const [subKlasifikasiText, setSubKlasifikasiText] = useState('');
    const [biayaEditForm, setBiayaEditForm] = useState<BiayaEditForm | null>(null);
    const [saving, setSaving] = useState(false);
    const [toast, setToast] = useState('');

    const hasAsosiasi = sbuType === 'konstruksi' || sbuType === 'skk';
    const hasKlasifikasiTab = sbuType === 'konstruksi' || sbuType === 'konsultan' || sbuType === 'skk';

    const getDataSources = useCallback(() => {
        let subs: SbuData[] = [];
        let klas: KlasifikasiData[] = [];
        let kual: BiayaData[] = [];
        let bs: BiayaData[] = [];
        let bl: BiayaData[] = [];

        switch (sbuType) {
            case 'konstruksi':
                subs = deepClone(certs.sbuKonstruksiData);
                klas = deepClone(certs.konstruksiKlasifikasiData);
                break;
            case 'konsultan':
                subs = deepClone(certs.sbuKonsultanData);
                klas = deepClone(certs.konsultanKlasifikasiData);
                kual = deepClone(certs.konsultanKualifikasiData);
                bs = deepClone(certs.konsultanBiayaSetorData);
                bl = deepClone(certs.konsultanBiayaLainnyaData);
                break;
            case 'skk':
                subs = deepClone(certs.skkKonstruksiData);
                klas = deepClone(certs.skkKlasifikasiData);
                kual = deepClone(certs.skkKualifikasiData);
                bs = deepClone(certs.skkBiayaSetorData);
                bl = deepClone(certs.skkBiayaLainnyaData);
                break;
            case 'smap':
                bs = deepClone(certs.smapBiayaSetorData);
                break;
            case 'simpk':
                bs = deepClone(certs.simpkBiayaSetorData);
                break;
            case 'notaris':
                kual = deepClone(certs.notarisKualifikasiData);
                bs = deepClone(certs.notarisBiayaSetorData);
                bl = deepClone(certs.notarisBiayaLainnyaData);
                break;
        }

        return { subs, klas, kual, bs, bl };
    }, [sbuType, certs]);

    useEffect(() => {
        if (!isOpen) return;
        const { subs, klas, kual, bs, bl } = getDataSources();

        if (sbuType === 'konstruksi') {
            const firstSub = subs[0];
            setSelectedSubId(firstSub?.id || '');
            if (firstSub) {
                const isGapeknas = firstSub.name.toUpperCase().includes('GAPEKNAS');
                setTempKualifikasi(deepClone(isGapeknas ? certs.gapeknasKualifikasiData : certs.p3smKualifikasiData));
                setTempBiayaSetor(deepClone(isGapeknas ? certs.gapeknasBiayaSetorData : certs.p3smBiayaSetorData));
                setTempBiayaLainnya(deepClone(isGapeknas ? certs.gapeknasBiayaLainnyaData : certs.p3smBiayaLainnyaData));
            } else {
                setTempKualifikasi([]);
                setTempBiayaSetor([]);
                setTempBiayaLainnya([]);
            }
        } else {
            setTempKualifikasi(kual);
            setTempBiayaSetor(bs);
            setTempBiayaLainnya(bl);
            setSelectedSubId(subs[0]?.id || '');
        }

        setTempSubs(subs);
        setTempKlasifikasi(klas);
        setSelectedKlasifikasiId('');
        setActiveTab(hasKlasifikasiTab ? 'klasifikasi' : (sbuType === 'notaris' ? 'kualifikasi' : 'biayaSetor'));
        setNewSubName('');
        setNewKlasifikasiName('');
        setSubKlasifikasiText('');
        setBiayaEditForm(null);
        setToast('');
    }, [isOpen, sbuType]);

    useEffect(() => {
        if (sbuType !== 'konstruksi' || !selectedSubId) return;
        const sub = tempSubs.find((s) => s.id === selectedSubId);
        if (!sub) return;
        const isGapeknas = sub.name.toUpperCase().includes('GAPEKNAS');
        setTempKualifikasi(deepClone(isGapeknas ? certs.gapeknasKualifikasiData : certs.p3smKualifikasiData));
        setTempBiayaSetor(deepClone(isGapeknas ? certs.gapeknasBiayaSetorData : certs.p3smBiayaSetorData));
        setTempBiayaLainnya(deepClone(isGapeknas ? certs.gapeknasBiayaLainnyaData : certs.p3smBiayaLainnyaData));
    }, [selectedSubId, sbuType]);

    const selectedKlasifikasi = useMemo(
        () => tempKlasifikasi.find((k) => k.id === selectedKlasifikasiId) || null,
        [tempKlasifikasi, selectedKlasifikasiId]
    );

    useEffect(() => {
        if (selectedKlasifikasi) {
            setSubKlasifikasiText(selectedKlasifikasi.subKlasifikasi.join('\n'));
        }
    }, [selectedKlasifikasiId]);

    const showToast = (msg: string) => {
        setToast(msg);
        setTimeout(() => setToast(''), 2500);
    };

    // Asosiasi CRUD
    const handleAddSub = () => {
        const name = newSubName.trim();
        if (!name) return;
        const newItem: SbuData = { id: genId(), name };
        setTempSubs([...tempSubs, newItem]);
        setNewSubName('');
        showToast(`Asosiasi "${name}" ditambahkan`);
    };

    const handleDeleteSub = () => {
        if (!selectedSubId) return;
        setTempSubs(tempSubs.filter((s) => s.id !== selectedSubId));
        setSelectedSubId('');
        showToast('Asosiasi dihapus');
    };

    // Klasifikasi CRUD
    const handleAddKlasifikasi = () => {
        const name = newKlasifikasiName.trim();
        if (!name) return;
        const newItem: KlasifikasiData = { id: genId(), name, subKlasifikasi: [], kualifikasi: [], subBidang: [] };
        setTempKlasifikasi([...tempKlasifikasi, newItem]);
        setNewKlasifikasiName('');
        showToast(`Klasifikasi "${name}" ditambahkan`);
    };

    const handleDeleteKlasifikasi = () => {
        if (!selectedKlasifikasiId) return;
        setTempKlasifikasi(tempKlasifikasi.filter((k) => k.id !== selectedKlasifikasiId));
        setSelectedKlasifikasiId('');
        showToast('Klasifikasi dihapus');
    };

    const handleSaveSubKlasifikasi = () => {
        if (!selectedKlasifikasiId) return;
        const items = subKlasifikasiText.split('\n').map((s) => s.trim()).filter(Boolean);
        setTempKlasifikasi(tempKlasifikasi.map((k) =>
            k.id === selectedKlasifikasiId ? { ...k, subKlasifikasi: items } : k
        ));
        showToast('Sub-klasifikasi disimpan');
    };

    // Biaya CRUD
    const getBiayaList = (cat: TabName): BiayaData[] => {
        if (cat === 'kualifikasi') return tempKualifikasi;
        if (cat === 'biayaSetor') return tempBiayaSetor;
        return tempBiayaLainnya;
    };

    const setBiayaList = (cat: TabName, list: BiayaData[]) => {
        if (cat === 'kualifikasi') setTempKualifikasi(list);
        else if (cat === 'biayaSetor') setTempBiayaSetor(list);
        else setTempBiayaLainnya(list);
    };

    const openBiayaForm = (cat: TabName, id: string | null = null) => {
        if (id) {
            const item = getBiayaList(cat).find((b) => b.id === id);
            if (item) setBiayaEditForm({ id, name: item.name, biaya: item.biaya, category: cat });
        } else {
            setBiayaEditForm({ id: null, name: '', biaya: 0, category: cat });
        }
    };

    const handleSaveBiaya = () => {
        if (!biayaEditForm) return;
        const { id, name, biaya, category } = biayaEditForm;
        const list = getBiayaList(category);

        if (id) {
            setBiayaList(category, list.map((b) => b.id === id ? { ...b, name, biaya } : b));
            showToast('Data diperbarui');
        } else {
            setBiayaList(category, [...list, { id: genId(), name, biaya }]);
            showToast('Data ditambahkan');
        }
        setBiayaEditForm(null);
    };

    const handleDeleteBiaya = (cat: TabName, id: string) => {
        setBiayaList(cat, getBiayaList(cat).filter((b) => b.id !== id));
        showToast('Data dihapus');
    };

    // SAVE ALL
    const handleSaveAll = async () => {
        setSaving(true);
        try {
            const body: Record<string, unknown> = { sbuType };

            if (sbuType === 'konstruksi') {
                body.sbuData = tempSubs;
                body.klasifikasiData = tempKlasifikasi;
                const sub = tempSubs.find((s) => s.id === selectedSubId);
                const isGapeknas = sub?.name.toUpperCase().includes('GAPEKNAS');
                if (isGapeknas) {
                    body.gapeknasKualifikasiData = tempKualifikasi;
                    body.gapeknasBiayaSetorData = tempBiayaSetor;
                    body.gapeknasBiayaLainnyaData = tempBiayaLainnya;
                } else {
                    body.p3smKualifikasiData = tempKualifikasi;
                    body.p3smBiayaSetorData = tempBiayaSetor;
                    body.p3smBiayaLainnyaData = tempBiayaLainnya;
                }
            } else if (sbuType === 'konsultan') {
                body.sbuData = tempSubs;
                body.klasifikasiData = tempKlasifikasi;
                body.kualifikasiData = tempKualifikasi;
                body.biayaSetorData = tempBiayaSetor;
                body.biayaLainnyaData = tempBiayaLainnya;
            } else if (sbuType === 'skk') {
                body.sbuData = tempSubs;
                body.klasifikasiData = tempKlasifikasi;
                body.kualifikasiData = tempKualifikasi;
                body.biayaSetorData = tempBiayaSetor;
                body.biayaLainnyaData = tempBiayaLainnya;
            } else if (sbuType === 'smap') {
                body.biayaSetorData = tempBiayaSetor;
            } else if (sbuType === 'simpk') {
                body.biayaSetorData = tempBiayaSetor;
            } else if (sbuType === 'notaris') {
                body.kualifikasiData = tempKualifikasi;
                body.biayaSetorData = tempBiayaSetor;
                body.biayaLainnyaData = tempBiayaLainnya;
            }

            await fetch('/api/certificates/reference-data', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(body),
            });

            await dispatch(fetchCertificates());
            showToast('Semua data berhasil disimpan!');
            setTimeout(() => onClose(), 800);
        } catch {
            showToast('Gagal menyimpan data');
        } finally {
            setSaving(false);
        }
    };

    // Tab button helper
    const TabBtn = ({ label, tab }: { label: string; tab: TabName }) => (
        <button
            type="button"
            onClick={() => setActiveTab(tab)}
            className={`px-4 py-2 text-sm font-medium rounded-t-lg transition-colors ${activeTab === tab
                ? 'bg-primary-600 dark:bg-indigo-600 text-white'
                : 'bg-gray-200 dark:bg-slate-700 text-gray-600 dark:text-slate-400 hover:bg-gray-300 dark:hover:bg-slate-600'
                }`}
        >
            {label}
        </button>
    );

    // Biaya table
    const BiayaTable = ({ cat, title }: { cat: TabName; title: string }) => {
        const list = getBiayaList(cat);
        const selectedSub = tempSubs.find((s) => s.id === selectedSubId);
        const needsSub = hasAsosiasi && !selectedSubId;

        if (needsSub) {
            return <p className="text-center text-gray-500 dark:text-slate-400 py-6">Pilih Asosiasi terlebih dahulu untuk mengelola {title}.</p>;
        }

        return (
            <div>
                <div className="flex justify-between items-center mb-3">
                    <p className="text-sm text-gray-500 dark:text-slate-400">
                        Data {title} untuk <strong className="text-gray-700 dark:text-white">{selectedSub?.name || titleMap[sbuType]}</strong>
                    </p>
                    <button
                        type="button"
                        onClick={() => openBiayaForm(cat)}
                        className="bg-primary-600 dark:bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-primary-700 dark:hover:bg-indigo-700 transition"
                    >
                        Tambah {title}
                    </button>
                </div>
                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead className="bg-gray-50 dark:bg-slate-700/50">
                            <tr>
                                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">Nama & Biaya</th>
                                <th className="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                            {list.length === 0 ? (
                                <tr><td colSpan={2} className="text-center py-6 text-gray-400 dark:text-slate-500">Belum ada data</td></tr>
                            ) : list.map((item) => (
                                <tr key={item.id} className="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                                    <td className="px-4 py-3">
                                        <div className="font-medium text-gray-900 dark:text-white text-sm">{item.name}</div>
                                        <div className="text-gray-500 dark:text-slate-400 text-xs">{formatCurrency(item.biaya)}</div>
                                    </td>
                                    <td className="px-4 py-3 text-right space-x-3">
                                        <button onClick={() => openBiayaForm(cat, item.id)} className="text-primary-600 dark:text-indigo-400 hover:text-primary-800 dark:hover:text-indigo-300 text-sm font-medium">Edit</button>
                                        <button onClick={() => handleDeleteBiaya(cat, item.id)} className="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-sm font-medium">Hapus</button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        );
    };

    const tabs = useMemo(() => {
        const t: { label: string; tab: TabName }[] = [];
        if (hasKlasifikasiTab) t.push({ label: 'Klasifikasi', tab: 'klasifikasi' });
        if (sbuType !== 'smap' && sbuType !== 'simpk') t.push({ label: sbuType === 'skk' ? 'Jenjang' : 'Kualifikasi', tab: 'kualifikasi' });
        t.push({ label: 'Biaya Setor', tab: 'biayaSetor' });
        if (sbuType !== 'smap' && sbuType !== 'simpk') t.push({ label: 'Biaya Lainnya', tab: 'biayaLainnya' });
        return t;
    }, [sbuType, hasKlasifikasiTab]);

    // Render
    return (
        <Modal isOpen={isOpen} onClose={onClose} title={`Kelola Menu ${titleMap[sbuType]}`} maxWidth="max-w-3xl">
            <div className="space-y-5">
                {/* Toast */}
                {toast && (
                    <div className="bg-green-50 dark:bg-emerald-900/20 border border-green-200 dark:border-emerald-700 text-green-700 dark:text-emerald-400 text-sm px-4 py-2 rounded-xl text-center animate-in">
                        {toast}
                    </div>
                )}

                {/* Asosiasi Section */}
                {hasAsosiasi && (
                    <div>
                        <div className="flex justify-between items-center mb-2">
                            <label className="text-gray-700 dark:text-slate-300 font-medium">Asosiasi</label>
                            {selectedSubId && (
                                <button type="button" onClick={handleDeleteSub} className="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300" title="Hapus asosiasi">
                                    <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fillRule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clipRule="evenodd" />
                                    </svg>
                                </button>
                            )}
                        </div>
                        <select
                            value={selectedSubId}
                            onChange={(e) => setSelectedSubId(e.target.value)}
                            className={inputCls}
                        >
                            <option value="">-- Pilih Asosiasi --</option>
                            {tempSubs.map((s) => <option key={s.id} value={s.id}>{s.name}</option>)}
                        </select>
                        <div className="flex mt-2 space-x-2">
                            <input
                                type="text"
                                value={newSubName}
                                onChange={(e) => setNewSubName(e.target.value)}
                                placeholder="Nama asosiasi baru"
                                className={inputSmCls}
                                onKeyDown={(e) => e.key === 'Enter' && handleAddSub()}
                            />
                            <button type="button" onClick={handleAddSub} className="bg-primary-600 dark:bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-primary-700 dark:hover:bg-indigo-700 transition">
                                Tambah
                            </button>
                        </div>
                    </div>
                )}

                {/* Tabs */}
                <div>
                    <div className="border-b border-gray-200 dark:border-slate-700">
                        <nav className="-mb-px flex space-x-2 overflow-x-auto">
                            {tabs.map((t) => <TabBtn key={t.tab} label={t.label} tab={t.tab} />)}
                        </nav>
                    </div>

                    <div className="pt-4">
                        {/* Klasifikasi Tab */}
                        {activeTab === 'klasifikasi' && hasKlasifikasiTab && (
                            <div className="space-y-4">
                                <div className="flex justify-between items-center mb-2">
                                    <label className="text-gray-700 dark:text-slate-300 font-medium">Klasifikasi</label>
                                    {selectedKlasifikasiId && (
                                        <button type="button" onClick={handleDeleteKlasifikasi} className="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300" title="Hapus klasifikasi">
                                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fillRule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clipRule="evenodd" />
                                            </svg>
                                        </button>
                                    )}
                                </div>
                                <select
                                    value={selectedKlasifikasiId}
                                    onChange={(e) => setSelectedKlasifikasiId(e.target.value)}
                                    className={inputCls}
                                >
                                    <option value="">-- Pilih Klasifikasi --</option>
                                    {tempKlasifikasi.map((k) => <option key={k.id} value={k.id}>{k.name}</option>)}
                                </select>
                                <div className="flex space-x-2">
                                    <input
                                        type="text"
                                        value={newKlasifikasiName}
                                        onChange={(e) => setNewKlasifikasiName(e.target.value)}
                                        placeholder="Nama klasifikasi baru"
                                        className={inputSmCls}
                                        onKeyDown={(e) => e.key === 'Enter' && handleAddKlasifikasi()}
                                    />
                                    <button type="button" onClick={handleAddKlasifikasi} className="bg-primary-600 dark:bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-primary-700 dark:hover:bg-indigo-700 transition">
                                        Tambah
                                    </button>
                                </div>

                                {/* Sub-klasifikasi */}
                                {selectedKlasifikasi && (
                                    <div className="mt-4 space-y-3">
                                        <label className="block text-gray-700 dark:text-slate-300 font-medium">Sub-klasifikasi (satu per baris)</label>
                                        <textarea
                                            value={subKlasifikasiText}
                                            onChange={(e) => setSubKlasifikasiText(e.target.value)}
                                            className="w-full h-32 p-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"
                                        />
                                        <button type="button" onClick={handleSaveSubKlasifikasi} className="w-full bg-primary-600 dark:bg-indigo-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-primary-700 dark:hover:bg-indigo-700 transition">
                                            Simpan Sub-klasifikasi
                                        </button>
                                    </div>
                                )}
                            </div>
                        )}

                        {activeTab === 'kualifikasi' && <BiayaTable cat="kualifikasi" title={sbuType === 'skk' ? 'Jenjang' : 'Kualifikasi'} />}
                        {activeTab === 'biayaSetor' && <BiayaTable cat="biayaSetor" title="Biaya Setor" />}
                        {activeTab === 'biayaLainnya' && <BiayaTable cat="biayaLainnya" title="Biaya Lainnya" />}
                    </div>
                </div>

                {/* Biaya Edit Form (inline) */}
                {biayaEditForm && (
                    <div className="bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl p-4 space-y-3 animate-in">
                        <h4 className="font-semibold text-gray-700 dark:text-white">{biayaEditForm.id ? 'Edit' : 'Tambah'} Data</h4>
                        <div>
                            <label className="block text-gray-600 dark:text-slate-400 text-sm mb-1">Nama</label>
                            <input
                                type="text"
                                value={biayaEditForm.name}
                                onChange={(e) => setBiayaEditForm({ ...biayaEditForm, name: e.target.value })}
                                className={inputSmCls}
                            />
                        </div>
                        <div>
                            <label className="block text-gray-600 dark:text-slate-400 text-sm mb-1">Biaya (Rp)</label>
                            <input
                                type="number"
                                value={biayaEditForm.biaya}
                                onChange={(e) => setBiayaEditForm({ ...biayaEditForm, biaya: Number(e.target.value) })}
                                className={inputSmCls}
                                min={0}
                            />
                        </div>
                        <div className="flex justify-end gap-2">
                            <button type="button" onClick={() => setBiayaEditForm(null)} className="px-4 py-2 rounded-xl text-sm font-semibold bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200 hover:bg-gray-300 dark:hover:bg-slate-500 transition">
                                Batal
                            </button>
                            <button type="button" onClick={handleSaveBiaya} className="px-4 py-2 rounded-xl text-sm font-semibold bg-primary-600 dark:bg-indigo-600 text-white hover:bg-primary-700 dark:hover:bg-indigo-700 transition">
                                Simpan
                            </button>
                        </div>
                    </div>
                )}

                {/* Footer */}
                <div className="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-slate-700">
                    <button
                        type="button"
                        onClick={onClose}
                        className="px-6 py-3 rounded-xl font-semibold bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200 hover:bg-gray-300 dark:hover:bg-slate-500 transition"
                    >
                        Tutup
                    </button>
                    <button
                        type="button"
                        onClick={handleSaveAll}
                        disabled={saving}
                        className="px-6 py-3 rounded-xl font-semibold bg-primary-600 dark:bg-indigo-600 text-white hover:bg-primary-700 dark:hover:bg-indigo-700 transition shadow-md disabled:opacity-50"
                    >
                        {saving ? 'Menyimpan...' : 'Simpan Semua'}
                    </button>
                </div>
            </div>
        </Modal>
    );
};
