import { useState, useEffect, useMemo } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAppSelector, useAppDispatch } from '@/app/hooks';
import { fetchCertificates } from '@/features/certificates/certificatesSlice';
import { fetchMarketing } from '@/features/marketing/marketingSlice';
import { createSubmission } from '@/features/submissions/submissionsSlice';
import { formatCurrency, capitalizeWords } from '@/utils/formatters';
import { calculateKeuntungan } from '@/utils/calculations';
import type { SbuType, BiayaData, SbuData, KlasifikasiData, Certificate, MenuConfig } from '@/types';
import { DEFAULT_MENU_CONFIG } from '@/types';

// ─── Card styles ────────────────────────────────────────────────────────────
const KNOWN_CARD_STYLES: Record<string, { icon: string; gradient: string; glow: string; desc: string }> = {
    'SBU Konstruksi': { icon: '🏗️', gradient: 'from-indigo-500 via-indigo-600 to-indigo-800', glow: 'hover:shadow-indigo-500/30', desc: 'Sertifikat Badan Usaha untuk jasa konstruksi' },
    'SKK Konstruksi': { icon: '👷', gradient: 'from-blue-500 via-blue-600 to-blue-800', glow: 'hover:shadow-blue-500/30', desc: 'Sertifikat Kompetensi Kerja konstruksi' },
    'SBU Konsultan': { icon: '📐', gradient: 'from-violet-500 via-violet-600 to-violet-800', glow: 'hover:shadow-violet-500/30', desc: 'Sertifikat Badan Usaha untuk jasa konsultansi' },
    'Dokumen SMAP': { icon: '📋', gradient: 'from-emerald-500 via-emerald-600 to-emerald-800', glow: 'hover:shadow-emerald-500/30', desc: 'Sistem Manajemen Anti Penyuapan' },
    'Akun SIMPK dan Alat': { icon: '🔧', gradient: 'from-amber-500 via-amber-600 to-amber-800', glow: 'hover:shadow-amber-500/30', desc: 'Sistem Informasi Manajemen Konstruksi' },
    'Notaris': { icon: '⚖️', gradient: 'from-rose-500 via-rose-600 to-rose-800', glow: 'hover:shadow-rose-500/30', desc: 'Layanan sertifikasi notaris' },
    'Sewa SKK Tenaga Ahli': { icon: '💼', gradient: 'from-cyan-500 via-cyan-600 to-cyan-800', glow: 'hover:shadow-cyan-500/30', desc: 'Sewa sertifikat kompetensi tenaga ahli' },
    'SMK3 Perusahaan (Kemenaker)': { icon: '🛡️', gradient: 'from-teal-500 via-teal-600 to-teal-800', glow: 'hover:shadow-teal-500/30', desc: 'Sistem Manajemen K3 perusahaan' },
    'AK3 Umum Kemenaker': { icon: '🎓', gradient: 'from-sky-500 via-sky-600 to-sky-800', glow: 'hover:shadow-sky-500/30', desc: 'Ahli Keselamatan dan Kesehatan Kerja' },
    'ISO Lokal': { icon: '🏅', gradient: 'from-lime-500 via-lime-600 to-lime-800', glow: 'hover:shadow-lime-500/30', desc: 'Sertifikasi ISO oleh badan lokal' },
    'ISO UAF (Americo)': { icon: '🌍', gradient: 'from-orange-500 via-orange-600 to-orange-800', glow: 'hover:shadow-orange-500/30', desc: 'Sertifikasi ISO UAF internasional' },
    'ISO KAN (P3SM)': { icon: '✨', gradient: 'from-fuchsia-500 via-fuchsia-600 to-fuchsia-800', glow: 'hover:shadow-fuchsia-500/30', desc: 'Sertifikasi ISO terakreditasi KAN' },
    'KAP Non Barcode (Alam)': { icon: '📝', gradient: 'from-pink-500 via-pink-600 to-pink-800', glow: 'hover:shadow-pink-500/30', desc: 'Klasifikasi & kualifikasi tanpa barcode' },
    'KAP Barcode Tidak Audit': { icon: '📊', gradient: 'from-purple-500 via-purple-600 to-purple-800', glow: 'hover:shadow-purple-500/30', desc: 'Klasifikasi & kualifikasi barcode non-audit' },
    'KAP Barcode P3SM (Sistem Audit)': { icon: '🔍', gradient: 'from-red-500 via-red-600 to-red-800', glow: 'hover:shadow-red-500/30', desc: 'Klasifikasi & kualifikasi barcode dengan audit' },
};
const DYN_ICONS = ['📜', '🏢', '🔑', '🎓', '⭐', '🛡️'];
const DYN_GRADIENTS = ['from-cyan-500 via-cyan-600 to-cyan-800', 'from-teal-500 via-teal-600 to-teal-800', 'from-pink-500 via-pink-600 to-pink-800', 'from-orange-500 via-orange-600 to-orange-800', 'from-sky-500 via-sky-600 to-sky-800', 'from-fuchsia-500 via-fuchsia-600 to-fuchsia-800'];
const DYN_GLOWS = ['hover:shadow-cyan-500/30', 'hover:shadow-teal-500/30', 'hover:shadow-pink-500/30', 'hover:shadow-orange-500/30', 'hover:shadow-sky-500/30', 'hover:shadow-fuchsia-500/30'];

const getCardStyle = (name: string, idx: number) =>
    KNOWN_CARD_STYLES[name] ?? {
        icon: DYN_ICONS[idx % DYN_ICONS.length],
        gradient: DYN_GRADIENTS[idx % DYN_GRADIENTS.length],
        glow: DYN_GLOWS[idx % DYN_GLOWS.length],
        desc: `Sertifikasi ${name}`,
    };

// ─── Shared styles ───────────────────────────────────────────────────────────
const inputCls = 'w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm';
const labelCls = 'block text-gray-700 dark:text-slate-300 text-sm font-medium mb-1.5';

// ─── Section wrapper with colored left-border accent ─────────────────────────
const Section = ({ accent, icon, title, children }: { accent: string; icon: string; title: string; children: React.ReactNode }) => (
    <div className={`bg-white dark:bg-slate-800/70 rounded-2xl border border-gray-200 dark:border-slate-700/50 overflow-hidden shadow-sm`}>
        <div className={`flex items-center gap-3 px-5 py-4 border-b border-gray-200 dark:border-slate-700/50 border-l-4 ${accent} bg-gray-50 dark:bg-transparent`}>
            <span className="text-xl">{icon}</span>
            <h2 className="text-gray-800 dark:text-white font-semibold text-base tracking-wide">{title}</h2>
        </div>
        <div className="p-5 space-y-4">{children}</div>
    </div>
);

// ─── Rp formatted input ──────────────────────────────────────────────────────
const RpInput = ({ value, onChange, placeholder = '0', required = false, readOnly = false, badge }: {
    value: number; onChange: (v: number) => void; placeholder?: string; required?: boolean; readOnly?: boolean; badge?: React.ReactNode;
}) => (
    <div className="relative">
        <span className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-slate-400 font-semibold text-sm select-none">Rp</span>
        <input
            type="text"
            inputMode="numeric"
            readOnly={readOnly}
            value={value === 0 ? '' : new Intl.NumberFormat('id-ID').format(value)}
            onChange={e => { const raw = e.target.value.replace(/\D/g, ''); onChange(raw === '' ? 0 : Number(raw)); }}
            className={inputCls + ' pl-12 pr-4' + (readOnly ? ' opacity-70 cursor-default' : '')}
            placeholder={placeholder}
            required={required}
        />
        {badge && <div className="absolute right-3 top-1/2 -translate-y-1/2">{badge}</div>}
    </div>
);

// ─── Component ───────────────────────────────────────────────────────────────
export const SubmissionFormPage = () => {
    const dispatch = useAppDispatch();
    const navigate = useNavigate();
    const { user } = useAppSelector(s => s.auth);
    const certs = useAppSelector(s => s.certificates);
    const { list: marketingNames } = useAppSelector(s => s.marketing);

    useEffect(() => { dispatch(fetchCertificates()); dispatch(fetchMarketing()); }, [dispatch]);

    const [selectedCert, setSelectedCert] = useState<Certificate | null>(null);
    const [companyPrefix, setCompanyPrefix] = useState('PT.');
    const [companyName, setCompanyName] = useState('');
    const [searchTerm, setSearchTerm] = useState('');
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
        setSbuType(selectedCert?.sbuTypeSlug || '');
        setSelectedSubId(''); setSelectedKlasifikasiId(''); setSelectedSubKlasifikasi('');
        setSelectedKualifikasiId(''); setSelectedBiayaLainnyaId(''); setBiayaSetorKantor(0);
    }, [selectedCert]);

    const { subOptions, klasifikasiOptions, subKlasifikasiOptions, kualifikasiOptions, biayaLainnyaOptions, biayaSetorOptions } = useMemo(() => {
        let sub: SbuData[] = [], klas: KlasifikasiData[] = [], kual: BiayaData[] = [], bl: BiayaData[] = [], bs: BiayaData[] = [];
        switch (sbuType) {
            case 'konstruksi':
                sub = certs.sbuKonstruksiData; klas = certs.konstruksiKlasifikasiData;
                {
                    const nm = sub.find(s => s.id === selectedSubId)?.name?.toUpperCase() ?? '';
                    if (nm === 'P3SM') { kual = certs.p3smKualifikasiData; bl = certs.p3smBiayaLainnyaData; bs = certs.p3smBiayaSetorData; }
                    else if (nm === 'GAPEKNAS') { kual = certs.gapeknasKualifikasiData; bl = certs.gapeknasBiayaLainnyaData; bs = certs.gapeknasBiayaSetorData; }
                    else if (selectedSubId) { kual = certs.p3smKualifikasiData; bl = certs.p3smBiayaLainnyaData; bs = certs.p3smBiayaSetorData; }
                } break;
            case 'konsultan': sub = certs.sbuKonsultanData; klas = certs.konsultanKlasifikasiData; kual = certs.konsultanKualifikasiData; bl = certs.konsultanBiayaLainnyaData; bs = certs.konsultanBiayaSetorData; break;
            case 'skk': sub = certs.skkKonstruksiData; klas = certs.skkKlasifikasiData; kual = certs.skkKualifikasiData; bl = certs.skkBiayaLainnyaData; bs = certs.skkBiayaSetorData; break;
            case 'smap': bs = certs.smapBiayaSetorData; break;
            case 'simpk': bs = certs.simpkBiayaSetorData; break;
            case 'notaris': kual = certs.notarisKualifikasiData; bl = certs.notarisBiayaLainnyaData; bs = certs.notarisBiayaSetorData; break;
            default: {
                const dynData = certs.dynamicReferenceData?.[sbuType];
                if (dynData) { sub = dynData.asosiasi; klas = dynData.klasifikasi; kual = dynData.kualifikasi; bl = dynData.biayaLainnya; bs = dynData.biayaSetor; }
            }
        }
        const selectedKlas = klas.find(k => k.id === selectedKlasifikasiId);
        return { subOptions: sub, klasifikasiOptions: klas, subKlasifikasiOptions: selectedKlas?.subKlasifikasi ?? [], kualifikasiOptions: kual, biayaLainnyaOptions: bl, biayaSetorOptions: bs };
    }, [sbuType, selectedSubId, selectedKlasifikasiId, certs]);

    const mc: MenuConfig = selectedCert?.menuConfig ?? DEFAULT_MENU_CONFIG;
    const hasSubOptions = mc.asosiasi && subOptions.length > 0;
    const hasKlasifikasi = mc.klasifikasi && klasifikasiOptions.length > 0;
    const hasKualifikasi = mc.kualifikasi;

    const selectedKualifikasi = kualifikasiOptions.find(k => k.id === selectedKualifikasiId) || null;
    const selectedBiayaLainnya = biayaLainnyaOptions.find(b => b.id === selectedBiayaLainnyaId) || null;

    useEffect(() => {
        if (selectedKualifikasi && biayaSetorOptions.length > 0) {
            // Match by name + kode first (for items like SMK3 that share the same name)
            const exactMatch = selectedKualifikasi.kode
                ? biayaSetorOptions.find(bs => bs.name === selectedKualifikasi.name && bs.kode === selectedKualifikasi.kode)
                : null;
            if (exactMatch) { setBiayaSetorKantor(exactMatch.biaya); return; }
            // Fallback: match by name only
            const nameMatch = biayaSetorOptions.find(bs => bs.name === selectedKualifikasi.name);
            if (nameMatch) { setBiayaSetorKantor(nameMatch.biaya); return; }
        }
        if (!selectedKualifikasiId && biayaSetorOptions.length === 1 && !hasKualifikasi) setBiayaSetorKantor(biayaSetorOptions[0].biaya);
        else if (!selectedKualifikasiId) setBiayaSetorKantor(0);
    }, [selectedKualifikasiId, selectedKualifikasi, biayaSetorOptions, hasKualifikasi]);

    const keuntungan = calculateKeuntungan(biayaSetorKantor, selectedKualifikasi?.biaya || 0, selectedBiayaLainnya?.biaya || 0);

    const handleBack = () => {
        setSelectedCert(null); setCompanyPrefix('PT.'); setCompanyName(''); setMarketingName('');
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!selectedCert || !user) return;
        const fullName = companyPrefix === '-' ? companyName : `${companyPrefix} ${companyName}`;
        await dispatch(createSubmission({
            certificateType: selectedCert.name,
            companyName: capitalizeWords(fullName.trim()),
            marketingName,
            inputDate,
            submittedById: user.id,
            sbuType: sbuType as SbuType,
            selectedSub: subOptions.find(s => s.id === selectedSubId) ?? null,
            selectedKlasifikasi: klasifikasiOptions.find(k => k.id === selectedKlasifikasiId) ?? null,
            selectedSubKlasifikasi: selectedSubKlasifikasi || null,
            selectedKualifikasi: kualifikasiOptions.find(k => k.id === selectedKualifikasiId) ?? null,
            selectedBiayaLainnya: biayaLainnyaOptions.find(b => b.id === selectedBiayaLainnyaId) ?? null,
            biayaSetorKantor,
            keuntungan,
        }));
        navigate('/submissions');
    };

    // ── Step 1: Cert Selection ────────────────────────────────────────────────
    if (!selectedCert) {
        return (
            <div className="space-y-8">
                {/* Breadcrumb */}
                <div className="flex items-center gap-2 text-sm text-gray-400 dark:text-slate-500">
                    <span>Input Data</span>
                    <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}><path strokeLinecap="round" strokeLinejoin="round" d="M9 5l7 7-7 7" /></svg>
                    <span className="text-gray-700 dark:text-slate-300 font-medium">Pilih Sertifikat</span>
                </div>

                {/* Hero */}
                <div className="flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <h1 className="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white tracking-tight mb-2">Portal Sertifikat</h1>
                        <p className="text-gray-500 dark:text-slate-400 text-base">Pilih jenis sertifikat untuk memulai input data</p>
                    </div>
                    <div className="w-full md:w-72 relative">
                        <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg className="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            placeholder="Cari sertifikat..."
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            className="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-xl leading-5 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-shadow shadow-sm"
                        />
                    </div>
                </div>

                {/* Card Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    {certs.list.filter(c => c.sbuTypeSlug && c.name.toLowerCase().includes(searchTerm.toLowerCase())).map((cert, idx) => {
                        const st = getCardStyle(cert.name, idx);
                        return (
                            <button
                                key={cert.id}
                                onClick={() => setSelectedCert(cert)}
                                className={`group relative bg-gradient-to-br ${st.gradient} rounded-2xl p-6 text-left text-white shadow-lg hover:shadow-2xl ${st.glow} hover:-translate-y-1.5 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-white/20 overflow-hidden`}
                            >
                                {/* Glossy shine */}
                                <div className="absolute inset-0 bg-gradient-to-b from-white/10 to-transparent pointer-events-none rounded-2xl" />

                                <div className="relative z-10">
                                    <div className="text-5xl mb-5 group-hover:scale-110 transition-transform duration-300 drop-shadow">{st.icon}</div>
                                    <h3 className="text-lg font-bold mb-1.5 leading-tight">{cert.name}</h3>
                                    <p className="text-sm text-white/75 leading-relaxed mb-5">{st.desc}</p>
                                    <div className="flex items-center gap-2 text-sm font-semibold text-white/80 group-hover:text-white group-hover:gap-3 transition-all">
                                        <span>Mulai Input</span>
                                        <svg className="h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}><path strokeLinecap="round" strokeLinejoin="round" d="M9 5l7 7-7 7" /></svg>
                                    </div>
                                </div>
                            </button>
                        );
                    })}
                </div>
            </div>
        );
    }

    // ── Step 2: Input Form ────────────────────────────────────────────────────
    const certStyle = getCardStyle(selectedCert.name, 0);
    return (
        <div className="max-w-3xl mx-auto space-y-6">
            {/* Header */}
            <div className="flex items-start gap-4">
                <button
                    onClick={handleBack}
                    className="flex items-center gap-1.5 text-gray-400 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white transition text-sm font-medium mt-1 shrink-0"
                >
                    <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}><path strokeLinecap="round" strokeLinejoin="round" d="M15 19l-7-7 7-7" /></svg>
                    Kembali
                </button>
                <div>
                    <div className="flex items-center gap-3 mb-1">
                        <span className={`text-3xl bg-gradient-to-br ${certStyle.gradient} rounded-xl p-2 shadow`}>{certStyle.icon}</span>
                        <h1 className="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Input {selectedCert.name}</h1>
                    </div>
                    <p className="text-gray-500 dark:text-slate-400 text-sm ml-14">Masukkan data sertifikasi baru</p>
                </div>
            </div>

            {/* Form */}
            <form onSubmit={handleSubmit} className="space-y-4">
                {/* ── Section 1: Informasi Perusahaan ────────────────────── */}
                <Section accent="border-indigo-500" icon="🏢" title="Informasi Perusahaan">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {/* Company name */}
                        <div>
                            <label className={labelCls}>Nama Perusahaan</label>
                            <div className="flex rounded-xl overflow-hidden border border-gray-300 dark:border-slate-600 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-transparent transition">
                                <select
                                    value={companyPrefix}
                                    onChange={e => setCompanyPrefix(e.target.value)}
                                    className="px-3 py-3 bg-gray-100 dark:bg-slate-600 text-gray-800 dark:text-white text-sm font-semibold border-r border-gray-300 dark:border-slate-500 focus:outline-none shrink-0"
                                >
                                    <option>PT.</option><option>CV.</option><option>UD.</option>
                                    <option>PD.</option><option>Firma</option><option>Koperasi</option>
                                    <option>Yayasan</option><option>-</option>
                                </select>
                                <input
                                    type="text"
                                    value={companyName}
                                    onChange={e => setCompanyName(e.target.value)}
                                    className="flex-1 px-4 py-3 bg-white dark:bg-slate-700/60 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-400 focus:outline-none text-sm"
                                    placeholder="Nama perusahaan..."
                                    required
                                />
                            </div>
                        </div>

                        {/* Marketing */}
                        <div>
                            <label className={labelCls}>Marketing</label>
                            <select value={marketingName} onChange={e => setMarketingName(e.target.value)} className={inputCls} required>
                                <option value="">Pilih Marketing</option>
                                {marketingNames.map(m => <option key={m.id} value={m.name}>{m.name}</option>)}
                            </select>
                        </div>
                    </div>

                    {/* Date */}
                    <div>
                        <label className={labelCls}>Tanggal Input</label>
                        <input type="date" value={inputDate} onChange={e => setInputDate(e.target.value)} className={inputCls} required />
                    </div>
                </Section>

                {/* ── Section 2: Detail Sertifikat (conditional) ──────────── */}
                {(hasSubOptions || hasKlasifikasi) && (
                    <Section accent="border-violet-500" icon="📋" title="Detail Sertifikat">
                        {hasSubOptions && (
                            <div>
                                <label className={labelCls}>Asosiasi</label>
                                <select value={selectedSubId} onChange={e => { setSelectedSubId(e.target.value); setSelectedKlasifikasiId(''); setSelectedSubKlasifikasi(''); }} className={inputCls} required>
                                    <option value="">Pilih Asosiasi</option>
                                    {subOptions.map(s => <option key={s.id} value={s.id}>{s.name}</option>)}
                                </select>
                            </div>
                        )}
                        {hasKlasifikasi && klasifikasiOptions.length > 0 && selectedSubId && (
                            <div>
                                <label className={labelCls}>Klasifikasi</label>
                                <select value={selectedKlasifikasiId} onChange={e => { setSelectedKlasifikasiId(e.target.value); setSelectedSubKlasifikasi(''); }} className={inputCls} required>
                                    <option value="">Pilih Klasifikasi</option>
                                    {klasifikasiOptions.map(k => <option key={k.id} value={k.id}>{k.name}</option>)}
                                </select>
                            </div>
                        )}
                        {subKlasifikasiOptions.length > 0 && selectedKlasifikasiId && (
                            <div>
                                <label className={labelCls}>Sub Klasifikasi</label>
                                <select value={selectedSubKlasifikasi} onChange={e => setSelectedSubKlasifikasi(e.target.value)} className={inputCls} required>
                                    <option value="">Pilih Sub Klasifikasi</option>
                                    {subKlasifikasiOptions.map((s, i) => <option key={i} value={s}>{s}</option>)}
                                </select>
                            </div>
                        )}
                    </Section>
                )}

                {/* ── Section 3: Biaya ────────────────────────────────────── */}
                {sbuType && (
                    <Section accent="border-emerald-500" icon="💰" title="Biaya">
                        {hasKualifikasi && kualifikasiOptions.length > 0 && (
                            <div>
                                <label className={labelCls}>{mc.kualifikasiLabel || 'Kualifikasi'} / Biaya Dasar</label>
                                <select value={selectedKualifikasiId} onChange={e => setSelectedKualifikasiId(e.target.value)} className={inputCls} required>
                                    <option value="">Pilih {mc.kualifikasiLabel || 'Kualifikasi'}</option>
                                    {kualifikasiOptions.map(k => <option key={k.id} value={k.id}>{k.kode ? `[${k.kode}] ` : ''}{k.name} — {formatCurrency(k.biaya)}</option>)}
                                </select>
                            </div>
                        )}

                        {biayaLainnyaOptions.length > 0 && (
                            <div>
                                <label className={labelCls}>Biaya Lainnya</label>
                                <select value={selectedBiayaLainnyaId} onChange={e => setSelectedBiayaLainnyaId(e.target.value)} className={inputCls}>
                                    <option value="">Tidak ada</option>
                                    {biayaLainnyaOptions.map(b => <option key={b.id} value={b.id}>{b.name} — {formatCurrency(b.biaya)}</option>)}
                                </select>
                            </div>
                        )}

                        <div>
                            <label className={labelCls}>
                                Biaya Setor Kantor
                                {biayaSetorKantor > 0 && selectedKualifikasi && (
                                    <span className="ml-2 inline-flex items-center gap-1 text-xs text-emerald-400 font-normal bg-emerald-400/10 px-2 py-0.5 rounded-full">
                                        <svg className="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}><path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" /></svg>
                                        Otomatis terisi
                                    </span>
                                )}
                            </label>
                            <RpInput value={biayaSetorKantor} onChange={setBiayaSetorKantor} required />
                        </div>

                        {/* Summary card */}
                        <div className={`rounded-xl border p-4 ${keuntungan >= 0 ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800/50' : 'bg-red-50 dark:bg-red-950/40 border-red-200 dark:border-red-800/50'}`}>
                            <p className="text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-slate-400 mb-3">Ringkasan Biaya</p>
                            <div className="space-y-2 text-sm">
                                {[
                                    { label: 'Biaya Setor Kantor', val: biayaSetorKantor },
                                    { label: mc.biayaSetorLabel || 'Biaya Kualifikasi', val: selectedKualifikasi?.biaya || 0 },
                                    { label: 'Biaya Lainnya', val: selectedBiayaLainnya?.biaya || 0 },
                                ].map(row => (
                                    <div key={row.label} className="flex justify-between">
                                        <span className="text-gray-500 dark:text-slate-400">{row.label}</span>
                                        <span className="font-semibold text-gray-900 dark:text-white">{formatCurrency(row.val)}</span>
                                    </div>
                                ))}
                                <div className={`flex justify-between pt-2 border-t ${keuntungan >= 0 ? 'border-emerald-300 dark:border-emerald-800/50' : 'border-red-300 dark:border-red-800/50'}`}>
                                    <span className="font-bold text-gray-900 dark:text-white">Keuntungan</span>
                                    <span className={`font-bold text-base ${keuntungan >= 0 ? 'text-emerald-400' : 'text-red-400'}`}>
                                        {formatCurrency(keuntungan)}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </Section>
                )}

                {/* ── Footer ─────────────────────────────────────────────── */}
                <div className="flex justify-end gap-3 pt-2">
                    <button
                        type="button"
                        onClick={handleBack}
                        className="px-6 py-3 rounded-xl font-semibold text-sm bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-200 hover:bg-gray-200 dark:hover:bg-slate-600 transition border border-gray-300 dark:border-slate-600"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        className="px-8 py-3 rounded-xl font-semibold text-sm bg-indigo-600 text-white hover:bg-indigo-500 transition shadow-lg shadow-indigo-500/20 flex items-center gap-2"
                    >
                        <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}><path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" /></svg>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    );
};
