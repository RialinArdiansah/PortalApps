import { useState, useEffect } from 'react';
import { NavLink, useNavigate, useLocation } from 'react-router-dom';
import { useAppSelector, useAppDispatch } from '@/app/hooks';
import { logout } from '@/features/auth/authSlice';
import { useTheme } from '@/context/ThemeContext';
import type { UserRole } from '@/types';

interface SubMenuItem {
    label: string;
    path: string;
    end?: boolean;
}

interface MenuItem {
    label: string;
    path?: string;
    icon: string;
    roles?: UserRole[];
    children?: SubMenuItem[];
    end?: boolean;
}

const menuItems: MenuItem[] = [
    { label: 'Dashboard', path: '/', icon: '📊', end: true },
    { label: 'Manajemen Pengguna', path: '/users', icon: '👥', roles: ['Super admin'] },
    { label: 'Manajemen Sertifikat', path: '/certificates', icon: '📜', roles: ['Super admin'] },
    { label: 'Manajemen Marketing', path: '/marketing', icon: '📢', roles: ['Super admin', 'admin', 'manager'] },
    {
        label: 'Data Sertifikat', icon: '📑',
        children: [
            { label: 'Input Data Sertifikat', path: '/submissions/new' },
            { label: 'Data Input Saya', path: '/submissions', end: true },
        ],
    },
    {
        label: 'Keuangan', icon: '💰',
        children: [
            { label: 'Entri Transaksi', path: '/finance/transactions' },
            { label: 'Fee P3SM', path: '/finance/fee-p3sm' },
        ],
    },
    { label: 'Pengaturan', path: '/settings', icon: '⚙️', roles: ['Super admin', 'admin'] },
];

interface SidebarProps {
    isOpen: boolean;
    onClose: () => void;
}

export const Sidebar = ({ isOpen, onClose }: SidebarProps) => {
    const dispatch = useAppDispatch();
    const navigate = useNavigate();
    const location = useLocation();
    const { user } = useAppSelector((state) => state.auth);
    const { isDark, toggleTheme } = useTheme();

    const [openMenus, setOpenMenus] = useState<Set<string>>(() => {
        const set = new Set<string>();
        menuItems.forEach((item) => {
            if (item.children?.some((c) => {
                if (c.end) return location.pathname === c.path;
                return location.pathname.startsWith(c.path);
            })) {
                set.add(item.label);
            }
        });
        return set;
    });

    useEffect(() => {
        menuItems.forEach((item) => {
            if (item.children?.some((c) => {
                if (c.end) return location.pathname === c.path;
                return location.pathname.startsWith(c.path);
            })) {
                setOpenMenus((prev) => {
                    const next = new Set(prev);
                    next.add(item.label);
                    return next;
                });
            }
        });
    }, [location.pathname]);

    // Close sidebar on route change (mobile)
    useEffect(() => {
        onClose();
    }, [location.pathname]);

    if (!user) return null;

    const filteredMenu = menuItems.filter(
        (item) => !item.roles || item.roles.includes(user.role)
    );

    const handleLogout = async () => {
        await dispatch(logout());
        navigate('/login');
    };

    const toggleMenu = (label: string) => {
        setOpenMenus((prev) => {
            const next = new Set(prev);
            if (next.has(label)) next.delete(label);
            else next.add(label);
            return next;
        });
    };

    const isChildActive = (children: SubMenuItem[]) => {
        return children.some((c) => {
            if (c.end) return location.pathname === c.path;
            return location.pathname.startsWith(c.path);
        });
    };

    const sidebarContent = (
        <nav className="w-72 bg-gradient-to-b from-slate-900 to-slate-950 text-white flex flex-col shadow-2xl relative transition-all duration-300 font-sans" style={{ height: '100vh' }}>
            {/* Decorative blobs — clipped in their own container */}
            <div className="absolute inset-0 overflow-hidden pointer-events-none">
                <div className="absolute top-0 left-0 w-64 h-64 bg-indigo-600/10 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2" />
                <div className="absolute bottom-0 right-0 w-64 h-64 bg-purple-600/10 rounded-full blur-3xl translate-x-1/2 translate-y-1/2" />
            </div>

            {/* Header */}
            <div className="p-6 pb-2 relative z-10" style={{ flexShrink: 0 }}>
                <div className="flex items-center justify-between mb-6">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/30 ring-1 ring-white/10">
                            <span className="text-xl">🏆</span>
                        </div>
                        <div>
                            <h1 className="text-lg font-bold text-white leading-tight tracking-tight">Sulthan Group</h1>
                            <p className="text-slate-400 text-xs font-medium uppercase tracking-wider mt-0.5">Portal Sertifikasi</p>
                        </div>
                    </div>
                    {/* Close button - mobile only */}
                    <button
                        onClick={onClose}
                        className="md:hidden p-1.5 rounded-lg hover:bg-white/10 text-slate-400 hover:text-white transition-colors"
                        aria-label="Tutup menu"
                    >
                        <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                            <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {/* Menu Items */}
            <div className="px-3 space-y-1 overflow-y-auto relative z-10 pb-4" style={{ flex: '1 1 0%', minHeight: 0 }}>
                {filteredMenu.map((item) => {
                    if (item.children) {
                        const isOpen = openMenus.has(item.label);
                        const hasActive = isChildActive(item.children);

                        return (
                            <div key={item.label} className="space-y-1 mb-2">
                                <button
                                    type="button"
                                    onClick={() => toggleMenu(item.label)}
                                    className={`w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 group ${hasActive
                                        ? 'bg-white/5 text-white shadow-inner backdrop-blur-sm ring-1 ring-white/5'
                                        : 'text-slate-300 hover:bg-white/5 hover:text-white'
                                        }`}
                                >
                                    <div className="flex items-center gap-3">
                                        <span className={`text-lg transition-transform duration-300 ${hasActive ? 'scale-110 drop-shadow-md' : 'group-hover:scale-110 opacity-70 group-hover:opacity-100'}`}>{item.icon}</span>
                                        <span className="tracking-wide">{item.label}</span>
                                    </div>
                                    <svg
                                        className={`w-4 h-4 transition-transform duration-300 text-slate-500 ${isOpen ? 'rotate-90 text-indigo-400' : ''}`}
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}
                                    >
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>

                                <div
                                    className={`pl-4 space-y-1 overflow-hidden transition-all duration-300 ease-in-out ${isOpen ? 'max-h-96 opacity-100 mt-1' : 'max-h-0 opacity-0'}`}
                                >
                                    {item.children.map((child) => (
                                        <NavLink
                                            key={child.path}
                                            to={child.path}
                                            end={child.end}
                                            className={({ isActive }) =>
                                                `flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm transition-all duration-200 ml-5 border-l-[2px] ${isActive
                                                    ? 'border-indigo-500 bg-indigo-500/10 text-white font-medium pl-3.5 shadow-sm'
                                                    : 'border-slate-800 text-slate-400 hover:text-white hover:bg-white/5 hover:border-slate-600 pl-3.5'
                                                }`
                                            }
                                        >
                                            {child.label}
                                        </NavLink>
                                    ))}
                                </div>
                            </div>
                        );
                    }

                    return (
                        <NavLink
                            key={item.path}
                            to={item.path!}
                            end={item.end}
                            className={({ isActive }) =>
                                `flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 group mb-1 ${isActive
                                    ? 'bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-lg shadow-indigo-500/30 translate-x-1 ring-1 ring-indigo-400/20'
                                    : 'text-slate-300 hover:bg-white/5 hover:text-white hover:translate-x-1'
                                }`
                            }
                        >
                            {({ isActive }) => (
                                <>
                                    <span className={`text-lg transition-transform duration-300 ${isActive ? 'scale-110 drop-shadow-md' : 'group-hover:scale-110 opacity-70 group-hover:opacity-100'}`}>{item.icon}</span>
                                    <span className="tracking-wide">{item.label}</span>
                                </>
                            )}
                        </NavLink>
                    );
                })}
            </div>

            {/* Dark mode toggle + User Profile */}
            <div className="p-4 relative z-10 border-t border-slate-800/50 bg-slate-900/40 backdrop-blur-md" style={{ flexShrink: 0 }}>
                {/* Dark Mode Toggle */}
                <button
                    onClick={toggleTheme}
                    className="w-full flex items-center justify-between px-3 py-2.5 rounded-xl mb-3 text-sm font-medium text-slate-300 hover:bg-white/5 hover:text-white transition-all duration-300"
                >
                    <div className="flex items-center gap-3">
                        <span className="text-lg">{isDark ? '🌙' : '☀️'}</span>
                        <span>{isDark ? 'Mode Gelap' : 'Mode Terang'}</span>
                    </div>
                    <div className={`w-11 h-6 rounded-full p-0.5 transition-colors duration-300 ${isDark ? 'bg-indigo-600' : 'bg-slate-600'}`}>
                        <div className={`w-5 h-5 bg-white rounded-full shadow-md transition-transform duration-300 ${isDark ? 'translate-x-5' : 'translate-x-0'}`} />
                    </div>
                </button>

                {/* User info */}
                <div className="flex items-center gap-3 mb-3 px-2">
                    <div className="w-10 h-10 rounded-full bg-gradient-to-tr from-emerald-400 to-cyan-500 flex items-center justify-center text-white font-bold shadow-md ring-2 ring-slate-800 text-sm">
                        {user.fullName.charAt(0).toUpperCase()}
                    </div>
                    <div className="flex-1 min-w-0">
                        <p className="text-sm font-semibold text-white truncate">{user.fullName}</p>
                        <p className="text-xs text-slate-400 truncate capitalize">{user.role}</p>
                    </div>
                </div>
                <button
                    onClick={handleLogout}
                    className="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-rose-500/20 text-rose-400 text-sm font-medium hover:bg-rose-500/10 hover:border-rose-500/40 transition-all duration-300 group"
                >
                    <svg className="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                        <path strokeLinecap="round" strokeLinejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Keluar Aplikasi
                </button>
            </div>
        </nav>
    );

    return (
        <>
            {/* Desktop sidebar - always visible */}
            <div className="hidden md:block flex-shrink-0 sticky top-0 h-screen">
                {sidebarContent}
            </div>

            {/* Mobile sidebar - overlay drawer */}
            <div
                className={`fixed inset-0 z-40 md:hidden transition-opacity duration-300 ${isOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'
                    }`}
            >
                {/* Backdrop */}
                <div
                    className="absolute inset-0 bg-black/60 backdrop-blur-sm"
                    onClick={onClose}
                />
                {/* Drawer */}
                <div
                    className={`absolute top-0 left-0 h-full transition-transform duration-300 ease-in-out ${isOpen ? 'translate-x-0' : '-translate-x-full'
                        }`}
                >
                    {sidebarContent}
                </div>
            </div>
        </>
    );
};
