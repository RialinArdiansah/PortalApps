import { getRoleBadgeClass } from '@/utils/formatters';

export const RoleBadge = ({ role }: { role: string }) => (
    <span className={`px-3 py-1 rounded-full text-xs font-semibold ${getRoleBadgeClass(role)}`}>
        {role}
    </span>
);
