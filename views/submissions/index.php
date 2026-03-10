<?php
$pageTitle = Auth::canViewAll() ? 'Data Input Pengguna' : 'Data Input Saya';

// Extract unique values for filters
$uniqueTypes = [];
$uniqueMarketings = [];
foreach ($submissions as $s) {
    $uniqueTypes[$s['certificate_type']] = true;
    $uniqueMarketings[$s['marketing_name']] = true;
}
ksort($uniqueTypes);
ksort($uniqueMarketings);
?>

<div class="flex flex-col mb-6 gap-4">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-white"><?= e($pageTitle) ?></h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Riwayat data sertifikasi yang telah diinput</p>
        </div>
        <a href="<?= url('/submissions/new') ?>" class="bg-indigo-600 text-white font-semibold px-5 py-2.5 rounded-xl hover:bg-indigo-700 transition shadow-md whitespace-nowrap flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Input Baru
        </a>
    </div>

    <!-- Search & Filters -->
    <div class="flex flex-col sm:flex-row gap-3">
        <input type="text" id="searchInput" placeholder="Cari data..." oninput="filterTable()"
            class="px-4 py-2 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-xl text-sm flex-1 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition placeholder-slate-400 dark:placeholder-slate-500">
        <select id="filterType" onchange="filterTable()"
            class="px-4 py-2 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-xl text-sm w-full sm:w-auto focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
            <option value="">Semua Jenis</option>
            <?php foreach (array_keys($uniqueTypes) as $t): ?>
            <option value="<?= e($t) ?>"><?= e($t) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (Auth::canViewAll()): ?>
        <select id="filterMarketing" onchange="filterTable()"
            class="px-4 py-2 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-xl text-sm w-full sm:w-auto focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
            <option value="">Semua Marketing</option>
            <?php foreach (array_keys($uniqueMarketings) as $m): ?>
            <option value="<?= e($m) ?>"><?= e($m) ?></option>
            <?php endforeach; ?>
        </select>
        <?php endif; ?>
    </div>
</div>

<div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden transition-colors">
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="submissionsTable">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="text-left py-3 px-4 font-semibold text-slate-600 dark:text-slate-400">#</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-600 dark:text-slate-400">Nama Perusahaan</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-600 dark:text-slate-400 hidden sm:table-cell">Marketing</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-600 dark:text-slate-400 hidden md:table-cell">Jenis</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-600 dark:text-slate-400 hidden lg:table-cell">Asosiasi</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-600 dark:text-slate-400 hidden lg:table-cell">Klasifikasi</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-600 dark:text-slate-400 hidden xl:table-cell">Sub Klasifikasi</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-600 dark:text-slate-400 hidden xl:table-cell">Kualifikasi</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-600 dark:text-slate-400 hidden xl:table-cell">Kode</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-600 dark:text-slate-400 hidden lg:table-cell">Tanggal</th>
                    <th class="text-right py-3 px-4 font-semibold text-slate-600 dark:text-slate-400">Keuntungan</th>
                    <th class="text-right py-3 px-4 font-semibold text-slate-600 dark:text-slate-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                <?php foreach ($submissions as $i => $s):
                    $sub = json_decode($s['selected_sub'] ?? 'null', true);
                    $klas = json_decode($s['selected_klasifikasi'] ?? 'null', true);
                    $kual = json_decode($s['selected_kualifikasi'] ?? 'null', true);
                    $subKlas = $s['selected_sub_klasifikasi'] ?? '';
                ?>
                <tr class="submission-row hover:bg-slate-50 dark:hover:bg-slate-700/30 transition"
                    data-search="<?= e(strtolower(
                        ($s['company_name'] ?? '') . ' ' .
                        ($s['marketing_name'] ?? '') . ' ' .
                        ($s['certificate_type'] ?? '') . ' ' .
                        ($sub['name'] ?? '') . ' ' .
                        ($klas['name'] ?? '') . ' ' .
                        ($subKlas) . ' ' .
                        ($kual['name'] ?? '') . ' ' .
                        ($kual['kode'] ?? '')
                    )) ?>"
                    data-type="<?= e($s['certificate_type'] ?? '') ?>"
                    data-marketing="<?= e($s['marketing_name'] ?? '') ?>">
                    <td class="py-3 px-4 text-slate-600 dark:text-slate-400"><?= $i + 1 ?></td>
                    <td class="py-3 px-4 font-medium text-slate-800 dark:text-white"><?= e($s['company_name']) ?></td>
                    <td class="py-3 px-4 text-slate-600 dark:text-slate-300 hidden sm:table-cell"><?= e($s['marketing_name']) ?></td>
                    <td class="py-3 px-4 hidden md:table-cell"><span class="bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 px-2 py-0.5 rounded-lg text-xs font-medium"><?= e($s['certificate_type']) ?></span></td>
                    <td class="py-3 px-4 text-slate-600 dark:text-slate-400 hidden lg:table-cell"><?= e($sub['name'] ?? '-') ?></td>
                    <td class="py-3 px-4 text-slate-600 dark:text-slate-400 hidden lg:table-cell"><?= e($klas['name'] ?? '-') ?></td>
                    <td class="py-3 px-4 text-slate-600 dark:text-slate-400 hidden xl:table-cell"><?= e($subKlas ?: '-') ?></td>
                    <td class="py-3 px-4 text-slate-600 dark:text-slate-400 hidden xl:table-cell"><?= e($kual['name'] ?? '-') ?></td>
                    <td class="py-3 px-4 text-slate-600 dark:text-slate-400 hidden xl:table-cell"><?= e($kual['kode'] ?? '-') ?></td>
                    <td class="py-3 px-4 text-slate-600 dark:text-slate-400 hidden lg:table-cell"><?= format_date($s['input_date']) ?></td>
                    <td class="py-3 px-4 text-right font-semibold <?= $s['keuntungan'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500' ?>"><?= format_rupiah($s['keuntungan']) ?></td>
                    <td class="py-3 px-4 text-right">
                        <button type="button" onclick="deleteSubmission('<?= e($s['id']) ?>')" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-medium text-sm">Hapus</button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($submissions)): ?>
                <tr><td colspan="12" class="text-center py-8 text-slate-400 dark:text-slate-500">Belum ada data</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4" style="background:rgba(0,0,0,.5)">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
        <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
            <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Hapus Data?</h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">Data input akan dihapus permanen dan tidak dapat dikembalikan.</p>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 font-medium text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition">Batal</button>
            <button id="confirmDeleteBtn" onclick="confirmDelete()" class="flex-1 px-4 py-2.5 rounded-xl bg-red-600 text-white font-medium text-sm hover:bg-red-700 transition">Ya, Hapus</button>
        </div>
    </div>
</div>

<script>
function filterTable() {
    const term = (document.getElementById('searchInput').value || '').toLowerCase();
    const typeFilter = document.getElementById('filterType').value;
    const marketingFilter = document.getElementById('filterMarketing')?.value || '';

    document.querySelectorAll('.submission-row').forEach(row => {
        const matchSearch = !term || row.dataset.search.includes(term);
        const matchType = !typeFilter || row.dataset.type === typeFilter;
        const matchMarketing = !marketingFilter || row.dataset.marketing === marketingFilter;
        row.style.display = (matchSearch && matchType && matchMarketing) ? '' : 'none';
    });
}

let pendingDeleteId = null;

function deleteSubmission(id) {
    pendingDeleteId = id;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    pendingDeleteId = null;
    document.getElementById('deleteModal').classList.add('hidden');
}

function confirmDelete() {
    if (!pendingDeleteId) return;
    const btn = document.getElementById('confirmDeleteBtn');
    btn.disabled = true;
    btn.textContent = 'Menghapus...';

    const csrfToken = '<?= csrf_token() ?>';
    const url = baseUrl('/submissions/' + pendingDeleteId + '/delete');

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: '_csrf=' + encodeURIComponent(csrfToken),
        redirect: 'follow'
    })
    .then(response => {
        // Regardless of response, reload the page
        window.location.href = baseUrl('/submissions');
    })
    .catch(err => {
        console.error('Delete error:', err);
        window.location.href = baseUrl('/submissions');
    });
}
</script>

