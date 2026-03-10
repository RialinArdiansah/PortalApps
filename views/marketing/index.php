<?php $pageTitle = 'Manajemen Marketing'; ?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Manajemen Marketing</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola daftar nama marketing</p>
    </div>
    <button onclick="openModal('addMktModal')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition shadow-lg shadow-indigo-500/30 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Marketing
    </button>
</div>

<div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr class="text-left text-slate-500 dark:text-slate-400">
                    <th class="px-6 py-4 font-medium">#</th>
                    <th class="px-6 py-4 font-medium">Nama Marketing</th>
                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                <?php foreach ($marketingNames as $i => $mk): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                    <td class="px-6 py-4 text-slate-500"><?= $i + 1 ?></td>
                    <td class="px-6 py-4 font-semibold text-slate-800 dark:text-white"><?= e($mk['name']) ?></td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="editMkt('<?= e($mk['id']) ?>','<?= e($mk['name']) ?>')" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm mr-3">Edit</button>
                        <button type="button" onclick="deleteMkt('<?= e($mk['id']) ?>')" class="text-red-500 hover:underline text-sm">Hapus</button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($marketingNames)): ?>
                <tr><td colspan="3" class="px-6 py-8 text-center text-slate-400">Belum ada data</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div id="addMktModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('addMktModal')"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Tambah Marketing</h3>
            <form method="POST" action="<?= url('/marketing/store') ?>" class="space-y-4">
                <?= csrf_field() ?>
                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Marketing</label><input name="name" required class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white" placeholder="Masukkan nama marketing"></div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal('addMktModal')" class="flex-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 py-2.5 rounded-xl text-sm font-medium transition">Batal</button>
                    <button type="submit" class="flex-1 bg-indigo-600 text-white py-2.5 rounded-xl text-sm font-medium hover:bg-indigo-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editMktModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('editMktModal')"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Edit Marketing</h3>
            <form id="editMktForm" method="POST" class="space-y-4">
                <?= csrf_field() ?>
                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Marketing</label><input name="name" id="edit_mkt_name" required class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white"></div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal('editMktModal')" class="flex-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 py-2.5 rounded-xl text-sm font-medium transition">Batal</button>
                    <button type="submit" class="flex-1 bg-indigo-600 text-white py-2.5 rounded-xl text-sm font-medium hover:bg-indigo-700 transition">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4" style="background:rgba(0,0,0,.5)">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center text-slate-800 dark:text-white relative">
        <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
            <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <h3 class="text-lg font-bold mb-2">Hapus Marketing?</h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">Aksi ini tidak dapat dibatalkan.</p>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 font-medium text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition">Batal</button>
            <button id="confirmDeleteBtn" onclick="confirmDelete()" class="flex-1 px-4 py-2.5 rounded-xl bg-red-600 text-white font-medium text-sm hover:bg-red-700 transition">Ya, Hapus</button>
        </div>
    </div>
</div>

<script>
function editMkt(id, name) {
    document.getElementById('editMktForm').action = baseUrl('/marketing/' + id + '/update');
    document.getElementById('edit_mkt_name').value = name;
    openModal('editMktModal');
}

let pendingDeleteId = null;

function deleteMkt(id) {
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
    const url = baseUrl('/marketing/' + pendingDeleteId + '/delete');

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: '_csrf=' + encodeURIComponent(csrfToken),
        redirect: 'follow'
    })
    .then(() => window.location.href = baseUrl('/marketing'))
    .catch(err => {
        console.error('Delete error:', err);
        window.location.href = baseUrl('/marketing');
    });
}
</script>
