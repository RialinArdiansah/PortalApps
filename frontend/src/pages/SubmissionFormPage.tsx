import { useState, useEffect, useMemo } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAppSelector, useAppDispatch } from '@/app/hooks';
import { fetchCertificates } from '@/features/certificates/certificatesSlice';
import { fetchMarketing } from '@/features/marketing/marketingSlice';
import { createSubmission } from '@/features/submissions/submissionsSlice';
import { formatCurrency, capitalizeWords } from '@/utils/formatters';
import { calculateKeuntungan } from '@/utils/calculations';
import type { SbuType, BiayaData, SbuData, KlasifikasiData, Certificate } from '@/types';

const CERT_CARDS: { certName: string; icon: string; gradient: string; desc: string }[] = [
    { certName: 'SBU Konstruksi', icon: '🏗️', gradient: 'from-indigo-500 to-indigo-700', desc: 'Sertifikat Badan Usaha untuk jasa konstruksi' },
    { certName: 'SKK Konstruksi', icon: '👷', gradient: 'from-blue-500 to-blue-700', desc: 'Sertifikat Kompetensi Kerja konstruksi' },
    { certName: 'SBU Konsultan', icon: '📐', gradient: 'from-violet-500 to-violet-700', desc: 'Sertifikat Badan Usaha untuk jasa konsultansi' },
    { certName: 'Dokumen SMAP', icon: '📋', gradient: 'from-emerald-500 to-emerald-700', desc: 'Sistem Manajemen Anti Penyuapan' },
    { certName: 'Akun SIMPK dan Alat', icon: '🔧', gradient: 'from-amber-500 to-amber-700', desc: 'Sistem Informasi Manajemen dan Pengawasan Konstruksi' },
    { certName: 'Notaris', icon: '⚖️', gradient: 'from-rose-500 to-rose-700', desc: 'Layanan sertifikasi notaris' },
];

const certToSbuMap: Record<string, SbuType> = {
    'SBU Konstruksi': 'konstruksi',
    'SKK Konstruksi': 'skk',
    'SBU Konsultan': 'konsultan',
    'Dokumen SMAP': 'smap',
    'Akun SIMPK dan Alat': 'simpk',
    'Notaris': 'notaris',
};

// Shared input classes
const inputCls = "w-full px-4 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition";

export const SubmissionFormPage = () => {
    const dispatch = useAppDispatch();
    const navigate = useNavigate();
    const { user } = useAppSelector((s) => s.auth);
    const certs = useAppSelector((s) => s.certificates);
    const { list: marketingNames } = useAppSelector((s) => s.marketing);

    useEffect(() => {
        dispatch(fetchCertificates());
        dispatch(fetchMarketing());
    }, [dispatch]);

    // Step 1: Certificate Selection
    const [selectedCert, setSelectedCert] = useState<Certificate | null>(null);

    // Step 2: Form state
    const [companyName, setCompanyName] = useState('');
    const [marketingName, setMarketingName] = useState('');
    const [inputDate, setInputDate] = useState(new Date().toISOString().split('T')[0]);
    const [sbuType, setSbuType] = useState<SbuType | ''>('');

    const [selectedSubId, setSelectedSubId] = useState('');
    const [selectedKlasifikasiId, setSelectedKlasifikasiId] = useState('');
    const [selectedSubKlasifikasi, setSelectedSubKlasifikasi] = useState('');
    const [selectedKualifikasiId, setSelectedKualifikasiId] = useState('');
    const [selectedBiayaLainnyaId, setSelectedBiayaLainnyaId] = useState('');
    const [biayaSetorKantor, setBiayaSetorKantor] = useState(0);

    useEffect(() => {
        if (selectedCert) {
            setSbuType(certToSbuMap[selectedCert.name] || '');
        } else {
            setSbuType('');
        }
        setSelectedSubId('');
        setSelectedKlasifikasiId('');
        setSelectedSubKlasifikasi('');
        setSelectedKualifikasiId('');
        setSelectedBiayaLainnyaId('');
        setBiayaSetorKantor(0);
    }, [selectedCert]);

    const { subOptions, klasifikasiOptions, subKlasifikasiOptions, kualifikasiOptions, biayaLainnyaOptions, biayaSetorOptions } = useMemo(() => {
        let sub: SbuData[] = [];
        let klas: KlasifikasiData[] = [];
        let kual: BiayaData[] = [];
        let bl: BiayaData[] = [];
        let bs: BiayaData[] = [];

        switch (sbuType) {
            case 'konstruksi':
                sub = certs.sbuKonstruksiData;
                klas = certs.konstruksiKlasifikasiData;
                if (selectedSubId === 'p3sm-id') {
                    kual = certs.p3smKualifikasiData; bl = certs.p3smBiayaLainnyaData; bs = certs.p3smBiayaSetorData;
                } else if (selectedSubId === 'gapeknas-id') {
                    kual = certs.gapeknasKualifikasiData; bl = certs.gapeknasBiayaLainnyaData; bs = certs.gapeknasBiayaSetorData;
                }
                break;
            case 'konsultan':
                sub = certs.sbuKonsultanData; klas = certs.konsultanKlasifikasiData;
                kual = certs.konsultanKualifikasiData; bl = certs.konsultanBiayaLainnyaData; bs = certs.konsultanBiayaSetorData;
                break;
            case 'skk':
                sub = certs.skkKonstruksiData; klas = certs.skkKlasifikasiData;
                kual = certs.skkKualifikasiData; bl = certs.skkBiayaLainnyaData; bs = certs.skkBiayaSetorData;
                break;
            case 'smap': bs = certs.smapBiayaSetorData; break;
            case 'simpk': bs = certs.simpkBiayaSetorData; break;
            case 'notaris':
                kual = certs.notarisKualifikasiData; bl = certs.notarisBiayaLainnyaData; bs = certs.notarisBiayaSetorData;
                break;
        }

        const selectedKlas = klas.find((k) => k.id === selectedKlasifikasiId);
        return {
            subOptions: sub, klasifikasiOptions: klas,
            subKlasifikasiOptions: selectedKlas?.subKlasifikasi || [],
            kualifikasiOptions: kual, biayaLainnyaOptions: bl, biayaSetorOptions: bs,
        };
    }, [sbuType, selectedSubId, selectedKlasifikasiId, certs]);

    const selectedKualifikasi = kualifikasiOptions.find((k) => k.id === selectedKualifikasiId) || null;
    const selectedBiayaLainnya = biayaLainnyaOptions.find((b) => b.id === selectedBiayaLainnyaId) || null;

    const hasSubOptions = sbuType === 'konstruksi' || sbuType === 'konsultan' || sbuType === 'skk';
    const hasKlasifikasi = hasSubOptions;
    const hasKualifikasi = sbuType !== 'smap' && sbuType !== 'simpk';

    useEffect(() => {
        if (selectedKualifikasi && biayaSetorOptions.length > 0) {
            const matchingSetor = biayaSetorOptions.find((bs) => bs.name === selectedKualifikasi.name);
            if (matchingSetor) {
                setBiayaSetorKantor(matchingSetor.biaya);
            }
        } else if (!selectedKualifikasiId) {
            if (biayaSetorOptions.length === 1 && !hasKualifikasi) {
                setBiayaSetorKantor(biayaSetorOptions[0].biaya);
            } else {
                setBiayaSetorKantor(0);
            }
        }
    }, [selectedKualifikasiId, selectedKualifikasi, biayaSetorOptions, hasKualifikasi]);

    const keuntungan = calculateKeuntungan(
        biayaSetorKantor,
        selectedKualifikasi?.biaya || 0,
        selectedBiayaLainnya?.biaya || 0
    );

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!selectedCert) return;
        const selectedSub = subOptions.find((s) => s.id === selectedSubId) || null;
        const selectedKlas = klasifikasiOptions.find((k) => k.id === selectedKlasifikasiId) || null;

        await dispatch(createSubmission({
            companyName: capitalizeWords(companyName),
            marketingName,
            inputDate,
            submittedById: user?.id || '',
            certificateType: selectedCert.name,
            sbuType: sbuType as SbuType,
            selectedSub,
            selectedKlasifikasi: selectedKlas,
            selectedSubKlasifikasi: selectedSubKlasifikasi || null,
            selectedKualifikasi,
            selectedBiayaLainnya,
            biayaSetorKantor,
            keuntungan,
        }));
        navigate('/submissions');
    };

    const handleBack = () => {
        setSelectedCert(null);
        setCompanyName('');
        setMarketingName('');
    };

    // ═════════════════════════════════════════════════════════════════════
    // STEP 1: Certificate Selection Portal
    // ═════════════════════════════════════════════════════════════════════
    if (!selectedCert) {
        return (
            <div>
                <h1 className="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white mb-2">Portal Sertifikat</h1>
                <p className="text-gray-500 dark:text-slate-400 mb-8">Pilih jenis sertifikat untuk memulai input data</p>

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    {CERT_CARDS.map((card) => {
                        const cert = certs.list.find((c) => c.name === card.certName);
                        if (!cert) return null;

                        return (
                            <button
                                key={card.certName}
                                onClick={() => setSelectedCert(cert)}
                                className={`group bg-gradient-to-br ${card.gradient} rounded-2xl p-5 sm:p-6 text-left text-white shadow-md hover:shadow-xl hover:scale-[1.03] transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-white/30`}
                            >
                                <div className="text-4xl mb-4 group-hover:scale-110 transition-transform duration-300">
                                    {card.icon}
                                </div>
                                <h3 className="text-lg font-bold mb-2">{cert.name}</h3>
                                <p className="text-sm text-white/80 leading-relaxed">{card.desc}</p>
                                <div className="mt-4 flex items-center gap-2 text-sm font-medium text-white/70 group-hover:text-white transition">
                                    <span>Mulai Input</span>
                                    <svg className="h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </button>
                        );
                    })}
                </div>
            </div>
        );
    }

    // ═════════════════════════════════════════════════════════════════════
    // STEP 2: Submission Form
    // ═════════════════════════════════════════════════════════════════════
    return (
        <div className="max-w-3xl mx-auto">
            {/* Back button + Title */}
            <div className="flex items-center gap-4 mb-6">
                <button
                    onClick={handleBack}
                    className="flex items-center gap-2 text-gray-500 dark:text-slate-400 hover:text-gray-800 dark:hover:text-white transition font-medium"
                >
                    <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                        <path strokeLinecap="round" strokeLinejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </button>
                <div>
                    <h1 className="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Input {selectedCert.name}</h1>
                    <p className="text-gray-500 dark:text-slate-400">Masukkan data sertifikasi baru</p>
                </div>
            </div>

            <form onSubmit={handleSubmit} className="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-5 sm:p-6 space-y-6 transition-colors">
                {/* Section 1: Informasi Perusahaan */}
                <fieldset className="space-y-4">
                    <legend className="text-lg font-semibold text-gray-800 dark:text-white border-b border-gray-200 dark:border-slate-700 pb-2 mb-4 w-full">Informasi Perusahaan</legend>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Nama Perusahaan</label>
                            <input type="text" value={companyName} onChange={(e) => setCompanyName(e.target.value)}
                                className={inputCls} placeholder="PT. / CV." required />
                        </div>
                        <div>
                            <label className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Marketing</label>
                            <select value={marketingName} onChange={(e) => setMarketingName(e.target.value)}
                                className={inputCls} required>
                                <option value="">Pilih Marketing</option>
                                {marketingNames.map((m) => <option key={m.id} value={m.name}>{m.name}</option>)}
                            </select>
                        </div>
                    </div>
                    <div>
                        <label className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Tanggal Input</label>
                        <input type="date" value={inputDate} onChange={(e) => setInputDate(e.target.value)}
                            className={inputCls} required />
                    </div>
                </fieldset>

                {/* Section 2: Detail Sertifikat */}
                {(hasSubOptions || hasKlasifikasi) && (
                    <fieldset className="space-y-4">
                        <legend className="text-lg font-semibold text-gray-800 dark:text-white border-b border-gray-200 dark:border-slate-700 pb-2 mb-4 w-full">Detail Sertifikat</legend>

                        {hasSubOptions && subOptions.length > 0 && (
                            <div>
                                <label className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Asosiasi</label>
                                <select value={selectedSubId} onChange={(e) => { setSelectedSubId(e.target.value); setSelectedKlasifikasiId(''); setSelectedSubKlasifikasi(''); }}
                                    className={inputCls} required>
                                    <option value="">Pilih Asosiasi</option>
                                    {subOptions.map((s) => <option key={s.id} value={s.id}>{s.name}</option>)}
                                </select>
                            </div>
                        )}

                        {hasKlasifikasi && klasifikasiOptions.length > 0 && selectedSubId && (
                            <div>
                                <label className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Klasifikasi</label>
                                <select value={selectedKlasifikasiId} onChange={(e) => { setSelectedKlasifikasiId(e.target.value); setSelectedSubKlasifikasi(''); }}
                                    className={inputCls} required>
                                    <option value="">Pilih Klasifikasi</option>
                                    {klasifikasiOptions.map((k) => <option key={k.id} value={k.id}>{k.name}</option>)}
                                </select>
                            </div>
                        )}

                        {subKlasifikasiOptions.length > 0 && selectedKlasifikasiId && (
                            <div>
                                <label className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Sub Klasifikasi</label>
                                <select value={selectedSubKlasifikasi} onChange={(e) => setSelectedSubKlasifikasi(e.target.value)}
                                    className={inputCls} required>
                                    <option value="">Pilih Sub Klasifikasi</option>
                                    {subKlasifikasiOptions.map((s, i) => <option key={i} value={s}>{s}</option>)}
                                </select>
                            </div>
                        )}
                    </fieldset>
                )}

                {/* Section 3: Biaya */}
                {sbuType && (
                    <fieldset className="space-y-4">
                        <legend className="text-lg font-semibold text-gray-800 dark:text-white border-b border-gray-200 dark:border-slate-700 pb-2 mb-4 w-full">Biaya</legend>

                        {hasKualifikasi && kualifikasiOptions.length > 0 && (
                            <div>
                                <label className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Kualifikasi / Biaya Dasar</label>
                                <select value={selectedKualifikasiId} onChange={(e) => setSelectedKualifikasiId(e.target.value)}
                                    className={inputCls} required>
                                    <option value="">Pilih Kualifikasi</option>
                                    {kualifikasiOptions.map((k) => (
                                        <option key={k.id} value={k.id}>{k.name} — {formatCurrency(k.biaya)}</option>
                                    ))}
                                </select>
                            </div>
                        )}

                        {biayaLainnyaOptions.length > 0 && (
                            <div>
                                <label className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Biaya Lainnya</label>
                                <select value={selectedBiayaLainnyaId} onChange={(e) => setSelectedBiayaLainnyaId(e.target.value)}
                                    className={inputCls}>
                                    <option value="">Tidak ada</option>
                                    {biayaLainnyaOptions.map((b) => (
                                        <option key={b.id} value={b.id}>{b.name} — {formatCurrency(b.biaya)}</option>
                                    ))}
                                </select>
                            </div>
                        )}

                        <div>
                            <label className="block text-gray-700 dark:text-slate-300 font-medium mb-1">
                                Biaya Setor Kantor
                                {biayaSetorKantor > 0 && selectedKualifikasi && (
                                    <span className="ml-2 text-xs text-emerald-600 dark:text-emerald-400 font-normal">✓ Otomatis terisi</span>
                                )}
                            </label>
                            <input type="number" value={biayaSetorKantor} onChange={(e) => setBiayaSetorKantor(Number(e.target.value))}
                                className={inputCls} min={0} required />
                        </div>

                        {/* Summary Card */}
                        <div className={`p-4 rounded-xl border ${keuntungan >= 0 ? 'bg-green-50 dark:bg-emerald-900/20 border-green-200 dark:border-emerald-700' : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-700'}`}>
                            <div className="grid grid-cols-2 gap-4 text-sm">
                                <div className="text-gray-600 dark:text-slate-400">Biaya Setor Kantor</div>
                                <div className="text-right font-semibold text-gray-800 dark:text-white">{formatCurrency(biayaSetorKantor)}</div>
                                <div className="text-gray-600 dark:text-slate-400">Biaya Kualifikasi</div>
                                <div className="text-right font-semibold text-gray-800 dark:text-white">{formatCurrency(selectedKualifikasi?.biaya || 0)}</div>
                                <div className="text-gray-600 dark:text-slate-400">Biaya Lainnya</div>
                                <div className="text-right font-semibold text-gray-800 dark:text-white">{formatCurrency(selectedBiayaLainnya?.biaya || 0)}</div>
                                <div className="text-gray-700 dark:text-slate-300 font-bold border-t border-gray-200 dark:border-slate-700 pt-2">Keuntungan</div>
                                <div className={`text-right font-bold border-t border-gray-200 dark:border-slate-700 pt-2 ${keuntungan >= 0 ? 'text-green-700 dark:text-emerald-400' : 'text-red-700 dark:text-red-400'}`}>
                                    {formatCurrency(keuntungan)}
                                </div>
                            </div>
                        </div>
                    </fieldset>
                )}

                <div className="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-slate-700">
                    <button type="button" onClick={handleBack} className="px-6 py-3 rounded-xl font-semibold bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200 hover:bg-gray-300 dark:hover:bg-slate-500 transition">Batal</button>
                    <button type="submit" className="px-6 py-3 rounded-xl font-semibold bg-primary-600 dark:bg-indigo-600 text-white hover:bg-primary-700 dark:hover:bg-indigo-700 transition shadow-md">Simpan</button>
                </div>
            </form>
        </div>
    );
};
