<?php $pageTitle = 'Entri Transaksi'; ?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Entri Transaksi</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Catat transaksi keuangan</p>
    </div>
    <button onclick="openModal('addTxModal')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition shadow-lg shadow-indigo-500/30 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Transaksi
    </button>
</div>

<div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr class="text-left text-slate-500 dark:text-slate-400">
                    <th class="px-4 py-4 font-medium">Tanggal</th>
                    <th class="px-4 py-4 font-medium">Nama Transaksi</th>
                    <th class="px-4 py-4 font-medium">Tipe</th>
                    <th class="px-4 py-4 font-medium text-right">Biaya</th>
                    <th class="px-4 py-4 font-medium">Bukti</th>
                    <?php if (Auth::canViewAll()): ?><th class="px-4 py-4 font-medium">Oleh</th><?php endif; ?>
                    <th class="px-4 py-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                <?php foreach ($transactions as $tx):
                    $typeColor = match($tx['transaction_type']) {
                        'Keluar' => 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300',
                        'Tabungan' => 'bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300',
                        'Kas' => 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300',
                        default => 'bg-slate-100 text-slate-700',
                    };
                ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300"><?= format_date($tx['transaction_date']) ?></td>
                    <td class="px-4 py-3 font-semibold text-slate-800 dark:text-white"><?= e($tx['transaction_name']) ?></td>
                    <td class="px-4 py-3"><span class="<?= $typeColor ?> px-2 py-0.5 rounded-lg text-xs font-medium"><?= e($tx['transaction_type']) ?></span></td>
                    <td class="px-4 py-3 text-right font-medium text-slate-800 dark:text-white"><?= format_rupiah($tx['cost']) ?></td>
                    <td class="px-4 py-3 text-slate-500 text-xs"><?= e($tx['proof'] ?? '-') ?></td>
                    <?php if (Auth::canViewAll()): ?><td class="px-4 py-3 text-slate-500 text-xs"><?= e($tx['submitted_by_name'] ?? '-') ?></td><?php endif; ?>
                    <td class="px-4 py-3 text-right">
                        <button onclick="editTx(<?= htmlspecialchars(json_encode($tx), ENT_QUOTES) ?>)" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm mr-2">Edit</button>
                        <button type="button" onclick="deleteTx('<?= e($tx['id']) ?>')" class="text-red-500 hover:underline text-sm">Hapus</button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($transactions)): ?>
                <tr><td colspan="<?= Auth::canViewAll() ? 7 : 6 ?>" class="px-6 py-8 text-center text-slate-400">Belum ada data</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Transaction Modal -->
<div id="addTxModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('addTxModal')"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Tambah Transaksi</h3>
            <form method="POST" action="<?= url('/transactions/store') ?>" class="space-y-4">
                <?= csrf_field() ?>
                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tanggal</label><input name="transaction_date" type="date" value="<?= date('Y-m-d') ?>" required class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white"></div>
                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Transaksi</label><input name="transaction_name" required class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white" placeholder="Deskripsi transaksi"></div>
                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Biaya</label><input name="cost" type="number" value="0" required class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white"></div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tipe Transaksi</label>
                    <select name="transaction_type" required class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white">
                        <option value="Keluar">Keluar</option>
                        <option value="Tabungan">Tabungan</option>
                        <option value="Kas">Kas</option>
                    </select>
                </div>
                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Bukti (opsional)</label><input name="proof" class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white" placeholder="Link atau keterangan bukti"></div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal('addTxModal')" class="flex-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 py-2.5 rounded-xl text-sm font-medium transition">Batal</button>
                    <button type="submit" class="flex-1 bg-indigo-600 text-white py-2.5 rounded-xl text-sm font-medium hover:bg-indigo-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Transaction Modal -->
<div id="editTxModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('editTxModal')"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Edit Transaksi</h3>
            <form id="editTxForm" method="POST" class="space-y-4">
                <?= csrf_field() ?>
                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tanggal</label><input name="transaction_date" id="edit_tx_date" type="date" required class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white"></div>
                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Transaksi</label><input name="transaction_name" id="edit_tx_name" required class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white"></div>
                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Biaya</label><input name="cost" id="edit_tx_cost" type="number" required class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white"></div>
                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tipe</label><select name="transaction_type" id="edit_tx_type" class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white"><option value="Keluar">Keluar</option><option value="Tabungan">Tabungan</option><option value="Kas">Kas</option></select></div>
                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Bukti</label><input name="proof" id="edit_tx_proof" class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white"></div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal('editTxModal')" class="flex-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 py-2.5 rounded-xl text-sm font-medium transition">Batal</button>
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
        <h3 class="text-lg font-bold mb-2">Hapus Transaksi?</h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">Aksi ini tidak dapat dibatalkan.</p>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 font-medium text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition">Batal</button>
            <button id="confirmDeleteBtn" onclick="confirmDelete()" class="flex-1 px-4 py-2.5 rounded-xl bg-red-600 text-white font-medium text-sm hover:bg-red-700 transition">Ya, Hapus</button>
        </div>
    </div>
</div>

<script>
function editTx(tx) {
    document.getElementById('editTxForm').action = baseUrl('/transactions/' + tx.id + '/update');
    document.getElementById('edit_tx_date').value = tx.transaction_date;
    document.getElementById('edit_tx_name').value = tx.transaction_name;
    document.getElementById('edit_tx_cost').value = tx.cost;
    document.getElementById('edit_tx_type').value = tx.transaction_type;
    document.getElementById('edit_tx_proof').value = tx.proof || '';
    openModal('editTxModal');
}

let pendingDeleteId = null;

function deleteTx(id) {
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
    const url = baseUrl('/transactions/' + pendingDeleteId + '/delete');

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: '_csrf=' + encodeURIComponent(csrfToken),
        redirect: 'follow'
    })
    .then(() => window.location.href = baseUrl('/transactions'))
    .catch(err => {
        console.error('Delete error:', err);
        window.location.href = baseUrl('/transactions');
    });
}
</script>
