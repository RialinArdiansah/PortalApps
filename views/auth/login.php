<!DOCTYPE html>
<html lang="id" class="<?= ($_COOKIE['darkMode'] ?? '') === '1' ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sulthan Group</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = { darkMode: 'class', theme: { extend: {
        colors: { primary: { 500:'#6366f1', 600:'#4f46e5', 700:'#4338ca', 800:'#3730a3', 950:'#1e1b4b' } }
    }}}
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body>
<div class="min-h-screen bg-gradient-to-br from-primary-800 to-primary-950 dark:from-slate-900 dark:to-slate-950 flex items-center justify-center p-4 relative">
    <!-- Decorative blobs -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-72 h-72 bg-purple-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="bg-white dark:bg-slate-800/80 dark:backdrop-blur-xl dark:ring-1 dark:ring-white/10 p-8 rounded-3xl shadow-2xl w-full max-w-md relative z-10 transition-colors duration-300">
        <!-- Dark mode toggle -->
        <button onclick="toggleDarkMode()" class="absolute top-4 right-4 p-2 rounded-lg text-gray-400 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
            <svg class="w-5 h-5 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            <svg class="w-5 h-5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </button>

        <div class="flex justify-center mb-4">
            <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                <span class="text-2xl">🏆</span>
            </div>
        </div>
        <h1 class="text-4xl font-bold text-center text-primary-800 dark:text-white mb-2">Sulthan Group</h1>
        <p class="text-center text-gray-500 dark:text-slate-400 mb-8">Portal Login</p>

        <?php if (!empty($error)): ?>
        <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl mb-4 text-sm">
            <?= e($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('/login') ?>" class="space-y-6">
            <?= csrf_field() ?>
            <div>
                <label for="username" class="block text-gray-700 dark:text-slate-300 font-medium mb-1">Username</label>
                <input id="username" name="username" type="text" required
                    class="w-full px-5 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 dark:focus:ring-indigo-500 transition placeholder-gray-400 dark:placeholder-slate-500"
                    placeholder="Masukkan username Anda">
            </div>
            <div>
                <label for="password" class="block text-gray-700 dark:text-slate-300 font-medium mb-1">Kata Sandi</label>
                <input id="password" name="password" type="password" required
                    class="w-full px-5 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 dark:focus:ring-indigo-500 transition placeholder-gray-400 dark:placeholder-slate-500"
                    placeholder="Masukkan kata sandi Anda">
            </div>
            <button type="submit"
                class="w-full bg-primary-600 dark:bg-indigo-600 text-white font-semibold py-3 rounded-xl hover:bg-primary-700 dark:hover:bg-indigo-700 transition duration-300 shadow-lg">
                Masuk
            </button>
        </form>

        <div class="mt-6 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-xl">
            <p class="text-xs text-gray-500 dark:text-slate-400 font-medium mb-2">Demo Akun:</p>
            <p class="text-xs text-gray-400 dark:text-slate-500">Super Admin: superadmin / superadmin123</p>
            <p class="text-xs text-gray-400 dark:text-slate-500">Admin: admin / admin123</p>
            <p class="text-xs text-gray-400 dark:text-slate-500">Marketing: marketing / marketing123</p>
        </div>
    </div>
</div>

<script>
function toggleDarkMode() {
    document.documentElement.classList.toggle('dark');
    document.cookie = 'darkMode=' + (document.documentElement.classList.contains('dark') ? '1' : '0') + ';path=/;max-age=31536000';
}
</script>
</body>
</html>
