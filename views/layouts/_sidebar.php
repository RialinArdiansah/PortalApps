<?php
$menuItems = [
    ['label' => 'Dashboard', 'path' => '/', 'icon' => 'dashboard', 'exact' => true],
    ['label' => 'Manajemen Pengguna', 'path' => '/users', 'icon' => 'group', 'roles' => ['Super admin']],
    ['label' => 'Manajemen Sertifikat', 'path' => '/certificates', 'icon' => 'workspace_premium', 'roles' => ['Super admin']],
    ['label' => 'Manajemen Marketing', 'path' => '/marketing', 'icon' => 'campaign', 'roles' => ['Super admin', 'admin', 'manager']],
    ['label' => 'Data Sertifikat', 'icon' => 'folder_open', 'children' => [
        ['label' => 'Input Data Sertifikat', 'path' => '/submissions/new'],
        ['label' => 'Data Input Saya', 'path' => '/submissions'],
    ]],
    ['label' => 'Keuangan', 'icon' => 'account_balance_wallet', 'children' => [
        ['label' => 'Entri Transaksi', 'path' => '/transactions'],
        ['label' => 'Fee P3SM', 'path' => '/fee-p3sm'],
    ]],
    ['label' => 'Pengaturan', 'path' => '/settings', 'icon' => 'settings', 'roles' => ['Super admin', 'admin']],
];

$userRole = Auth::role();
?>

<!-- Mobile overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/40 dark:bg-black/60 backdrop-blur-sm z-40 hidden lg:hidden transition-all duration-300" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed top-0 left-0 z-50 w-72 h-screen glass-sidebar transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col shrink-0">
    <!-- Logo -->
    <div class="px-8 py-6 flex items-center gap-4 shrink-0 mt-2">
        <div class="w-11 h-11 bg-gradient-to-br from-accent-indigo to-primary rounded-[14px] flex items-center justify-center text-white shadow-lg shadow-primary/30 shrink-0">
            <span class="material-symbols-outlined text-2xl font-light">account_balance</span>
        </div>
        <div>
            <h1 class="text-slate-800 dark:text-white text-[17px] font-bold tracking-tight leading-tight">Sulthan Group</h1>
            <p class="text-accent-indigo dark:text-slate-400 text-[10px] uppercase tracking-[0.2em] font-bold mt-0.5">Portal Hub</p>
        </div>
    </div>

    <!-- Menu -->
    <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1 hide-scrollbar-smooth">
        <?php foreach ($menuItems as $item):
            // Role check
            if (isset($item['roles']) && !in_array($userRole, $item['roles'])) continue;

            if (isset($item['children'])):
                // Check if any child is active
                $isGroupActive = false;
                foreach ($item['children'] as $child) {
                    if (is_active($child['path'])) { $isGroupActive = true; break; }
                }
        ?>
            <div>
                <button onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.chevron-icon').classList.toggle('rotate-180');" class="w-full flex items-center justify-between px-4 py-3.5 rounded-2xl text-slate-500 dark:text-slate-400 hover:text-primary dark:hover:text-white hover:bg-slate-100/50 dark:hover:bg-white/5 transition-all group cursor-pointer font-medium tracking-wide">
                    <span class="flex items-center gap-4">
                        <span class="material-symbols-outlined text-[22px] font-light transition-transform group-hover:scale-110"><?= $item['icon'] ?></span>
                        <span class="text-[13px]"><?= e($item['label']) ?></span>
                    </span>
                    <svg class="chevron-icon w-4 h-4 transition-transform duration-200 <?= $isGroupActive ? 'rotate-180' : '' ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ml-[42px] mt-1 mb-2 space-y-1 <?= $isGroupActive ? '' : 'hidden' ?> border-l border-slate-200 dark:border-white/10 pl-4 py-1">
                    <?php foreach ($item['children'] as $child):
                        $active = is_active($child['path']);
                    ?>
                        <a href="<?= url($child['path']) ?>" class="block px-3 py-2.5 rounded-xl text-[13px] <?= $active ? 'text-primary dark:text-white font-bold bg-slate-100/50 dark:bg-white/5' : 'text-slate-500 dark:text-slate-400 hover:text-primary dark:hover:text-white hover:bg-slate-100/50 dark:hover:bg-white/5' ?> transition-colors tracking-wide">
                            <?= e($child['label']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else:
            $active = isset($item['exact']) ? (is_active('/') && parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === url('/')) : is_active($item['path']);
        ?>
            <a href="<?= url($item['path']) ?>" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl text-[13px] font-medium transition-all group <?= $active ? 'nav-item-active' : 'text-slate-500 dark:text-slate-400 hover:text-primary dark:hover:text-white hover:bg-slate-100/50 dark:hover:bg-white/5' ?> tracking-wide cursor-pointer w-full">
                <span class="material-symbols-outlined text-[22px] font-light transition-transform group-hover:scale-110 <?= $active ? 'text-primary dark:text-accent-indigo drop-shadow-[0_0_8px_rgba(99,102,241,0.5)]' : '' ?>"><?= $item['icon'] ?></span>
                <span><?= e($item['label']) ?></span>
            </a>
        <?php endif; endforeach; ?>
    </nav>

    <!-- Logout & Profile Settings (bottom) -->
    <div class="px-6 py-6 border-t border-slate-200/60 dark:border-white/5 bg-slate-50/30 dark:bg-white/[0.01]">
        <div class="relative p-5 rounded-2xl bg-gradient-to-br from-slate-100 to-white dark:from-white/5 dark:to-white/[0.02] border border-slate-200 dark:border-white/10 overflow-hidden group">
            <div class="relative z-10 flex flex-col gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary border border-primary/20 shrink-0">
                        <span class="material-symbols-outlined text-sm font-bold">verified_user</span>
                    </div>
                    <div>
                        <p class="text-slate-800 dark:text-white text-xs font-bold leading-tight"><?= e(Auth::user()['full_name'] ?? 'User') ?></p>
                        <p class="text-slate-500 text-[10px] mt-0.5"><?= e(Auth::role()) ?></p>
                    </div>
                </div>
                <form action="<?= BASE_URL ?>/logout" method="POST" class="w-full mt-1">
                    <?= csrf_field() ?>
                    <button type="submit" class="bg-white dark:bg-background-dark text-rose-500 border border-slate-200 dark:border-white/10 text-[11px] font-bold py-2.5 px-4 rounded-xl w-full hover:bg-rose-50 dark:hover:bg-rose-500/10 hover:border-rose-200 dark:hover:border-rose-500/30 hover:text-rose-600 dark:hover:text-rose-400 transition-all uppercase tracking-wider flex items-center justify-center gap-2 shadow-sm">
                        <span class="material-symbols-outlined text-[14px]">logout</span>
                        Sign Out
                    </button>
                </form>
            </div>
            <!-- Ambient glow behind the card content in dark mode -->
            <div class="hidden dark:block absolute -right-6 -bottom-6 bg-accent-indigo/10 w-24 h-24 rounded-full blur-2xl transition-transform group-hover:scale-150 pointer-events-none"></div>
        </div>
    </div>
</aside>
