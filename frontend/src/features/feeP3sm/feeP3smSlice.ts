import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import type { FeeP3SM, AuthState } from '@/types';

interface FeeP3SMState {
    list: FeeP3SM[];
    status: 'idle' | 'loading' | 'succeeded' | 'failed';
    error: string | null;
}

const initialState: FeeP3SMState = {
    list: [],
    status: 'idle',
    error: null,
};

export const fetchFeeP3SM = createAsyncThunk('feeP3sm/fetch', async (_, { getState }) => {
    const state = getState() as { auth: AuthState };
    const res = await fetch('/api/fee-p3sm', {
        headers: { Authorization: `Bearer ${state.auth.token}` },
    });
    const data = await res.json();
    return data.data as FeeP3SM[];
});

export const createFeeP3SM = createAsyncThunk('feeP3sm/create', async (fee: Omit<FeeP3SM, 'id'>, { getState }) => {
    const state = getState() as { auth: AuthState };
    const res = await fetch('/api/fee-p3sm', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${state.auth.token}` },
        body: JSON.stringify(fee),
    });
    const data = await res.json();
    return data.data as FeeP3SM;
});

export const updateFeeP3SM = createAsyncThunk('feeP3sm/update', async ({ id, ...fee }: FeeP3SM, { getState }) => {
    const state = getState() as { auth: AuthState };
    const res = await fetch(`/api/fee-p3sm/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${state.auth.token}` },
        body: JSON.stringify(fee),
    });
    const data = await res.json();
    return data.data as FeeP3SM;
});

export const deleteFeeP3SM = createAsyncThunk('feeP3sm/delete', async (id: string, { getState }) => {
    const state = getState() as { auth: AuthState };
    await fetch(`/api/fee-p3sm/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${state.auth.token}` },
    });
    return id;
});

const feeP3smSlice = createSlice({
    name: 'feeP3sm',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(fetchFeeP3SM.pending, (state) => { state.status = 'loading'; })
            .addCase(fetchFeeP3SM.fulfilled, (state, action) => {
                state.status = 'succeeded';
                state.list = action.payload;
            })
            .addCase(fetchFeeP3SM.rejected, (state, action) => {
                state.status = 'failed';
                state.error = action.error.message || null;
            })
            .addCase(createFeeP3SM.fulfilled, (state, action) => {
                state.list.push(action.payload);
            })
            .addCase(updateFeeP3SM.fulfilled, (state, action) => {
                const idx = state.list.findIndex((f) => f.id === action.payload.id);
                if (idx !== -1) state.list[idx] = action.payload;
            })
            .addCase(deleteFeeP3SM.fulfilled, (state, action) => {
                state.list = state.list.filter((f) => f.id !== action.payload);
            });
    },
});

export default feeP3smSlice.reducer;
