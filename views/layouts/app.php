<!DOCTYPE html>
<html lang="id" class="<?= ($_COOKIE['darkMode'] ?? '') === '1' ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> — Sulthan Group</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        darkMode: 'class',
        theme: { extend: {
            colors: {
                primary: { 50:'#eef2ff', 100:'#e0e7ff', 200:'#c7d2fe', 300:'#a5b4fc', 400:'#818cf8', 500:'#6366f1', 600:'#4f46e5', 700:'#4338ca', 800:'#3730a3', 900:'#312e81', 950:'#1e1b4b' },
                "background-light": "#f8fafc",
                "background-dark": "#0a0815",
                "glass-dark": "rgba(10, 9, 20, 0.7)",
                "accent-indigo": "#6366f1",
            }
        }}
    }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Glassmorphism Styles */
        .glass-sidebar {
            background: linear-gradient(180deg, rgba(248, 250, 255, 0.85) 0%, rgba(241, 243, 250, 0.9) 100%);
            border-right: 1px solid rgba(99, 102, 241, 0.08);
        }
        .dark .glass-sidebar {
            background: linear-gradient(180deg, rgba(15, 14, 35, 0.35) 0%, rgba(10, 8, 25, 0.4) 100%);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-right: 1px solid rgba(139, 92, 246, 0.1);
        }

        .glass-header {
            background: rgba(248, 250, 255, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(99, 102, 241, 0.06);
        }
        .dark .glass-header {
            background: rgba(10, 8, 21, 0.3);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(139, 92, 246, 0.08);
        }

        .nav-item-active {
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.1) 0%, rgba(99, 102, 241, 0.02) 100%);
            box-shadow: inset 3px 0 0 0 #6366f1;
            color: #4f46e5;
        }
        .dark .nav-item-active {
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.15) 0%, rgba(99, 102, 241, 0.02) 100%);
            box-shadow: inset 2px 0 0 0 #818cf8;
            color: white;
        }
        
        .profile-dropdown-glass {
            background: rgba(248, 250, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(99, 102, 241, 0.1);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .dark .profile-dropdown-glass {
            background: rgba(15, 14, 35, 0.75);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(139, 92, 246, 0.15);
            box-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.5);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0, 0, 0, 0.1); border-radius: 10px; }
        .dark ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); }
        
        [x-cloak] { display: none !important; }
    </style>
    <?php if (!empty($useCharts)): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <?php endif; ?>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 min-h-screen flex overflow-hidden">
    <!-- Mesh Gradient Background (Dark Mode) -->
    <div class="mesh-gradient-bg"></div>
    <!-- Floating Gradient Orbs -->
    <div class="gradient-orb gradient-orb-1"></div>
    <div class="gradient-orb gradient-orb-2"></div>
    <div class="gradient-orb gradient-orb-3"></div>

    <div class="flex flex-1 min-h-screen relative">

        <!-- Sidebar -->
        <?php require BASE_PATH . '/views/layouts/_sidebar.php'; ?>

        <!-- Main content -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden relative lg:ml-72">
            <!-- Top bar -->
            <header class="h-20 glass-header px-4 lg:px-10 flex items-center justify-between shrink-0 z-40">
                <div class="flex items-center gap-4 flex-1">
                    <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-xl border border-slate-200 dark:border-white/10 text-slate-500 hover:text-accent-indigo hover:border-accent-indigo/30 transition-all bg-white/50 dark:bg-white/5 backdrop-blur-md">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <!-- Search Bar (Optional visual element for premium feel) -->
                    <div class="relative max-w-md w-full hidden sm:block">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-lg font-light">search</span>
                        <input class="w-full bg-slate-100/50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl pl-11 pr-4 py-2.5 text-sm text-slate-700 dark:text-slate-200 focus:ring-1 focus:ring-accent-indigo/50 focus:border-accent-indigo/50 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500 outline-none" placeholder="Cari data, menu, atau pengguna..." type="text"/>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-5 ml-auto">
                    <!-- Dark Mode Toggle -->
                    <button onclick="toggleDarkMode()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100/50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-500 dark:text-slate-400 hover:text-accent-indigo dark:hover:text-accent-indigo hover:border-accent-indigo/30 transition-all group" title="Toggle dark mode">
                        <span class="material-symbols-outlined text-xl font-light dark:hidden transition-transform group-hover:scale-110">light_mode</span>
                        <span class="material-symbols-outlined text-xl font-light hidden dark:block transition-transform group-hover:scale-110">dark_mode</span>
                    </button>

                    <!-- Notifications (static premium element) -->
                    <button class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100/50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-500 dark:text-slate-400 hover:text-accent-indigo hover:border-accent-indigo/30 transition-all relative group">
                        <span class="material-symbols-outlined text-xl font-light transition-transform group-hover:scale-110">notifications</span>
                        <span class="absolute top-3 right-3 w-1.5 h-1.5 bg-rose-500 rounded-full shadow-[0_0_8px_rgba(244,63,94,0.6)]"></span>
                    </button>

                    <div class="h-8 w-px bg-slate-200 dark:bg-white/10 mx-1 sm:mx-2 hidden sm:block"></div>
                    
                    <!-- Profile Badge -->
                    <div class="relative group cursor-pointer" onclick="document.getElementById('profileDropdown').classList.toggle('opacity-0'); document.getElementById('profileDropdown').classList.toggle('invisible');">
                        <div class="flex items-center gap-3 pl-2 py-1.5 pr-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-semibold leading-none text-slate-800 dark:text-white"><?= e(Auth::user()['full_name'] ?? 'Guest') ?></p>
                                <p class="text-[10px] font-bold text-accent-indigo mt-1 uppercase tracking-tighter"><?= e(Auth::role()) ?></p>
                            </div>
                            <div class="w-10 h-10 rounded-xl overflow-hidden border border-slate-200 dark:border-white/20 p-0.5 relative bg-gradient-to-br from-indigo-50 to-primary/10 dark:from-white/5 dark:to-white/10">
                                <div class="w-full h-full rounded-[10px] bg-primary flex items-center justify-center text-white font-bold text-sm shadow-inner">
                                    <?= strtoupper(substr(Auth::user()['full_name'] ?? 'G', 0, 1)) ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Premium Profile Dropdown -->
                        <div id="profileDropdown" class="absolute top-full right-0 mt-3 w-56 profile-dropdown-glass rounded-2xl p-2 opacity-0 invisible transition-all duration-300 transform translate-y-2 lg:group-hover:opacity-100 lg:group-hover:visible lg:group-hover:translate-y-0 z-50">
                            <div class="px-4 py-3 border-b border-slate-100 dark:border-white/5">
                                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Akun Saya</p>
                            </div>
                            <div class="py-1 border-b border-slate-100 dark:border-white/5">
                                <a href="<?= BASE_URL ?>/profile" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-slate-900 dark:hover:text-white rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-lg font-light">person</span>
                                    <span>Profil Anda</span>
                                </a>
                                <a href="<?= BASE_URL ?>/settings" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-slate-900 dark:hover:text-white rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-lg font-light">settings</span>
                                    <span>Pengaturan</span>
                                </a>
                            </div>
                            <div class="mt-1 pt-1">
                                <form action="<?= BASE_URL ?>/logout" method="POST" class="w-full">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-lg font-light">logout</span>
                                        <span>Keluar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash messages -->
            <?php $flash = get_flash(); if ($flash): ?>
            <div class="mx-4 lg:mx-6 mt-4">
                <div class="<?= $flash['type'] === 'success' ? 'bg-emerald-50 dark:bg-emerald-900/30 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300' : 'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800 text-red-700 dark:text-red-300' ?> border px-4 py-3 rounded-xl text-sm" id="flash-msg">
                    <?= e($flash['message']) ?>
                </div>
            </div>
            <script>setTimeout(() => { const el = document.getElementById('flash-msg'); if (el) el.style.display = 'none'; }, 4000);</script>
            <?php endif; ?>

            <!-- Page content -->
            <div class="flex-1 overflow-y-auto p-4 lg:p-10 hide-scrollbar-smooth">
                <?php require $viewContent; ?>
            </div>
        </main>
    </div>

    <!-- For overflow-y-auto smooth custom scroll -->
    <style>
        .hide-scrollbar-smooth::-webkit-scrollbar { width: 6px; }
        .hide-scrollbar-smooth::-webkit-scrollbar-track { background: transparent; }
        .hide-scrollbar-smooth::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.2); border-radius: 10px; }
        .dark .hide-scrollbar-smooth::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); }
        .hide-scrollbar-smooth::-webkit-scrollbar-thumb:hover { background: rgba(148, 163, 184, 0.4); }
        .dark .hide-scrollbar-smooth::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.2); }
    </style>

    <script>window.BASE_URL = '<?= BASE_URL ?>';</script>
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
