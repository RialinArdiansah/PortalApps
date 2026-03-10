// ═══════════════════════════════════════════════════════════════════
// Portal Sertifikasi — Global JavaScript
// ═══════════════════════════════════════════════════════════════════

// Base URL helper (injected by layout)
function baseUrl(path) {
    return (window.BASE_URL || '') + path;
}

// Dark mode toggle
function toggleDarkMode() {
    document.documentElement.classList.toggle('dark');
    const isDark = document.documentElement.classList.contains('dark');
    document.cookie = 'darkMode=' + (isDark ? '1' : '0') + ';path=/;max-age=31536000';
}

// Mobile sidebar
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}

// Modal helpers
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.body.style.overflow = '';
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.fixed.z-50:not(.hidden)').forEach(modal => {
            modal.classList.add('hidden');
        });
        document.body.style.overflow = '';
    }
});

// Format Rupiah
function formatRupiah(n) {
    return 'Rp ' + Number(n).toLocaleString('id-ID');
}
