<?php $pageTitle = 'Pengaturan'; $user = Auth::user(); ?>

<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-white">Pengaturan</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola profil dan data sistem</p>
    </div>

    <div class="space-y-6">
        <!-- Profil Pengguna -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 transition-colors">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h2 class="text-lg font-semibold text-slate-800 dark:text-white">Profil Pengguna</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-slate-500 dark:text-slate-400 mb-0.5">Nama Lengkap</p>
                    <p class="font-semibold text-slate-800 dark:text-white"><?= e($user['full_name']) ?></p>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400 mb-0.5">Username</p>
                    <p class="font-semibold text-slate-800 dark:text-white"><?= e($user['username']) ?></p>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400 mb-0.5">Email</p>
                    <p class="font-semibold text-slate-800 dark:text-white"><?= e($user['email']) ?></p>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400 mb-0.5">Peran</p>
                    <span class="bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 px-2.5 py-1 rounded-lg text-xs font-medium"><?= e($user['role']) ?></span>
                </div>
            </div>
        </div>

        <!-- Backup & Import Data -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 transition-colors">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </div>
                <h2 class="text-lg font-semibold text-slate-800 dark:text-white">Backup & Import Data</h2>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-5">Ekspor seluruh data sistem dalam format JSON atau impor dari file backup sebelumnya.</p>

            <div class="flex flex-col sm:flex-row gap-4">
                <!-- Export Button -->
                <a href="<?= url('/settings/export') ?>" class="flex-1 flex items-center justify-center gap-2 bg-indigo-600 text-white font-semibold py-3 px-5 rounded-xl hover:bg-indigo-700 transition shadow-md text-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Ekspor Data (JSON)
                </a>

                <!-- Import Button -->
                <button type="button" onclick="document.getElementById('importFileInput').click()" class="flex-1 flex items-center justify-center gap-2 bg-orange-500 text-white font-semibold py-3 px-5 rounded-xl hover:bg-orange-600 transition shadow-md">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Impor Data (JSON)
                </button>
            </div>

            <!-- Hidden file input + form -->
            <form id="importForm" method="POST" action="<?= url('/settings/import') ?>" enctype="multipart/form-data" class="hidden">
                <?= csrf_field() ?>
                <input type="file" id="importFileInput" name="json_file" accept=".json" onchange="handleImportFile(this)">
            </form>

            <div class="mt-5 p-4 bg-slate-50 dark:bg-slate-700/30 rounded-xl border border-slate-200 dark:border-slate-600/50">
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    <strong class="text-slate-600 dark:text-slate-300">ℹ️ Catatan:</strong> Ekspor akan mengunduh seluruh data sistem (pengguna, sertifikat, input data, transaksi, dll.) dalam format JSON.
                    Impor akan <strong>mengganti</strong> seluruh data yang ada dengan data dari file backup. Pastikan Anda memiliki backup sebelum melakukan impor.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Import Confirmation Modal -->
<div id="importModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4" style="background:rgba(0,0,0,.5)">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
        <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
            <svg class="w-7 h-7 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
        </div>
        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Impor Data?</h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm mb-2">File: <strong id="importFileName" class="text-slate-700 dark:text-slate-300"></strong></p>
        <p class="text-red-500 dark:text-red-400 text-xs mb-5">⚠️ Semua data yang ada akan diganti dengan data dari file backup. Proses ini tidak dapat dibatalkan.</p>
        <div class="flex gap-3">
            <button onclick="cancelImport()" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 font-medium text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition">Batal</button>
            <button onclick="submitImport()" id="confirmImportBtn" class="flex-1 px-4 py-2.5 rounded-xl bg-orange-500 text-white font-medium text-sm hover:bg-orange-600 transition">Ya, Impor</button>
        </div>
    </div>
</div>

<script>
function handleImportFile(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    document.getElementById('importFileName').textContent = file.name;
    document.getElementById('importModal').classList.remove('hidden');
}

function cancelImport() {
    document.getElementById('importModal').classList.add('hidden');
    document.getElementById('importFileInput').value = '';
}

function submitImport() {
    const btn = document.getElementById('confirmImportBtn');
    btn.disabled = true;
    btn.textContent = 'Mengimpor...';
    document.getElementById('importForm').submit();
}
</script>
