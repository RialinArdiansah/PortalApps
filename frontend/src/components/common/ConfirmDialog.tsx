import { Modal } from './Modal';

interface ConfirmDialogProps {
    isOpen: boolean;
    onClose: () => void;
    onConfirm: () => void;
    message?: string;
    title?: string;
}

export const ConfirmDialog = ({
    isOpen,
    onClose,
    onConfirm,
    message = 'Apakah Anda yakin ingin melanjutkan?',
    title = 'Konfirmasi',
}: ConfirmDialogProps) => {
    return (
        <Modal isOpen={isOpen} onClose={onClose} title={title} maxWidth="max-w-sm">
            <p className="text-gray-600 dark:text-slate-400 mb-6 text-center">{message}</p>
            <div className="flex justify-center gap-4">
                <button
                    onClick={onClose}
                    className="px-6 py-3 rounded-xl font-semibold bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200 hover:bg-gray-300 dark:hover:bg-slate-500 transition"
                >
                    Batal
                </button>
                <button
                    onClick={() => { onConfirm(); onClose(); }}
                    className="px-6 py-3 rounded-xl font-semibold bg-red-600 text-white hover:bg-red-700 transition"
                >
                    Yakin
                </button>
            </div>
        </Modal>
    );
};
