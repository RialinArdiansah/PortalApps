// =====================================================
// Formatting Utilities
// =====================================================

/**
 * Format a number as Indonesian Rupiah currency
 * Example: 1000000 → "Rp 1.000.000"
 */
export const formatCurrency = (number: number): string => {
    if (typeof number !== 'number' || isNaN(number)) return 'Rp 0';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(number);
};

/**
 * Format ISO date string (YYYY-MM-DD) to DD-MM-YYYY
 */
export const formatDate = (dateString: string): string => {
    if (!dateString || !/^\d{4}-\d{2}-\d{2}$/.test(dateString)) return '-';
    const [year, month, day] = dateString.split('-');
    return `${day}-${month}-${year}`;
};

/**
 * Capitalize words with PT./CV. prefix handling
 * "pt. maju bersama" → "PT. Maju Bersama"
 */
export const capitalizeWords = (str: string): string => {
    if (!str) return '';
    const prefixes = ['PT. ', 'CV. '];
    let prefix = '';
    let namePart = str;

    for (const p of prefixes) {
        if (str.toUpperCase().startsWith(p.toUpperCase())) {
            prefix = str.substring(0, p.length);
            namePart = str.substring(p.length);
            break;
        }
    }

    const capitalizedNamePart = namePart
        .toLowerCase()
        .split(' ')
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');

    return prefix + capitalizedNamePart;
};

/**
 * Generate a unique ID
 */
export const generateId = (): string =>
    `id-${Date.now()}-${Math.floor(Math.random() * 1000)}`;

/**
 * Get role badge CSS classes for Tailwind
 */
export const getRoleBadgeClass = (role: string): string => {
    switch (role) {
        case 'Super admin': return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        case 'admin': return 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400';
        case 'manager': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
        case 'karyawan': return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        case 'marketing': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
        case 'mitra': return 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400';
        default: return 'bg-gray-100 text-gray-800 dark:bg-slate-700 dark:text-slate-300';
    }
};

/**
 * Get time-based greeting in Indonesian
 */
export const getGreeting = (): { text: string; icon: string } => {
    const hour = new Date().getHours();
    if (hour >= 6 && hour < 12) return { text: 'Selamat Pagi', icon: '🌅' };
    if (hour >= 12 && hour < 15) return { text: 'Selamat Siang', icon: '☀️' };
    if (hour >= 15 && hour < 18) return { text: 'Selamat Sore', icon: '🌇' };
    return { text: 'Selamat Malam', icon: '🌙' };
};

/**
 * Month names in Indonesian
 */
export const MONTH_NAMES = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
] as const;
