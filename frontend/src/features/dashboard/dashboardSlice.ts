import { createSlice, PayloadAction } from '@reduxjs/toolkit';
import type { DashboardFilter } from '@/types';

interface DashboardState {
    filter: DashboardFilter;
    activeTab: 'ringkasan' | 'analisisSertifikat' | 'laporanKeuangan' | 'pencapaianMarketing';
}

const initialState: DashboardState = {
    filter: {
        type: 'all',
        month: new Date().getMonth() + 1,
        year: new Date().getFullYear(),
    },
    activeTab: 'ringkasan',
};

const dashboardSlice = createSlice({
    name: 'dashboard',
    initialState,
    reducers: {
        setFilter(state, action: PayloadAction<Partial<DashboardFilter>>) {
            state.filter = { ...state.filter, ...action.payload };
        },
        setActiveTab(state, action: PayloadAction<DashboardState['activeTab']>) {
            state.activeTab = action.payload;
        },
    },
});

export const { setFilter, setActiveTab } = dashboardSlice.actions;
export default dashboardSlice.reducer;
