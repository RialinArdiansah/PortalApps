<?php $pageTitle = 'Manajemen Pengguna'; $roles = ['Super admin','admin','manager','karyawan','marketing','mitra']; ?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Manajemen Pengguna</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola akun pengguna sistem</p>
    </div>
    <button onclick="openModal('addUserModal')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition shadow-lg shadow-indigo-500/30 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Pengguna
    </button>
</div>

<!-- Users Table -->
<div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr class="text-left text-slate-500 dark:text-slate-400">
                    <th class="px-6 py-4 font-medium">Nama</th>
                    <th class="px-6 py-4 font-medium">Username</th>
                    <th class="px-6 py-4 font-medium">Email</th>
                    <th class="px-6 py-4 font-medium">Role</th>
                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                <?php foreach ($users as $u): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                    <td class="px-6 py-4 font-semibold text-slate-800 dark:text-white"><?= e($u['full_name']) ?></td>
                    <td class="px-6 py-4 text-slate-600 dark:text-slate-300"><?= e($u['username']) ?></td>
                    <td class="px-6 py-4 text-slate-600 dark:text-slate-300"><?= e($u['email']) ?></td>
                    <td class="px-6 py-4"><span class="bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 px-2.5 py-1 rounded-lg text-xs font-medium"><?= e($u['role']) ?></span></td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="editUser(<?= htmlspecialchars(json_encode($u), ENT_QUOTES) ?>)" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm mr-3">Edit</button>
                        <button type="button" onclick="deleteUser('<?= e($u['id']) ?>')" class="text-red-500 hover:underline text-sm">Hapus</button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?>
                <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400">Belum ada data pengguna</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('addUserModal')"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md p-6 relative">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Tambah Pengguna</h3>
            <form method="POST" action="<?= url('/users/store') ?>" class="space-y-4">
                <?= csrf_field() ?>
                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap</label><input name="full_name" required class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white"></div>
                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Username</label><input name="username" required class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white"></div>
                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email</label><input name="email" type="email" required class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white"></div>
                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Password</label><input name="password" type="password" required class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white"></div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Role</label>
                    <select name="role" class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white">
                        <?php foreach ($roles as $r): ?><option value="<?= e($r) ?>"><?= e($r) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal('addUserModal')" class="flex-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-600 transition">Batal</button>
                    <button type="submit" class="flex-1 bg-indigo-600 text-white py-2.5 rounded-xl text-sm font-medium hover:bg-indigo-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('editUserModal')"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md p-6 relative">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Edit Pengguna</h3>
            <form id="editUserForm" method="POST" class="space-y-4">
                <?= csrf_field() ?>
                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap</label><input name="full_name" id="edit_full_name" required class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white"></div>
                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Username</label><input name="username" id="edit_username" required class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white"></div>
                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email</label><input name="email" id="edit_email" type="email" required class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white"></div>
                <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Password Baru (kosongkan jika tidak diubah)</label><input name="password" type="password" class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white"></div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Role</label>
                    <select name="role" id="edit_role" class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white">
                        <?php foreach ($roles as $r): ?><option value="<?= e($r) ?>"><?= e($r) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal('editUserModal')" class="flex-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-600 transition">Batal</button>
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
        <h3 class="text-lg font-bold mb-2">Hapus Pengguna?</h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">Aksi ini tidak dapat dibatalkan.</p>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 font-medium text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition">Batal</button>
            <button id="confirmDeleteBtn" onclick="confirmDelete()" class="flex-1 px-4 py-2.5 rounded-xl bg-red-600 text-white font-medium text-sm hover:bg-red-700 transition">Ya, Hapus</button>
        </div>
    </div>
</div>

<script>
function editUser(user) {
    document.getElementById('editUserForm').action = baseUrl('/users/' + user.id + '/update');
    document.getElementById('edit_full_name').value = user.full_name;
    document.getElementById('edit_username').value = user.username;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_role').value = user.role;
    openModal('editUserModal');
}

let pendingDeleteId = null;

function deleteUser(id) {
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
    const url = baseUrl('/users/' + pendingDeleteId + '/delete');

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: '_csrf=' + encodeURIComponent(csrfToken),
        redirect: 'follow'
    })
    .then(() => window.location.href = baseUrl('/users'))
    .catch(err => {
        console.error('Delete error:', err);
        window.location.href = baseUrl('/users');
    });
}
</script>
