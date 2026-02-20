import { ITEMS_PER_PAGE_OPTIONS, MAX_VISIBLE_PAGES } from '@/types';

interface PaginationProps {
    currentPage: number;
    totalItems: number;
    itemsPerPage: number;
    onPageChange: (page: number) => void;
    onItemsPerPageChange: (items: number) => void;
}

export const Pagination = ({
    currentPage, totalItems, itemsPerPage, onPageChange, onItemsPerPageChange,
}: PaginationProps) => {
    const totalPages = Math.max(1, Math.ceil(totalItems / itemsPerPage));

    const getVisiblePages = (): number[] => {
        const pages: number[] = [];
        let start = Math.max(1, currentPage - Math.floor(MAX_VISIBLE_PAGES / 2));
        const end = Math.min(totalPages, start + MAX_VISIBLE_PAGES - 1);
        start = Math.max(1, end - MAX_VISIBLE_PAGES + 1);
        for (let i = start; i <= end; i++) pages.push(i);
        return pages;
    };

    return (
        <div className="flex flex-col sm:flex-row items-center justify-between gap-4 mt-4">
            <div className="flex items-center gap-2 text-sm text-gray-600 dark:text-slate-400">
                <span>Tampilkan</span>
                <select
                    value={itemsPerPage}
                    onChange={(e) => onItemsPerPageChange(Number(e.target.value))}
                    className="rounded-lg border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 shadow-sm text-sm px-2 py-1 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"
                >
                    {ITEMS_PER_PAGE_OPTIONS.map((opt) => (
                        <option key={opt} value={opt}>{opt}</option>
                    ))}
                </select>
                <span>dari {totalItems} data</span>
            </div>
            <div className="flex items-center gap-1">
                <button
                    onClick={() => onPageChange(currentPage - 1)}
                    disabled={currentPage <= 1}
                    className="px-3 py-2 rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition"
                >
                    ← Prev
                </button>
                {getVisiblePages().map((page) => (
                    <button
                        key={page}
                        onClick={() => onPageChange(page)}
                        className={`px-3 py-2 rounded-lg text-sm font-medium transition ${page === currentPage
                            ? 'bg-primary-600 dark:bg-indigo-600 text-white'
                            : 'hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-300'
                            }`}
                    >
                        {page}
                    </button>
                ))}
                <button
                    onClick={() => onPageChange(currentPage + 1)}
                    disabled={currentPage >= totalPages}
                    className="px-3 py-2 rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition"
                >
                    Next →
                </button>
            </div>
        </div>
    );
};
