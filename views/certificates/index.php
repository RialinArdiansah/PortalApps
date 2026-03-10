<?php $pageTitle = 'Manajemen Sertifikat'; ?>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
    <div>
        <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-white">Manajemen Sertifikat</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola jenis sertifikat dan referensi data</p>
    </div>
    <button onclick="openModal('addCertModal')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl text-sm font-semibold transition shadow-md flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Sertifikat
    </button>
</div>

<!-- Flash Messages -->
<?php if ($flash = $_SESSION['flash'] ?? null): unset($_SESSION['flash']); ?>
<div class="mb-4 px-4 py-3 rounded-xl text-sm <?= $flash['type'] === 'success' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-700' : 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-700' ?>">
    <?= e($flash['message']) ?>
</div>
<?php endif; ?>

<!-- Certificates Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
    <?php foreach ($certificates as $cert):
        $slug = $cert['sbu_type_slug'] ?? '';
        $sbuType = null;
        foreach ($sbuTypes as $sbt) { if ($sbt['slug'] === $slug) { $sbuType = $sbt; break; } }
        $subMenus = json_decode($cert['sub_menus'] ?? '[]', true) ?: [];
        $menuConfig = $sbuType ? json_decode($sbuType['menu_config'] ?? '{}', true) : [];
        $isAdvanced = !empty($slug);

        // Menu summary
        $menuParts = [];
        if (!empty($menuConfig['asosiasi'])) $menuParts[] = 'Asosiasi';
        if (!empty($menuConfig['klasifikasi'])) $menuParts[] = 'Klasifikasi';
        if (!empty($menuConfig['kualifikasi'])) $menuParts[] = ($menuConfig['kualifikasiLabel'] ?? 'Kualifikasi');
        if (!empty($menuConfig['biayaSetor'])) $menuParts[] = 'Biaya Setor Kantor';
        if (!empty($menuConfig['biayaLainnya'])) $menuParts[] = 'Biaya Lainnya';
        if (!empty($menuConfig['kodeField']['enabled'])) $menuParts[] = ($menuConfig['kodeField']['label'] ?? 'Kode');
        $menuSummary = !empty($menuParts) ? implode(', ', $menuParts) : 'Konfigurasi menu belum ditentukan';

        // Reference data stats
        $refData = $referenceData[$slug] ?? null;
    ?>
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-md hover:shadow-lg transition p-5 sm:p-6 border border-slate-100 dark:border-slate-700">
        <div class="flex justify-between items-start mb-3">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white"><?= e($cert['name']) ?></h3>
            <?php if ($isAdvanced): ?>
            <span class="bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 text-xs font-medium px-2 py-1 rounded-lg whitespace-nowrap">Menu Bertingkat</span>
            <?php endif; ?>
        </div>

        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
            <?php if ($isAdvanced): ?>
                <?= e($menuSummary) ?>
            <?php elseif (!empty($subMenus)): ?>
                <?= e(implode(', ', $subMenus)) ?>
            <?php else: ?>
                Tidak ada sub-menu
            <?php endif; ?>
        </p>

        <?php if ($refData):
            $asosCount = count($refData['asosiasi'] ?? []);
            $klasCount = count($refData['klasifikasi'] ?? []);
            $biayaCount = count($refData['biayaItems'] ?? []);
        ?>
        <div class="flex gap-3 mb-4 pt-3 border-t border-slate-100 dark:border-slate-700/50 text-xs text-slate-400">
            <?php if ($asosCount): ?><span>📋 <?= $asosCount ?> Asosiasi</span><?php endif; ?>
            <?php if ($klasCount): ?><span>📂 <?= $klasCount ?> Klasifikasi</span><?php endif; ?>
            <?php if ($biayaCount): ?><span>💰 <?= $biayaCount ?> Item Biaya</span><?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-2">
            <?php if ($isAdvanced): ?>
            <button onclick="openSbuAdmin('<?= e($slug) ?>')"
                class="flex-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition">
                Kelola Menu
            </button>
            <?php endif; ?>
            <button onclick="openEditCert(<?= htmlspecialchars(json_encode([
                'cert' => $cert,
                'menuConfig' => $menuConfig,
                'subMenus' => $subMenus
            ]), ENT_QUOTES) ?>)"
                class="<?= $isAdvanced ? '' : 'flex-1 ' ?>bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                Edit
            </button>
            <button type="button" onclick="deleteCert('<?= e($cert['id']) ?>')" class="bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-red-100 dark:hover:bg-red-900/50 transition">
                Hapus
            </button>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if (empty($certificates)): ?>
    <div class="col-span-full text-center py-12 text-slate-400">Belum ada sertifikat</div>
    <?php endif; ?>
</div>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- Add Certificate Modal -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<div id="addCertModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('addCertModal')"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Tambah Sertifikat</h3>
            <form method="POST" action="<?= url('/certificates/store') ?>" class="space-y-4">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Sertifikat</label>
                    <input name="name" required class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white" placeholder="Masukkan nama sertifikat">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal('addCertModal')" class="flex-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-600 transition">Batal</button>
                    <button type="submit" class="flex-1 bg-indigo-600 text-white py-2.5 rounded-xl text-sm font-medium hover:bg-indigo-700 transition shadow-md">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- Edit Certificate Modal (with Menu Config) -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<div id="editCertModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('editCertModal')"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-5">Edit Sertifikat</h3>
            <form id="editCertForm" method="POST" class="space-y-5">
                <?= csrf_field() ?>
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Sertifikat</label>
                    <input name="name" id="edit_cert_name" required class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white">
                </div>

                <!-- Menu Config Section -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">Menu Bertingkat</label>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mb-3">Pilih menu yang akan tersedia di "Kelola Menu" untuk sertifikat ini.</p>

                    <div class="space-y-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4 border border-slate-200 dark:border-slate-600">
                        <!-- Asosiasi -->
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="mc_asosiasi" class="w-5 h-5 text-indigo-600 bg-white dark:bg-slate-700 border-slate-300 dark:border-slate-500 rounded focus:ring-indigo-500 cursor-pointer">
                            <span class="ml-3 text-sm font-medium text-slate-700 dark:text-slate-300">Asosiasi</span>
                        </label>

                        <!-- Klasifikasi -->
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="mc_klasifikasi" class="w-5 h-5 text-indigo-600 bg-white dark:bg-slate-700 border-slate-300 dark:border-slate-500 rounded focus:ring-indigo-500 cursor-pointer">
                            <span class="ml-3 text-sm font-medium text-slate-700 dark:text-slate-300">Klasifikasi</span>
                        </label>

                        <!-- Kualifikasi -->
                        <div>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" id="mc_kualifikasi" class="w-5 h-5 text-indigo-600 bg-white dark:bg-slate-700 border-slate-300 dark:border-slate-500 rounded focus:ring-indigo-500 cursor-pointer" onchange="document.getElementById('mc_kualifikasi_sub').style.display = this.checked ? 'block' : 'none'">
                                <span class="ml-3 text-sm font-medium text-slate-700 dark:text-slate-300">Kualifikasi / Jenjang</span>
                            </label>
                            <div id="mc_kualifikasi_sub" class="ml-8 mt-2 space-y-2" style="display:none">
                                <div>
                                    <label class="text-xs text-slate-500 dark:text-slate-400 mb-1 block">Label kustom:</label>
                                    <input type="text" id="mc_kualifikasiLabel" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-600 text-slate-900 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="mis. Kualifikasi, Jenjang, Ekuitas KAP">
                                </div>
                                <div>
                                    <label class="text-xs text-slate-500 dark:text-slate-400 mb-1 block">Label nama partner (biaya di kualifikasi):</label>
                                    <input type="text" id="mc_biayaSetorLabel" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-600 text-slate-900 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="mis. Stor Ke RIA, Stor Ke P3SM, Stor Ke Alam">
                                </div>
                            </div>
                        </div>

                        <!-- Biaya Setor -->
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="mc_biayaSetor" class="w-5 h-5 text-indigo-600 bg-white dark:bg-slate-700 border-slate-300 dark:border-slate-500 rounded focus:ring-indigo-500 cursor-pointer">
                            <span class="ml-3 text-sm font-medium text-slate-700 dark:text-slate-300">Biaya Setor Kantor</span>
                        </label>

                        <!-- Biaya Lainnya -->
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="mc_biayaLainnya" class="w-5 h-5 text-indigo-600 bg-white dark:bg-slate-700 border-slate-300 dark:border-slate-500 rounded focus:ring-indigo-500 cursor-pointer">
                            <span class="ml-3 text-sm font-medium text-slate-700 dark:text-slate-300">Biaya Lainnya</span>
                        </label>

                        <!-- Kode Field -->
                        <div class="border-t border-slate-200 dark:border-slate-600 pt-3 mt-1">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" id="mc_kodeEnabled" class="w-5 h-5 text-indigo-600 bg-white dark:bg-slate-700 border-slate-300 dark:border-slate-500 rounded focus:ring-indigo-500 cursor-pointer" onchange="document.getElementById('mc_kode_sub').style.display = this.checked ? 'block' : 'none'">
                                <span class="ml-3 text-sm font-medium text-slate-700 dark:text-slate-300">Extra Text Field (Kode / Keterangan)</span>
                            </label>
                            <div id="mc_kode_sub" class="ml-8 mt-2" style="display:none">
                                <label class="text-xs text-slate-500 dark:text-slate-400 mb-1 block">Label field:</label>
                                <input type="text" id="mc_kodeLabel" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-600 text-slate-900 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="mis. Kode, Keterangan">
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="menuConfig" id="edit_menuConfig">

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal('editCertModal')" class="flex-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-600 transition">Batal</button>
                    <button type="submit" class="flex-1 bg-indigo-600 text-white py-2.5 rounded-xl text-sm font-medium hover:bg-indigo-700 transition shadow-md">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- SBU Admin Modal (Kelola Menu) -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<div id="sbuAdminModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeSbuAdmin()"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-3xl p-6 max-h-[90vh] overflow-y-auto">
            <h3 id="sbuAdminTitle" class="text-xl font-bold text-slate-800 dark:text-white mb-5">Kelola Menu</h3>

            <!-- Toast -->
            <div id="sbuToast" class="hidden mb-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-400 text-sm px-4 py-2 rounded-xl text-center"></div>

            <!-- Asosiasi Section -->
            <div id="asosiasiSection" class="mb-5 hidden">
                <div class="flex justify-between items-center mb-2">
                    <label class="text-slate-700 dark:text-slate-300 font-medium">Asosiasi</label>
                    <button type="button" onclick="deleteSub()" id="btnDeleteSub" class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hidden" title="Hapus asosiasi">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    </button>
                </div>
                <select id="selAsosiasi" onchange="onAsosiasiChange()" class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">-- Pilih Asosiasi --</option>
                </select>
                <div class="flex mt-2 space-x-2">
                    <input type="text" id="newSubName" placeholder="Nama asosiasi baru" class="flex-grow px-3 py-2 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none" onkeydown="if(event.key==='Enter')addSub()">
                    <button type="button" onclick="addSub()" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-indigo-700 transition">Tambah</button>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mb-4">
                <div class="border-b border-slate-200 dark:border-slate-700">
                    <nav id="sbuTabs" class="-mb-px flex space-x-2 overflow-x-auto"></nav>
                </div>
            </div>

            <!-- Tab Content: Klasifikasi -->
            <div id="tabKlasifikasi" class="hidden space-y-4">
                <div class="flex justify-between items-center mb-2">
                    <label class="text-slate-700 dark:text-slate-300 font-medium">Klasifikasi</label>
                    <button type="button" onclick="deleteKlasifikasi()" id="btnDeleteKlas" class="text-red-500 dark:text-red-400 hover:text-red-700 hidden">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    </button>
                </div>
                <select id="selKlasifikasi" onchange="onKlasifikasiChange()" class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">-- Pilih Klasifikasi --</option>
                </select>
                <div class="flex space-x-2">
                    <input type="text" id="newKlasName" placeholder="Nama klasifikasi baru" class="flex-grow px-3 py-2 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none" onkeydown="if(event.key==='Enter')addKlasifikasi()">
                    <button type="button" onclick="addKlasifikasi()" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-indigo-700 transition">Tambah</button>
                </div>
                <!-- Sub-klasifikasi -->
                <div id="subKlasSection" class="hidden mt-4 space-y-3">
                    <label class="block text-slate-700 dark:text-slate-300 font-medium">Sub-klasifikasi (satu per baris)</label>
                    <textarea id="subKlasText" class="w-full h-32 p-3 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                    <button type="button" onclick="saveSubKlasifikasi()" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-indigo-700 transition">Simpan Sub-klasifikasi</button>
                </div>
            </div>

            <!-- Tab Content: Biaya Tables (kualifikasi, biayaSetor, biayaLainnya) -->
            <div id="tabBiaya" class="hidden">
                <div class="flex justify-between items-center mb-3">
                    <p id="biayaSubtitle" class="text-sm text-slate-500 dark:text-slate-400"></p>
                    <button type="button" onclick="openBiayaForm(null)" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-indigo-700 transition">
                        Tambah Data
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-700/50">
                            <tr id="biayaThead"></tr>
                        </thead>
                        <tbody id="biayaTbody" class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700"></tbody>
                    </table>
                </div>
            </div>

            <!-- Biaya Edit Form (inline) -->
            <div id="biayaEditForm" class="hidden mt-4 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl p-4 space-y-3">
                <h4 id="biayaFormTitle" class="font-semibold text-slate-700 dark:text-white">Tambah Data</h4>
                <div id="biayaNameField">
                    <label class="block text-slate-600 dark:text-slate-400 text-sm mb-1">Nama</label>
                    <input type="text" id="biayaName" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div id="biayaNameDropdownField" class="hidden">
                    <label class="block text-slate-600 dark:text-slate-400 text-sm mb-1">Pilih Kualifikasi</label>
                    <select id="biayaNameDropdown" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"></select>
                </div>
                <div id="biayaKodeField" class="hidden">
                    <label id="biayaKodeLabel" class="block text-slate-600 dark:text-slate-400 text-sm mb-1">Kode</label>
                    <input type="text" id="biayaKode" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label id="biayaCostLabel" class="block text-slate-600 dark:text-slate-400 text-sm mb-1">Biaya (Rp)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-sm font-medium">Rp</span>
                        <input type="text" id="biayaCost" inputmode="numeric" class="w-full pl-10 px-3 py-2 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="0" oninput="formatBiayaInput(this)">
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeBiayaForm()" class="px-4 py-2 rounded-xl text-sm font-semibold bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-200 hover:bg-slate-300 dark:hover:bg-slate-500 transition">Batal</button>
                    <button type="button" onclick="saveBiaya()" class="px-4 py-2 rounded-xl text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-700 transition">Simpan</button>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-3 pt-5 mt-5 border-t border-slate-200 dark:border-slate-700">
                <button type="button" onclick="closeSbuAdmin()" class="px-6 py-3 rounded-xl font-semibold bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-200 hover:bg-slate-300 dark:hover:bg-slate-500 transition">Tutup</button>
                <button type="button" onclick="saveAllReferenceData()" id="btnSaveAll" class="px-6 py-3 rounded-xl font-semibold bg-indigo-600 text-white hover:bg-indigo-700 transition shadow-md disabled:opacity-50">Simpan Semua</button>
            </div>
        </div>
    </div>
</div>

<script>
// ═══════════════════════════════════════════════════════════════════
// Edit Certificate Modal
// ═══════════════════════════════════════════════════════════════════
let currentEditId = null;
let currentEditSlug = null;

function openEditCert(data) {
    const cert = data.cert;
    const mc = data.menuConfig || {};
    currentEditId = cert.id;
    currentEditSlug = cert.sbu_type_slug;

    document.getElementById('editCertForm').action = baseUrl('/certificates/' + cert.id + '/update');
    document.getElementById('edit_cert_name').value = cert.name;

    // Set menu config checkboxes
    document.getElementById('mc_asosiasi').checked = !!mc.asosiasi;
    document.getElementById('mc_klasifikasi').checked = !!mc.klasifikasi;
    document.getElementById('mc_kualifikasi').checked = !!mc.kualifikasi;
    document.getElementById('mc_biayaSetor').checked = !!mc.biayaSetor;
    document.getElementById('mc_biayaLainnya').checked = !!mc.biayaLainnya;
    document.getElementById('mc_kodeEnabled').checked = !!(mc.kodeField && mc.kodeField.enabled);

    document.getElementById('mc_kualifikasiLabel').value = mc.kualifikasiLabel || 'Kualifikasi';
    document.getElementById('mc_biayaSetorLabel').value = mc.biayaSetorLabel || '';
    document.getElementById('mc_kodeLabel').value = (mc.kodeField && mc.kodeField.label) || 'Kode';

    // Toggle sub-sections
    document.getElementById('mc_kualifikasi_sub').style.display = mc.kualifikasi ? 'block' : 'none';
    document.getElementById('mc_kode_sub').style.display = (mc.kodeField && mc.kodeField.enabled) ? 'block' : 'none';

    openModal('editCertModal');
}

// Serialize menu config before form submit
document.getElementById('editCertForm').addEventListener('submit', function() {
    const mc = {
        asosiasi: document.getElementById('mc_asosiasi').checked,
        klasifikasi: document.getElementById('mc_klasifikasi').checked,
        kualifikasi: document.getElementById('mc_kualifikasi').checked,
        kualifikasiLabel: document.getElementById('mc_kualifikasiLabel').value || 'Kualifikasi',
        biayaSetor: document.getElementById('mc_biayaSetor').checked,
        biayaSetorLabel: document.getElementById('mc_biayaSetorLabel').value || '',
        biayaLainnya: document.getElementById('mc_biayaLainnya').checked,
        kodeField: {
            enabled: document.getElementById('mc_kodeEnabled').checked,
            label: document.getElementById('mc_kodeLabel').value || 'Kode',
        },
    };
    document.getElementById('edit_menuConfig').value = JSON.stringify(mc);
});

// ═══════════════════════════════════════════════════════════════════
// SBU Admin Modal (Kelola Menu)
// ═══════════════════════════════════════════════════════════════════
let sbuSlug = '';
let sbuMenuConfig = {};
let tempSubs = [];
let tempKlasifikasi = [];
let tempKualifikasi = [];
let tempBiayaSetor = [];
let tempBiayaLainnya = [];
let activeTab = 'klasifikasi';
let biayaEditId = null;
let biayaEditCategory = '';

const titleMap = {
    'konstruksi': 'SBU Konstruksi', 'konsultan': 'SBU Konsultan', 'skk': 'SKK Konstruksi',
    'smap': 'Dokumen SMAP', 'simpk': 'Akun SIMPK dan Alat', 'notaris': 'Notaris',
};

function genId() { return 'id-' + Date.now() + '-' + Math.floor(Math.random() * 10000); }

function showSbuToast(msg) {
    const el = document.getElementById('sbuToast');
    el.textContent = msg;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 2500);
}

function formatRupiah(n) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(n);
}

function formatBiayaInput(el) {
    const raw = el.value.replace(/\D/g, '');
    el.value = raw === '' ? '' : new Intl.NumberFormat('id-ID').format(Number(raw));
    el.dataset.raw = raw;
}

function openSbuAdmin(slug) {
    sbuSlug = slug;
    document.getElementById('sbuAdminTitle').textContent = 'Kelola Menu ' + (titleMap[slug] || slug);
    openModal('sbuAdminModal');

    // Load reference data via AJAX
    fetch(baseUrl('/api/certificates/reference-data/' + slug))
        .then(r => r.json())
        .then(res => {
            if (!res.success) return;
            const d = res.data;
            sbuMenuConfig = d.menuConfig || {};

            tempSubs = (d.asosiasi || []).map(a => ({ id: a.id, name: a.name }));
            tempKlasifikasi = (d.klasifikasi || []).map(k => ({
                id: k.id, name: k.name,
                subKlasifikasi: k.subKlasifikasi || [],
            }));
            tempKualifikasi = (d.kualifikasi || []).map(b => ({ id: b.id, name: b.name, kode: b.kode, biaya: b.biaya }));
            tempBiayaSetor = (d.biayaSetor || []).map(b => ({ id: b.id, name: b.name, kode: b.kode, biaya: b.biaya }));
            tempBiayaLainnya = (d.biayaLainnya || []).map(b => ({ id: b.id, name: b.name, kode: b.kode, biaya: b.biaya }));

            renderSbuAdmin();
        })
        .catch(err => { console.error(err); showSbuToast('Gagal memuat data'); });
}

function closeSbuAdmin() {
    closeModal('sbuAdminModal');
    closeBiayaForm();
}

function renderSbuAdmin() {
    const mc = sbuMenuConfig;

    // Asosiasi
    const asosSection = document.getElementById('asosiasiSection');
    if (mc.asosiasi) {
        asosSection.classList.remove('hidden');
        renderAsosiasiSelect();
    } else {
        asosSection.classList.add('hidden');
    }

    // Tabs
    const tabsEl = document.getElementById('sbuTabs');
    tabsEl.innerHTML = '';
    const tabs = [];
    if (mc.klasifikasi) tabs.push({ label: 'Klasifikasi', tab: 'klasifikasi' });
    if (mc.kualifikasi) tabs.push({ label: mc.kualifikasiLabel || 'Kualifikasi', tab: 'kualifikasi' });
    if (mc.biayaSetor) tabs.push({ label: 'Biaya Setor Kantor', tab: 'biayaSetor' });
    if (mc.biayaLainnya) tabs.push({ label: 'Biaya Lainnya', tab: 'biayaLainnya' });

    if (tabs.length > 0) {
        // Set default active tab
        const validTabs = tabs.map(t => t.tab);
        if (!validTabs.includes(activeTab)) activeTab = validTabs[0];

        tabs.forEach(t => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = t.label;
            btn.className = 'px-4 py-2 text-sm font-medium rounded-t-lg transition-colors ' +
                (activeTab === t.tab
                    ? 'bg-indigo-600 text-white'
                    : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-300 dark:hover:bg-slate-600');
            btn.onclick = () => { activeTab = t.tab; closeBiayaForm(); renderTabContent(); renderTabs(); };
            tabsEl.appendChild(btn);
        });
    }

    renderTabContent();
}

function renderTabs() {
    const mc = sbuMenuConfig;
    const tabsEl = document.getElementById('sbuTabs');
    tabsEl.innerHTML = '';
    const tabs = [];
    if (mc.klasifikasi) tabs.push({ label: 'Klasifikasi', tab: 'klasifikasi' });
    if (mc.kualifikasi) tabs.push({ label: mc.kualifikasiLabel || 'Kualifikasi', tab: 'kualifikasi' });
    if (mc.biayaSetor) tabs.push({ label: 'Biaya Setor Kantor', tab: 'biayaSetor' });
    if (mc.biayaLainnya) tabs.push({ label: 'Biaya Lainnya', tab: 'biayaLainnya' });

    tabs.forEach(t => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = t.label;
        btn.className = 'px-4 py-2 text-sm font-medium rounded-t-lg transition-colors ' +
            (activeTab === t.tab
                ? 'bg-indigo-600 text-white'
                : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-300 dark:hover:bg-slate-600');
        btn.onclick = () => { activeTab = t.tab; closeBiayaForm(); renderTabContent(); renderTabs(); };
        tabsEl.appendChild(btn);
    });
}

function renderTabContent() {
    const klasEl = document.getElementById('tabKlasifikasi');
    const biayaEl = document.getElementById('tabBiaya');

    if (activeTab === 'klasifikasi') {
        klasEl.classList.remove('hidden');
        biayaEl.classList.add('hidden');
        renderKlasifikasiSelect();
    } else {
        klasEl.classList.add('hidden');
        biayaEl.classList.remove('hidden');
        renderBiayaTable(activeTab);
    }
}

// ── Asosiasi ──
function renderAsosiasiSelect() {
    const sel = document.getElementById('selAsosiasi');
    const val = sel.value;
    sel.innerHTML = '<option value="">-- Pilih Asosiasi --</option>';
    tempSubs.forEach(s => {
        const opt = document.createElement('option');
        opt.value = s.id; opt.textContent = s.name;
        sel.appendChild(opt);
    });
    if (val && tempSubs.find(s => s.id === val)) sel.value = val;
    document.getElementById('btnDeleteSub').classList.toggle('hidden', !sel.value);
}

function onAsosiasiChange() {
    document.getElementById('btnDeleteSub').classList.toggle('hidden', !document.getElementById('selAsosiasi').value);
}

function addSub() {
    const name = document.getElementById('newSubName').value.trim();
    if (!name) return;
    tempSubs.push({ id: genId(), name });
    document.getElementById('newSubName').value = '';
    renderAsosiasiSelect();
    showSbuToast('Asosiasi "' + name + '" ditambahkan');
}

function deleteSub() {
    const sel = document.getElementById('selAsosiasi');
    if (!sel.value) return;
    tempSubs = tempSubs.filter(s => s.id !== sel.value);
    sel.value = '';
    renderAsosiasiSelect();
    showSbuToast('Asosiasi dihapus');
}

// ── Klasifikasi ──
function renderKlasifikasiSelect() {
    const sel = document.getElementById('selKlasifikasi');
    const val = sel.value;
    sel.innerHTML = '<option value="">-- Pilih Klasifikasi --</option>';
    tempKlasifikasi.forEach(k => {
        const opt = document.createElement('option');
        opt.value = k.id; opt.textContent = k.name;
        sel.appendChild(opt);
    });
    if (val && tempKlasifikasi.find(k => k.id === val)) sel.value = val;
    onKlasifikasiChange();
}

function onKlasifikasiChange() {
    const id = document.getElementById('selKlasifikasi').value;
    document.getElementById('btnDeleteKlas').classList.toggle('hidden', !id);
    const section = document.getElementById('subKlasSection');
    if (id) {
        const klas = tempKlasifikasi.find(k => k.id === id);
        document.getElementById('subKlasText').value = klas ? klas.subKlasifikasi.join('\n') : '';
        section.classList.remove('hidden');
    } else {
        section.classList.add('hidden');
    }
}

function addKlasifikasi() {
    const name = document.getElementById('newKlasName').value.trim();
    if (!name) return;
    tempKlasifikasi.push({ id: genId(), name, subKlasifikasi: [] });
    document.getElementById('newKlasName').value = '';
    renderKlasifikasiSelect();
    showSbuToast('Klasifikasi "' + name + '" ditambahkan');
}

function deleteKlasifikasi() {
    const id = document.getElementById('selKlasifikasi').value;
    if (!id) return;
    tempKlasifikasi = tempKlasifikasi.filter(k => k.id !== id);
    document.getElementById('selKlasifikasi').value = '';
    renderKlasifikasiSelect();
    showSbuToast('Klasifikasi dihapus');
}

function saveSubKlasifikasi() {
    const id = document.getElementById('selKlasifikasi').value;
    if (!id) return;
    const items = document.getElementById('subKlasText').value.split('\n').map(s => s.trim()).filter(Boolean);
    const klas = tempKlasifikasi.find(k => k.id === id);
    if (klas) klas.subKlasifikasi = items;
    showSbuToast('Sub-klasifikasi disimpan');
}

// ── Biaya Tables ──
function getBiayaList(cat) {
    if (cat === 'kualifikasi') return tempKualifikasi;
    if (cat === 'biayaSetor') return tempBiayaSetor;
    return tempBiayaLainnya;
}

function setBiayaList(cat, list) {
    if (cat === 'kualifikasi') tempKualifikasi = list;
    else if (cat === 'biayaSetor') tempBiayaSetor = list;
    else tempBiayaLainnya = list;
}

function getBiayaLabel(cat) {
    if (cat === 'kualifikasi') return sbuMenuConfig.biayaSetorLabel || 'Biaya Partner';
    if (cat === 'biayaSetor') return 'Stor Kantor';
    return 'Biaya';
}

function renderBiayaTable(cat) {
    const mc = sbuMenuConfig;
    const list = getBiayaList(cat);
    const hasKode = mc.kodeField && mc.kodeField.enabled;
    const biayaLabel = getBiayaLabel(cat);
    const selectedSub = tempSubs.find(s => s.id === document.getElementById('selAsosiasi')?.value);

    // Subtitle
    document.getElementById('biayaSubtitle').innerHTML = 'Data untuk <strong class="text-slate-700 dark:text-white">' + (selectedSub ? selectedSub.name : (titleMap[sbuSlug] || sbuSlug)) + '</strong>';

    // Header
    const thead = document.getElementById('biayaThead');
    thead.innerHTML = '<th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Nama & ' + biayaLabel + '</th>' +
        (hasKode ? '<th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">' + (mc.kodeField.label || 'Kode') + '</th>' : '') +
        '<th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Aksi</th>';

    // Body
    const tbody = document.getElementById('biayaTbody');
    if (list.length === 0) {
        tbody.innerHTML = '<tr><td colspan="' + (hasKode ? 3 : 2) + '" class="text-center py-6 text-slate-400 dark:text-slate-500">Belum ada data</td></tr>';
        return;
    }

    tbody.innerHTML = list.map(item =>
        '<tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">' +
        '<td class="px-4 py-3"><div class="font-medium text-slate-900 dark:text-white text-sm">' + escHtml(item.name) + '</div><div class="text-slate-500 dark:text-slate-400 text-xs">' + biayaLabel + ': ' + formatRupiah(item.biaya) + '</div></td>' +
        (hasKode ? '<td class="px-4 py-3 text-slate-600 dark:text-slate-300 text-sm">' + escHtml(item.kode || '—') + '</td>' : '') +
        '<td class="px-4 py-3 text-right space-x-3">' +
        '<button onclick="openBiayaForm(\'' + item.id + '\')" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 text-sm font-medium">Edit</button>' +
        '<button onclick="deleteBiaya(\'' + item.id + '\')" class="text-red-600 dark:text-red-400 hover:text-red-800 text-sm font-medium">Hapus</button>' +
        '</td></tr>'
    ).join('');
}

function escHtml(s) {
    const d = document.createElement('div');
    d.textContent = s || '';
    return d.innerHTML;
}

// ── Biaya Edit Form ──
function openBiayaForm(id) {
    biayaEditCategory = activeTab;
    biayaEditId = id;
    const form = document.getElementById('biayaEditForm');
    const mc = sbuMenuConfig;
    const hasKode = mc.kodeField && mc.kodeField.enabled;

    document.getElementById('biayaFormTitle').textContent = id ? 'Edit Data' : 'Tambah Data';

    // Show/hide kode field
    document.getElementById('biayaKodeField').classList.toggle('hidden', !hasKode);
    document.getElementById('biayaKodeLabel').textContent = (mc.kodeField && mc.kodeField.label) || 'Kode';

    // Cost label
    document.getElementById('biayaCostLabel').textContent = getBiayaLabel(biayaEditCategory) + ' (Rp)';

    // Show name field or dropdown for biayaSetor
    const showDropdown = biayaEditCategory === 'biayaSetor' && tempKualifikasi.length > 0;
    document.getElementById('biayaNameField').classList.toggle('hidden', showDropdown);
    document.getElementById('biayaNameDropdownField').classList.toggle('hidden', !showDropdown);

    if (showDropdown) {
        const dd = document.getElementById('biayaNameDropdown');
        dd.innerHTML = '<option value="">-- Pilih Kualifikasi --</option>';
        tempKualifikasi.forEach(k => {
            const opt = document.createElement('option');
            opt.value = k.name; opt.textContent = k.name;
            dd.appendChild(opt);
        });
    }

    if (id) {
        const item = getBiayaList(biayaEditCategory).find(b => b.id === id);
        if (item) {
            if (showDropdown) document.getElementById('biayaNameDropdown').value = item.name;
            else document.getElementById('biayaName').value = item.name;
            document.getElementById('biayaKode').value = item.kode || '';
            const costEl = document.getElementById('biayaCost');
            costEl.value = item.biaya ? new Intl.NumberFormat('id-ID').format(item.biaya) : '';
            costEl.dataset.raw = String(item.biaya || 0);
        }
    } else {
        document.getElementById('biayaName').value = '';
        document.getElementById('biayaKode').value = '';
        document.getElementById('biayaCost').value = '';
        document.getElementById('biayaCost').dataset.raw = '0';
        if (showDropdown && tempKualifikasi.length > 0) {
            document.getElementById('biayaNameDropdown').value = tempKualifikasi[0].name;
        }
    }

    form.classList.remove('hidden');
}

function closeBiayaForm() {
    document.getElementById('biayaEditForm').classList.add('hidden');
    biayaEditId = null;
}

function saveBiaya() {
    const showDropdown = biayaEditCategory === 'biayaSetor' && tempKualifikasi.length > 0;
    const name = showDropdown
        ? document.getElementById('biayaNameDropdown').value
        : document.getElementById('biayaName').value.trim();
    const kode = document.getElementById('biayaKode').value.trim() || undefined;
    const raw = document.getElementById('biayaCost').dataset.raw || document.getElementById('biayaCost').value.replace(/\D/g, '');
    const biaya = Number(raw) || 0;

    if (!name) { showSbuToast('Nama wajib diisi'); return; }

    const list = getBiayaList(biayaEditCategory);
    if (biayaEditId) {
        const item = list.find(b => b.id === biayaEditId);
        if (item) { item.name = name; item.kode = kode; item.biaya = biaya; }
        showSbuToast('Data diperbarui');
    } else {
        list.push({ id: genId(), name, kode, biaya });
        showSbuToast('Data ditambahkan');
    }

    closeBiayaForm();
    renderBiayaTable(biayaEditCategory);
}

function deleteBiaya(id) {
    const cat = activeTab;
    setBiayaList(cat, getBiayaList(cat).filter(b => b.id !== id));
    renderBiayaTable(cat);
    showSbuToast('Data dihapus');
}

// ── Save All ──
function saveAllReferenceData() {
    const btn = document.getElementById('btnSaveAll');
    btn.disabled = true;
    btn.textContent = 'Menyimpan...';

    const body = { sbuType: sbuSlug };

    body.sbuData = tempSubs;
    body.klasifikasiData = tempKlasifikasi;
    body.kualifikasiData = tempKualifikasi;
    body.biayaSetorData = tempBiayaSetor;
    body.biayaLainnyaData = tempBiayaLainnya;

    // For konstruksi, split by asosiasi
    if (sbuSlug === 'konstruksi') {
        // In simplified PHP version, send as generic data
        // The backend getMappings handles the split for known types
    }

    const csrf = document.querySelector('input[name="_csrf"]')?.value || '';

    fetch(baseUrl('/certificates/reference-data'), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
        body: JSON.stringify(body),
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            showSbuToast('Semua data berhasil disimpan!');
            setTimeout(() => location.reload(), 800);
        } else {
            showSbuToast('Gagal: ' + (res.message || 'Unknown error'));
        }
    })
    .catch(err => { console.error(err); showSbuToast('Gagal menyimpan data'); })
    .finally(() => { btn.disabled = false; btn.textContent = 'Simpan Semua'; });
}
</script>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4" style="background:rgba(0,0,0,.5)">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center text-slate-800 dark:text-white relative">
        <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
            <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <h3 class="text-lg font-bold mb-2">Hapus Sertifikat?</h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">Aksi ini tidak dapat dibatalkan.</p>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 font-medium text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition">Batal</button>
            <button id="confirmDeleteBtn" onclick="confirmDelete()" class="flex-1 px-4 py-2.5 rounded-xl bg-red-600 text-white font-medium text-sm hover:bg-red-700 transition">Ya, Hapus</button>
        </div>
    </div>
</div>

<script>
let pendingDeleteId = null;

function deleteCert(id) {
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
    const url = baseUrl('/certificates/' + pendingDeleteId + '/delete');

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: '_csrf=' + encodeURIComponent(csrfToken),
        redirect: 'follow'
    })
    .then(() => window.location.href = baseUrl('/certificates'))
    .catch(err => {
        console.error('Delete error:', err);
        window.location.href = baseUrl('/certificates');
    });
}
</script>
