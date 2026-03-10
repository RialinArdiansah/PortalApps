<?php
$pageTitle = 'Input Data Sertifikat';

// Card styles mapping
$cardStyles = [
    'SBU Konstruksi' => ['icon' => '🏗️', 'gradient' => 'from-indigo-500 via-indigo-600 to-indigo-800', 'desc' => 'Sertifikat Badan Usaha untuk jasa konstruksi'],
    'SKK Konstruksi' => ['icon' => '👷', 'gradient' => 'from-blue-500 via-blue-600 to-blue-800', 'desc' => 'Sertifikat Kompetensi Kerja konstruksi'],
    'SBU Konsultan' => ['icon' => '📐', 'gradient' => 'from-violet-500 via-violet-600 to-violet-800', 'desc' => 'Sertifikat Badan Usaha untuk jasa konsultansi'],
    'Dokumen SMAP' => ['icon' => '📋', 'gradient' => 'from-emerald-500 via-emerald-600 to-emerald-800', 'desc' => 'Sistem Manajemen Anti Penyuapan'],
    'Akun SIMPK dan Alat' => ['icon' => '🔧', 'gradient' => 'from-amber-500 via-amber-600 to-amber-800', 'desc' => 'Sistem Informasi Manajemen Konstruksi'],
    'Notaris' => ['icon' => '⚖️', 'gradient' => 'from-rose-500 via-rose-600 to-rose-800', 'desc' => 'Layanan sertifikasi notaris'],
    'Sewa SKK Tenaga Ahli' => ['icon' => '💼', 'gradient' => 'from-cyan-500 via-cyan-600 to-cyan-800', 'desc' => 'Sewa sertifikat kompetensi tenaga ahli'],
    'SMK3 Perusahaan (Kemenaker)' => ['icon' => '🛡️', 'gradient' => 'from-teal-500 via-teal-600 to-teal-800', 'desc' => 'Sistem Manajemen K3 perusahaan'],
    'AK3 Umum Kemenaker' => ['icon' => '🎓', 'gradient' => 'from-sky-500 via-sky-600 to-sky-800', 'desc' => 'Ahli Keselamatan dan Kesehatan Kerja'],
    'ISO Lokal' => ['icon' => '🏅', 'gradient' => 'from-lime-500 via-lime-600 to-lime-800', 'desc' => 'Sertifikasi ISO oleh badan lokal'],
];
$dynIcons = ['📜','🏢','🔑','🎓','⭐','🛡️'];
$dynGradients = ['from-cyan-500 via-cyan-600 to-cyan-800','from-teal-500 via-teal-600 to-teal-800','from-pink-500 via-pink-600 to-pink-800','from-orange-500 via-orange-600 to-orange-800','from-sky-500 via-sky-600 to-sky-800','from-fuchsia-500 via-fuchsia-600 to-fuchsia-800'];

// Filter to only certificates with sbu_type_slug (advanced types)
$advancedCerts = array_values(array_filter($certificates, fn($c) => !empty($c['sbu_type_slug'])));
?>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- Step 1: Certificate Selection (Card Grid) -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<div id="step1" class="space-y-8">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-slate-400 dark:text-slate-500">
        <span>Input Data</span>
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-700 dark:text-slate-300 font-medium">Pilih Sertifikat</span>
    </div>

    <!-- Hero -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white tracking-tight mb-2">Portal Sertifikat</h1>
            <p class="text-slate-500 dark:text-slate-400 text-base">Pilih jenis sertifikat untuk memulai input data</p>
        </div>
        <div class="w-full md:w-72 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" id="certSearch" placeholder="Cari sertifikat..." oninput="filterCerts()"
                class="block w-full pl-10 pr-3 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl leading-5 bg-white dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm transition-shadow shadow-sm">
        </div>
    </div>

    <!-- Card Grid -->
    <div id="certGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php foreach ($advancedCerts as $idx => $cert):
            $style = $cardStyles[$cert['name']] ?? [
                'icon' => $dynIcons[$idx % count($dynIcons)],
                'gradient' => $dynGradients[$idx % count($dynGradients)],
                'desc' => 'Sertifikasi ' . $cert['name'],
            ];
        ?>
        <button type="button" onclick='selectCert(<?= json_encode($cert, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'
            class="cert-card group relative bg-gradient-to-br <?= $style['gradient'] ?> rounded-2xl p-6 text-left text-white shadow-lg hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-white/20 overflow-hidden"
            data-name="<?= e(strtolower($cert['name'])) ?>">
            <!-- Glossy shine -->
            <div class="absolute inset-0 bg-gradient-to-b from-white/10 to-transparent pointer-events-none rounded-2xl"></div>
            <div class="relative z-10">
                <div class="text-5xl mb-5 group-hover:scale-110 transition-transform duration-300 drop-shadow"><?= $style['icon'] ?></div>
                <h3 class="text-lg font-bold mb-1.5 leading-tight"><?= e($cert['name']) ?></h3>
                <p class="text-sm text-white/75 leading-relaxed mb-5"><?= e($style['desc']) ?></p>
                <div class="flex items-center gap-2 text-sm font-semibold text-white/80 group-hover:text-white group-hover:gap-3 transition-all">
                    <span>Mulai Input</span>
                    <svg class="h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </div>
            </div>
        </button>
        <?php endforeach; ?>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- Step 2: Input Form -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<div id="step2" class="hidden max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-start gap-4">
        <button type="button" onclick="goBack()" class="flex items-center gap-1.5 text-slate-400 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition text-sm font-medium mt-1 shrink-0">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </button>
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span id="formIcon" class="text-3xl rounded-xl p-2 shadow"></span>
                <h1 id="formTitle" class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white"></h1>
            </div>
            <p class="text-slate-500 dark:text-slate-400 text-sm ml-14">Masukkan data sertifikasi baru</p>
        </div>
    </div>

    <form method="POST" action="<?= url('/submissions/store') ?>" id="submissionForm" class="space-y-4" onsubmit="return prepareSubmit()">
        <?= csrf_field() ?>
        <input type="hidden" name="certificate_type" id="h_certificate_type">
        <input type="hidden" name="sbu_type" id="h_sbu_type">
        <input type="hidden" name="selected_sub" id="h_selected_sub">
        <input type="hidden" name="selected_klasifikasi" id="h_selected_klasifikasi">
        <input type="hidden" name="selected_sub_klasifikasi" id="h_selected_sub_klasifikasi">
        <input type="hidden" name="selected_kualifikasi" id="h_selected_kualifikasi">
        <input type="hidden" name="selected_biaya_lainnya" id="h_selected_biaya_lainnya">
        <input type="hidden" name="biaya_setor_kantor" id="h_biaya_setor_kantor" value="0">
        <input type="hidden" name="company_prefix" id="h_company_prefix" value="PT.">

        <!-- ── Section 1: Informasi Perusahaan ────────────────────── -->
        <div class="bg-white dark:bg-slate-800/70 rounded-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden shadow-sm">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-200 dark:border-slate-700/50 border-l-4 border-l-indigo-500 bg-slate-50 dark:bg-transparent">
                <span class="text-xl">🏢</span>
                <h2 class="text-slate-800 dark:text-white font-semibold text-base tracking-wide">Informasi Perusahaan</h2>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Company Name with Prefix -->
                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-1.5">Nama Perusahaan</label>
                        <div class="flex rounded-xl overflow-hidden border border-slate-300 dark:border-slate-600 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-transparent transition">
                            <select id="companyPrefix" onchange="document.getElementById('h_company_prefix').value=this.value"
                                class="px-3 py-3 bg-slate-100 dark:bg-slate-600 text-slate-800 dark:text-white text-sm font-semibold border-r border-slate-300 dark:border-slate-500 focus:outline-none shrink-0">
                                <option>PT.</option><option>CV.</option><option>UD.</option>
                                <option>PD.</option><option>Firma</option><option>Koperasi</option>
                                <option>Yayasan</option><option>-</option>
                            </select>
                            <input type="text" name="company_name" id="companyName" required
                                class="flex-1 px-4 py-3 bg-white dark:bg-slate-700/60 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-400 focus:outline-none text-sm"
                                placeholder="Nama perusahaan...">
                        </div>
                    </div>

                    <!-- Marketing -->
                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-1.5">Marketing</label>
                        <select name="marketing_name" required
                            class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-700/60 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm">
                            <option value="">Pilih Marketing</option>
                            <?php foreach ($marketingNames as $mk): ?>
                            <option value="<?= e($mk['name']) ?>"><?= e($mk['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-1.5">Tanggal Input</label>
                    <input type="date" name="input_date" value="<?= date('Y-m-d') ?>" required
                        class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-700/60 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm">
                </div>
            </div>
        </div>

        <!-- ── Section 2: Detail Sertifikat ────────────────────── -->
        <div id="sectionDetail" class="hidden bg-white dark:bg-slate-800/70 rounded-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden shadow-sm">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-200 dark:border-slate-700/50 border-l-4 border-l-violet-500 bg-slate-50 dark:bg-transparent">
                <span class="text-xl">📋</span>
                <h2 class="text-slate-800 dark:text-white font-semibold text-base tracking-wide">Detail Sertifikat</h2>
            </div>
            <div class="p-5 space-y-4">
                <!-- Asosiasi -->
                <div id="asosiasiField" class="hidden">
                    <label class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-1.5">Asosiasi</label>
                    <select id="selAsosiasi" onchange="onAsosiasiSelect()"
                        class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-700/60 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm">
                        <option value="">Pilih Asosiasi</option>
                    </select>
                </div>

                <!-- Klasifikasi -->
                <div id="klasifikasiField" class="hidden">
                    <label class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-1.5">Klasifikasi</label>
                    <select id="selKlasifikasi" onchange="onKlasifikasiSelect()"
                        class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-700/60 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm">
                        <option value="">Pilih Klasifikasi</option>
                    </select>
                </div>

                <!-- Sub Klasifikasi -->
                <div id="subKlasifikasiField" class="hidden">
                    <label class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-1.5">Sub Klasifikasi</label>
                    <select id="selSubKlasifikasi" onchange="onSubKlasifikasiSelect()"
                        class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-700/60 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm">
                        <option value="">Pilih Sub Klasifikasi</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- ── Section 3: Biaya ────────────────────────────────── -->
        <div id="sectionBiaya" class="hidden bg-white dark:bg-slate-800/70 rounded-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden shadow-sm">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-200 dark:border-slate-700/50 border-l-4 border-l-emerald-500 bg-slate-50 dark:bg-transparent">
                <span class="text-xl">💰</span>
                <h2 class="text-slate-800 dark:text-white font-semibold text-base tracking-wide">Biaya</h2>
            </div>
            <div class="p-5 space-y-4">
                <!-- Kualifikasi / Biaya Dasar -->
                <div id="kualifikasiField" class="hidden">
                    <label id="kualifikasiFieldLabel" class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-1.5">Kualifikasi / Biaya Dasar</label>
                    <select id="selKualifikasi" onchange="onKualifikasiSelect()"
                        class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-700/60 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm">
                        <option value="">Pilih Kualifikasi</option>
                    </select>
                </div>

                <!-- Biaya Lainnya -->
                <div id="biayaLainnyaField" class="hidden">
                    <label class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-1.5">Biaya Lainnya</label>
                    <select id="selBiayaLainnya" onchange="onBiayaLainnyaSelect()"
                        class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-700/60 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm">
                        <option value="">Tidak ada</option>
                    </select>
                </div>

                <!-- Biaya Setor Kantor -->
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-1.5">
                        Biaya Setor Kantor
                        <span id="autoFillBadge" class="hidden ml-2 inline-flex items-center gap-1 text-xs text-emerald-500 font-normal bg-emerald-400/10 px-2 py-0.5 rounded-full">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Otomatis terisi
                        </span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-400 font-semibold text-sm select-none">Rp</span>
                        <input type="text" id="biayaSetorInput" inputmode="numeric"
                            class="w-full pl-12 pr-4 py-3 rounded-xl bg-white dark:bg-slate-700/60 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm"
                            placeholder="0" oninput="onBiayaSetorInput(this)" onchange="recalculate()">
                    </div>
                </div>

                <!-- Cost Summary Card -->
                <div id="costSummary" class="rounded-xl border p-4 bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800/50">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 dark:text-slate-400 mb-3">Ringkasan Biaya</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Biaya Setor Kantor</span>
                            <span class="font-semibold text-slate-900 dark:text-white" id="sumSetor">Rp 0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-slate-400" id="sumKualLabel">Biaya Kualifikasi</span>
                            <span class="font-semibold text-slate-900 dark:text-white" id="sumKual">Rp 0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Biaya Lainnya</span>
                            <span class="font-semibold text-slate-900 dark:text-white" id="sumLain">Rp 0</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-emerald-300 dark:border-emerald-800/50" id="sumKeuntunganRow">
                            <span class="font-bold text-slate-900 dark:text-white">Keuntungan</span>
                            <span class="font-bold text-base text-emerald-500" id="sumKeuntungan">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-3 pt-2">
            <button type="button" onclick="goBack()"
                class="px-6 py-3 rounded-xl font-semibold text-sm bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-600 transition border border-slate-300 dark:border-slate-600">
                Batal
            </button>
            <button type="submit"
                class="px-8 py-3 rounded-xl font-semibold text-sm bg-indigo-600 text-white hover:bg-indigo-500 transition shadow-lg shadow-indigo-500/20 flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Simpan Data
            </button>
        </div>
    </form>
</div>

<script>
// ═══════════════════════════════════════════════════════════════════
// State
// ═══════════════════════════════════════════════════════════════════
const sbuTypesMap = <?= json_encode($sbuTypesMap) ?>;
const cardStylesMap = <?= json_encode($cardStyles) ?>;

let selectedCertData = null;
let refData = null;
let menuConfig = {};

// Current selections
let currentAsosiasiId = '';
let currentKlasifikasiId = '';
let currentKualifikasiId = '';
let currentBiayaLainnyaId = '';
let biayaSetorValue = 0;

// ═══════════════════════════════════════════════════════════════════
// Step 1: Card Grid
// ═══════════════════════════════════════════════════════════════════
function filterCerts() {
    const term = document.getElementById('certSearch').value.toLowerCase();
    document.querySelectorAll('.cert-card').forEach(card => {
        card.style.display = card.dataset.name.includes(term) ? '' : 'none';
    });
}

function selectCert(cert) {
    selectedCertData = cert;
    const slug = cert.sbu_type_slug || '';
    document.getElementById('h_certificate_type').value = cert.name;
    document.getElementById('h_sbu_type').value = slug;

    // Set form header
    const style = cardStylesMap[cert.name] || { icon: '📜' };
    document.getElementById('formIcon').textContent = style?.icon || '📜';
    document.getElementById('formTitle').textContent = 'Input ' + cert.name;

    // Reset all
    currentAsosiasiId = ''; currentKlasifikasiId = ''; currentKualifikasiId = ''; currentBiayaLainnyaId = '';
    biayaSetorValue = 0;
    document.getElementById('biayaSetorInput').value = '';
    document.getElementById('autoFillBadge').classList.add('hidden');
    clearHiddenFields();

    // Switch to step 2
    document.getElementById('step1').classList.add('hidden');
    document.getElementById('step2').classList.remove('hidden');

    // Load reference data
    if (slug) {
        fetch(baseUrl('/api/certificates/reference-data/' + slug))
            .then(r => r.json())
            .then(res => {
                if (!res.success) return;
                refData = res.data;
                menuConfig = res.data.menuConfig || {};
                buildForm();
            })
            .catch(err => console.error('Failed to load reference data:', err));
    }
}

function goBack() {
    document.getElementById('step2').classList.add('hidden');
    document.getElementById('step1').classList.remove('hidden');
    selectedCertData = null;
    refData = null;
}

function clearHiddenFields() {
    ['h_selected_sub','h_selected_klasifikasi','h_selected_sub_klasifikasi','h_selected_kualifikasi','h_selected_biaya_lainnya'].forEach(id => {
        document.getElementById(id).value = '';
    });
    document.getElementById('h_biaya_setor_kantor').value = '0';
}

// ═══════════════════════════════════════════════════════════════════
// Step 2: Build Form Sections
// ═══════════════════════════════════════════════════════════════════
function buildForm() {
    const mc = menuConfig;
    const hasAsosiasi = mc.asosiasi && (refData.asosiasi || []).length > 0;
    const hasKlasifikasi = mc.klasifikasi && (refData.klasifikasi || []).length > 0;
    const hasKualifikasi = mc.kualifikasi && (refData.kualifikasi || []).length > 0;
    const hasBiayaLainnya = (refData.biayaLainnya || []).length > 0;

    // Section 2: Detail
    const showDetail = hasAsosiasi || hasKlasifikasi;
    document.getElementById('sectionDetail').classList.toggle('hidden', !showDetail);

    // Asosiasi
    if (hasAsosiasi) {
        const sel = document.getElementById('selAsosiasi');
        sel.innerHTML = '<option value="">Pilih Asosiasi</option>';
        refData.asosiasi.forEach(a => {
            sel.innerHTML += `<option value="${a.id}">${esc(a.name)}</option>`;
        });
        document.getElementById('asosiasiField').classList.remove('hidden');
    } else {
        document.getElementById('asosiasiField').classList.add('hidden');
    }

    // Klasifikasi (show immediately only if no asosiasi)
    if (hasKlasifikasi && !hasAsosiasi) {
        populateKlasifikasi();
    } else {
        document.getElementById('klasifikasiField').classList.add('hidden');
    }
    document.getElementById('subKlasifikasiField').classList.add('hidden');

    // Section 3: Biaya (always show when sbu_type exists)
    document.getElementById('sectionBiaya').classList.remove('hidden');

    // Kualifikasi
    if (hasKualifikasi) {
        const label = (mc.kualifikasiLabel || 'Kualifikasi') + ' / Biaya Dasar';
        document.getElementById('kualifikasiFieldLabel').textContent = label;
        populateKualifikasi();
    } else {
        document.getElementById('kualifikasiField').classList.add('hidden');
        // If no kualifikasi but biayaSetor has only 1 item, auto-fill
        if ((refData.biayaSetor || []).length === 1) {
            biayaSetorValue = refData.biayaSetor[0].biaya;
            setBiayaSetorDisplay(biayaSetorValue);
            document.getElementById('autoFillBadge').classList.remove('hidden');
        }
    }

    // Biaya Lainnya
    if (hasBiayaLainnya) {
        const sel = document.getElementById('selBiayaLainnya');
        sel.innerHTML = '<option value="">Tidak ada</option>';
        refData.biayaLainnya.forEach(b => {
            sel.innerHTML += `<option value="${b.id}">${esc(b.name)} — ${fmtRp(b.biaya)}</option>`;
        });
        document.getElementById('biayaLainnyaField').classList.remove('hidden');
    } else {
        document.getElementById('biayaLainnyaField').classList.add('hidden');
    }

    // Kualifikasi label for summary
    document.getElementById('sumKualLabel').textContent = mc.biayaSetorLabel || 'Biaya Kualifikasi';

    recalculate();
}

function populateKlasifikasi() {
    const sel = document.getElementById('selKlasifikasi');
    sel.innerHTML = '<option value="">Pilih Klasifikasi</option>';
    (refData.klasifikasi || []).forEach(k => {
        sel.innerHTML += `<option value="${k.id}">${esc(k.name)}</option>`;
    });
    document.getElementById('klasifikasiField').classList.remove('hidden');
}

function populateKualifikasi() {
    const mc = menuConfig;
    const sel = document.getElementById('selKualifikasi');
    sel.innerHTML = `<option value="">Pilih ${mc.kualifikasiLabel || 'Kualifikasi'}</option>`;
    (refData.kualifikasi || []).forEach(k => {
        const label = (k.kode ? `[${k.kode}] ` : '') + k.name + ' — ' + fmtRp(k.biaya);
        sel.innerHTML += `<option value="${k.id}">${esc(label)}</option>`;
    });
    document.getElementById('kualifikasiField').classList.remove('hidden');
}

// ═══════════════════════════════════════════════════════════════════
// Cascading Select Handlers
// ═══════════════════════════════════════════════════════════════════
function onAsosiasiSelect() {
    const sel = document.getElementById('selAsosiasi');
    currentAsosiasiId = sel.value;
    const asos = (refData.asosiasi || []).find(a => a.id === currentAsosiasiId);
    document.getElementById('h_selected_sub').value = asos ? JSON.stringify(asos) : '';

    // Reset downstream
    currentKlasifikasiId = '';
    document.getElementById('h_selected_klasifikasi').value = '';
    document.getElementById('h_selected_sub_klasifikasi').value = '';
    document.getElementById('subKlasifikasiField').classList.add('hidden');

    // Show klasifikasi if menu config allows
    if (menuConfig.klasifikasi && (refData.klasifikasi || []).length > 0 && currentAsosiasiId) {
        populateKlasifikasi();
    } else if (menuConfig.klasifikasi) {
        document.getElementById('klasifikasiField').classList.add('hidden');
    }
}

function onKlasifikasiSelect() {
    const sel = document.getElementById('selKlasifikasi');
    currentKlasifikasiId = sel.value;
    const klas = (refData.klasifikasi || []).find(k => k.id === currentKlasifikasiId);
    document.getElementById('h_selected_klasifikasi').value = klas ? JSON.stringify({ id: klas.id, name: klas.name }) : '';

    // Sub-klasifikasi
    document.getElementById('h_selected_sub_klasifikasi').value = '';
    const subKlas = klas?.subKlasifikasi || [];
    if (subKlas.length > 0 && currentKlasifikasiId) {
        const subSel = document.getElementById('selSubKlasifikasi');
        subSel.innerHTML = '<option value="">Pilih Sub Klasifikasi</option>';
        subKlas.forEach(s => { subSel.innerHTML += `<option value="${esc(s)}">${esc(s)}</option>`; });
        document.getElementById('subKlasifikasiField').classList.remove('hidden');
    } else {
        document.getElementById('subKlasifikasiField').classList.add('hidden');
    }
}

function onSubKlasifikasiSelect() {
    document.getElementById('h_selected_sub_klasifikasi').value = document.getElementById('selSubKlasifikasi').value;
}

function onKualifikasiSelect() {
    const sel = document.getElementById('selKualifikasi');
    currentKualifikasiId = sel.value;
    const kual = (refData.kualifikasi || []).find(k => k.id === currentKualifikasiId);
    document.getElementById('h_selected_kualifikasi').value = kual ? JSON.stringify(kual) : '';

    // Auto-fill Biaya Setor from biayaSetor data (match by name + kode)
    if (kual && (refData.biayaSetor || []).length > 0) {
        let match = kual.kode
            ? (refData.biayaSetor || []).find(bs => bs.name === kual.name && bs.kode === kual.kode)
            : null;
        if (!match) match = (refData.biayaSetor || []).find(bs => bs.name === kual.name);
        if (match) {
            biayaSetorValue = match.biaya;
            setBiayaSetorDisplay(biayaSetorValue);
            document.getElementById('autoFillBadge').classList.remove('hidden');
        }
    } else if (!currentKualifikasiId) {
        biayaSetorValue = 0;
        setBiayaSetorDisplay(0);
        document.getElementById('autoFillBadge').classList.add('hidden');
    }

    recalculate();
}

function onBiayaLainnyaSelect() {
    const sel = document.getElementById('selBiayaLainnya');
    currentBiayaLainnyaId = sel.value;
    const item = (refData.biayaLainnya || []).find(b => b.id === currentBiayaLainnyaId);
    document.getElementById('h_selected_biaya_lainnya').value = item ? JSON.stringify(item) : '';
    recalculate();
}

// ═══════════════════════════════════════════════════════════════════
// Biaya Setor Input & Calculation
// ═══════════════════════════════════════════════════════════════════
function onBiayaSetorInput(el) {
    const raw = el.value.replace(/\D/g, '');
    biayaSetorValue = raw === '' ? 0 : Number(raw);
    el.value = biayaSetorValue === 0 ? '' : new Intl.NumberFormat('id-ID').format(biayaSetorValue);
    // If user manually types, hide auto-fill badge
    document.getElementById('autoFillBadge').classList.add('hidden');
    recalculate();
}

function setBiayaSetorDisplay(val) {
    biayaSetorValue = val;
    document.getElementById('biayaSetorInput').value = val === 0 ? '' : new Intl.NumberFormat('id-ID').format(val);
}

function recalculate() {
    const kualBiaya = getSelectedBiaya('kualifikasi');
    const lainBiaya = getSelectedBiaya('biayaLainnya');
    const keuntungan = biayaSetorValue - kualBiaya - lainBiaya;

    document.getElementById('sumSetor').textContent = fmtRp(biayaSetorValue);
    document.getElementById('sumKual').textContent = fmtRp(kualBiaya);
    document.getElementById('sumLain').textContent = fmtRp(lainBiaya);
    document.getElementById('sumKeuntungan').textContent = fmtRp(keuntungan);

    // Color
    const row = document.getElementById('costSummary');
    const keuntEl = document.getElementById('sumKeuntungan');
    const borderRow = document.getElementById('sumKeuntunganRow');
    if (keuntungan >= 0) {
        row.className = 'rounded-xl border p-4 bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800/50';
        keuntEl.className = 'font-bold text-base text-emerald-500';
        borderRow.className = 'flex justify-between pt-2 border-t border-emerald-300 dark:border-emerald-800/50';
    } else {
        row.className = 'rounded-xl border p-4 bg-red-50 dark:bg-red-950/40 border-red-200 dark:border-red-800/50';
        keuntEl.className = 'font-bold text-base text-red-500';
        borderRow.className = 'flex justify-between pt-2 border-t border-red-300 dark:border-red-800/50';
    }

    document.getElementById('h_biaya_setor_kantor').value = biayaSetorValue;
}

function getSelectedBiaya(type) {
    if (type === 'kualifikasi') {
        try { return JSON.parse(document.getElementById('h_selected_kualifikasi').value || '{}').biaya || 0; } catch(e) { return 0; }
    }
    try { return JSON.parse(document.getElementById('h_selected_biaya_lainnya').value || '{}').biaya || 0; } catch(e) { return 0; }
}

// ═══════════════════════════════════════════════════════════════════
// Submit
// ═══════════════════════════════════════════════════════════════════
function prepareSubmit() {
    // Set biaya setor hidden field
    document.getElementById('h_biaya_setor_kantor').value = biayaSetorValue;
    return true;
}

// Helpers
function fmtRp(n) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(n); }
function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
</script>
