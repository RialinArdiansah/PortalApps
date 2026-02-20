import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import type { MarketingName, AuthState } from '@/types';

interface MarketingState {
    list: MarketingName[];
    status: 'idle' | 'loading' | 'succeeded' | 'failed';
    error: string | null;
}

const initialState: MarketingState = {
    list: [],
    status: 'idle',
    error: null,
};

export const fetchMarketing = createAsyncThunk('marketing/fetch', async (_, { getState }) => {
    const state = getState() as { auth: AuthState };
    const res = await fetch('/api/marketing', {
        headers: { Authorization: `Bearer ${state.auth.token}` },
    });
    const data = await res.json();
    return data.data as MarketingName[];
});

export const createMarketing = createAsyncThunk('marketing/create', async (mkt: { name: string }, { getState }) => {
    const state = getState() as { auth: AuthState };
    const res = await fetch('/api/marketing', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${state.auth.token}` },
        body: JSON.stringify(mkt),
    });
    const data = await res.json();
    return data.data as MarketingName;
});

export const updateMarketing = createAsyncThunk('marketing/update', async ({ id, name }: { id: string; name: string }, { getState }) => {
    const state = getState() as { auth: AuthState };
    const res = await fetch(`/api/marketing/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${state.auth.token}` },
        body: JSON.stringify({ name }),
    });
    const data = await res.json();
    return data.data as MarketingName;
});

export const deleteMarketing = createAsyncThunk('marketing/delete', async (id: string, { getState }) => {
    const state = getState() as { auth: AuthState };
    await fetch(`/api/marketing/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${state.auth.token}` },
    });
    return id;
});

const marketingSlice = createSlice({
    name: 'marketing',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(fetchMarketing.pending, (state) => { state.status = 'loading'; })
            .addCase(fetchMarketing.fulfilled, (state, action) => {
                state.status = 'succeeded';
                state.list = action.payload;
            })
            .addCase(fetchMarketing.rejected, (state, action) => {
                state.status = 'failed';
                state.error = action.error.message || null;
            })
            .addCase(createMarketing.fulfilled, (state, action) => {
                state.list.push(action.payload);
            })
            .addCase(updateMarketing.fulfilled, (state, action) => {
                const idx = state.list.findIndex((m) => m.id === action.payload.id);
                if (idx !== -1) state.list[idx] = action.payload;
            })
            .addCase(deleteMarketing.fulfilled, (state, action) => {
                state.list = state.list.filter((m) => m.id !== action.payload);
            });
    },
});

export default marketingSlice.reducer;
