import { configureStore } from '@reduxjs/toolkit';
import authReducer from '@/features/auth/authSlice';
import usersReducer from '@/features/users/usersSlice';
import certificatesReducer from '@/features/certificates/certificatesSlice';
import submissionsReducer from '@/features/submissions/submissionsSlice';
import transactionsReducer from '@/features/transactions/transactionsSlice';
import dashboardReducer from '@/features/dashboard/dashboardSlice';
import marketingReducer from '@/features/marketing/marketingSlice';
import feeP3smReducer from '@/features/feeP3sm/feeP3smSlice';

export const store = configureStore({
    reducer: {
        auth: authReducer,
        users: usersReducer,
        certificates: certificatesReducer,
        submissions: submissionsReducer,
        transactions: transactionsReducer,
        dashboard: dashboardReducer,
        marketing: marketingReducer,
        feeP3sm: feeP3smReducer,
    },
});

export type RootState = ReturnType<typeof store.getState>;
export type AppDispatch = typeof store.dispatch;
