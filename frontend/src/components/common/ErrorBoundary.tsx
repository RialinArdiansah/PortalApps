import React from 'react';

interface ErrorBoundaryProps {
    children: React.ReactNode;
}
interface ErrorBoundaryState {
    hasError: boolean;
    error: Error | null;
}

export class ErrorBoundary extends React.Component<ErrorBoundaryProps, ErrorBoundaryState> {
    constructor(props: ErrorBoundaryProps) {
        super(props);
        this.state = { hasError: false, error: null };
    }

    static getDerivedStateFromError(error: Error) {
        return { hasError: true, error };
    }

    componentDidCatch(error: Error, info: React.ErrorInfo) {
        console.error('[ErrorBoundary]', error, info);
    }

    render() {
        if (this.state.hasError) {
            return (
                <div className="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-slate-900 p-4 transition-colors">
                    <div className="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-8 max-w-md w-full text-center border border-transparent dark:border-slate-700">
                        <div className="text-6xl mb-4">⚠️</div>
                        <h1 className="text-2xl font-bold text-gray-800 dark:text-white mb-2">Terjadi Kesalahan</h1>
                        <p className="text-gray-500 dark:text-slate-400 mb-6">{this.state.error?.message || 'Kesalahan tidak diketahui'}</p>
                        <button
                            onClick={() => window.location.reload()}
                            className="bg-primary-600 dark:bg-indigo-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-primary-700 dark:hover:bg-indigo-700 transition"
                        >
                            Muat Ulang
                        </button>
                    </div>
                </div>
            );
        }
        return this.props.children;
    }
}
