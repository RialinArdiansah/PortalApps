import { createSlice, createAsyncThunk, PayloadAction } from '@reduxjs/toolkit';
import type { Submission, AuthState, PaginationState } from '@/types';
import { DEFAULT_ITEMS_PER_PAGE } from '@/types';

interface SubmissionsState {
    list: Submission[];
    status: 'idle' | 'loading' | 'succeeded' | 'failed';
    error: string | null;
    pagination: PaginationState;
}

const initialState: SubmissionsState = {
    list: [],
    status: 'idle',
    error: null,
    pagination: {
        currentPage: 1,
        itemsPerPage: DEFAULT_ITEMS_PER_PAGE,
        searchTerm: '',
    },
};

export const fetchSubmissions = createAsyncThunk('submissions/fetch', async (_, { getState }) => {
    const state = getState() as { auth: AuthState };
    const res = await fetch('/api/submissions', {
        headers: { Authorization: `Bearer ${state.auth.token}` },
    });
    const data = await res.json();
    return data.data as Submission[];
});

export const createSubmission = createAsyncThunk('submissions/create', async (sub: Omit<Submission, 'id'>, { getState }) => {
    const state = getState() as { auth: AuthState };
    const res = await fetch('/api/submissions', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${state.auth.token}` },
        body: JSON.stringify(sub),
    });
    const data = await res.json();
    return data.data as Submission;
});

export const updateSubmission = createAsyncThunk('submissions/update', async ({ id, ...sub }: Partial<Submission> & { id: string }, { getState }) => {
    const state = getState() as { auth: AuthState };
    const res = await fetch(`/api/submissions/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${state.auth.token}` },
        body: JSON.stringify(sub),
    });
    const data = await res.json();
    return data.data as Submission;
});

export const deleteSubmission = createAsyncThunk('submissions/delete', async (id: string, { getState }) => {
    const state = getState() as { auth: AuthState };
    await fetch(`/api/submissions/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${state.auth.token}` },
    });
    return id;
});

const submissionsSlice = createSlice({
    name: 'submissions',
    initialState,
    reducers: {
        setPage(state, action: PayloadAction<number>) {
            state.pagination.currentPage = action.payload;
        },
        setItemsPerPage(state, action: PayloadAction<number>) {
            state.pagination.itemsPerPage = action.payload;
            state.pagination.currentPage = 1;
        },
        setSearchTerm(state, action: PayloadAction<string>) {
            state.pagination.searchTerm = action.payload;
            state.pagination.currentPage = 1; // Reset to page 1 on search
        },
    },
    extraReducers: (builder) => {
        builder
            .addCase(fetchSubmissions.pending, (state) => { state.status = 'loading'; })
            .addCase(fetchSubmissions.fulfilled, (state, action) => {
                state.status = 'succeeded';
                state.list = action.payload;
            })
            .addCase(fetchSubmissions.rejected, (state, action) => {
                state.status = 'failed';
                state.error = action.error.message || null;
            })
            .addCase(createSubmission.fulfilled, (state, action) => {
                state.list.push(action.payload);
            })
            .addCase(updateSubmission.fulfilled, (state, action) => {
                const idx = state.list.findIndex((s) => s.id === action.payload.id);
                if (idx !== -1) state.list[idx] = action.payload;
            })
            .addCase(deleteSubmission.fulfilled, (state, action) => {
                state.list = state.list.filter((s) => s.id !== action.payload);
            });
    },
});

export const { setPage, setItemsPerPage, setSearchTerm } = submissionsSlice.actions;
export default submissionsSlice.reducer;
