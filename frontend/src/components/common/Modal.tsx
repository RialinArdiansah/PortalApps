import { useEffect, useRef } from 'react';

interface ModalProps {
    isOpen: boolean;
    onClose: () => void;
    title: string;
    children: React.ReactNode;
    maxWidth?: string;
}

export const Modal = ({ isOpen, onClose, title, children, maxWidth = 'max-w-lg' }: ModalProps) => {
    const overlayRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        const handleEsc = (e: KeyboardEvent) => {
            if (e.key === 'Escape') onClose();
        };
        if (isOpen) document.addEventListener('keydown', handleEsc);
        return () => document.removeEventListener('keydown', handleEsc);
    }, [isOpen, onClose]);

    if (!isOpen) return null;

    return (
        <div
            ref={overlayRef}
            className="fixed inset-0 bg-gray-900/70 dark:bg-black/80 backdrop-blur-sm overflow-y-auto h-full w-full flex items-start justify-center py-8 px-4 z-40"
            onClick={(e) => { if (e.target === overlayRef.current) onClose(); }}
        >
            <div className={`bg-white dark:bg-slate-800 p-6 sm:p-8 rounded-3xl shadow-2xl w-full ${maxWidth} relative animate-in border border-transparent dark:border-slate-700 transition-colors`}>
                <h2 className="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white mb-6">{title}</h2>
                <button
                    onClick={onClose}
                    className="absolute top-4 right-4 text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-300 transition"
                    aria-label="Tutup"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                {children}
            </div>
        </div>
    );
};
