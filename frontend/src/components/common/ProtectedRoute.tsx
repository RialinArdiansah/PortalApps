import { Navigate } from 'react-router-dom';
import { useAppSelector } from '@/app/hooks';
import type { UserRole } from '@/types';

interface ProtectedRouteProps {
    children: React.ReactNode;
    allowedRoles?: UserRole[];
}

export const ProtectedRoute = ({ children, allowedRoles }: ProtectedRouteProps) => {
    const { user, token } = useAppSelector((state) => state.auth);

    if (!user || !token) {
        return <Navigate to="/login" replace />;
    }

    if (allowedRoles && !allowedRoles.includes(user.role)) {
        return <Navigate to="/" replace />;
    }

    return <>{children}</>;
};
