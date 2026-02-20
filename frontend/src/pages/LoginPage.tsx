import { useState, type FormEvent } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAppDispatch, useAppSelector } from '@/app/hooks';
import { login, clearError } from '@/features/auth/authSlice';
import { useTheme } from '@/context/ThemeContext';

export const LoginPage = () => {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const dispatch = useAppDispatch();
    const navigate = useNavigate();
    const { status, error } = useAppSelector((state) => state.auth);
    const { isDark, toggleTheme } = useTheme();

    const handleSubmit = async (e: FormEvent) => {
        e.preventDefault();
        dispatch(clearError());
        const result = await dispatch(login({ username, password }));
        if (login.fulfilled.match(result)) {
            navigate('/');
        }
    };

    return (
        <div className="min-h-screen bg-gradient-to-br from-primary-800 to-primary-950 dark:from-slate-900 dark:to-slate-950 flex items-center justify-center p-4 relative">
            {/* Decorative blobs */}
            <div className="absolute top-1/4 left-1/4 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none" />
            <div className="absolute bottom-1/4 right-1/4 w-72 h-72 bg-purple-600/15 rounded-full blur-3xl pointer-events-none" />

            <div className="bg-white dark:bg-slate-800/80 dark:backdrop-blur-xl dark:ring-1 dark:ring-white/10 p-8 rounded-3xl shadow-2xl w-full max-w-md relative z-10 transition-colors duration-300">
                {/* Dark mode toggle */}
                <button
                    onClick={toggleTheme}
                    className="absolute top-4 right-4 p-2 rounded-lg text-gray-400 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
                    aria-label="Toggle dark mode"
                >
                    {isDark ? (
                        <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    ) : (
                        <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                            <path strokeLinecap="round" strokeLinejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    )}
                </button>

                <div className="flex justify-center mb-4">
                    <div className="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <span className="text-2xl">🏆</span>
                    </div>
                </div>
                <h1 className="text-4xl font-bold text-center text-primary-800 dark:text-white mb-2">Sulthan Group</h1>
                <p className="text-center text-gray-500 dark:text-slate-400 mb-8">Portal Login</p>

                {error && (
                    <div className="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl mb-4 text-sm">
                        {error}
                    </div>
                )}

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div>
                        <label htmlFor="username" className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Username</label>
                        <input
                            id="username"
                            type="text"
                            value={username}
                            onChange={(e) => setUsername(e.target.value)}
                            className="w-full px-5 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 dark:focus:ring-indigo-500 transition placeholder-gray-400 dark:placeholder-slate-500"
                            placeholder="Masukkan username Anda"
                            required
                        />
                    </div>
                    <div>
                        <label htmlFor="password" className="block text-gray-700 dark:text-slate-300 font-medium mb-1">Kata Sandi</label>
                        <input
                            id="password"
                            type="password"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            className="w-full px-5 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 dark:focus:ring-indigo-500 transition placeholder-gray-400 dark:placeholder-slate-500"
                            placeholder="Masukkan kata sandi Anda"
                            required
                        />
                    </div>
                    <button
                        type="submit"
                        disabled={status === 'loading'}
                        className="w-full bg-primary-600 dark:bg-indigo-600 text-white font-semibold py-3 rounded-xl hover:bg-primary-700 dark:hover:bg-indigo-700 transition duration-300 shadow-lg disabled:opacity-50"
                    >
                        {status === 'loading' ? 'Memproses...' : 'Masuk'}
                    </button>
                </form>

                <div className="mt-6 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-xl">
                    <p className="text-xs text-gray-500 dark:text-slate-400 font-medium mb-2">Demo Akun:</p>
                    <p className="text-xs text-gray-400 dark:text-slate-500">Super Admin: superadmin / superadmin123</p>
                    <p className="text-xs text-gray-400 dark:text-slate-500">Admin: admin / admin123</p>
                    <p className="text-xs text-gray-400 dark:text-slate-500">Marketing: marketing / marketing123</p>
                </div>
            </div>
        </div>
    );
};
