import { createSlice, createAsyncThunk, PayloadAction } from '@reduxjs/toolkit';
import type { Transaction, AuthState, PaginationState } from '@/types';
import { DEFAULT_ITEMS_PER_PAGE } from '@/types';

interface TransactionsState {
    list: Transaction[];
    status: 'idle' | 'loading' | 'succeeded' | 'failed';
    error: string | null;
    pagination: PaginationState;
}

const initialState: TransactionsState = {
    list: [],
    status: 'idle',
    error: null,
    pagination: {
        currentPage: 1,
        itemsPerPage: DEFAULT_ITEMS_PER_PAGE,
        searchTerm: '',
    },
};

export const fetchTransactions = createAsyncThunk('transactions/fetch', async (_, { getState }) => {
    const state = getState() as { auth: AuthState };
    const res = await fetch('/api/transactions', {
        headers: { Authorization: `Bearer ${state.auth.token}` },
    });
    const data = await res.json();
    return data.data as Transaction[];
});

export const createTransaction = createAsyncThunk('transactions/create', async (tran: Omit<Transaction, 'id'>, { getState }) => {
    const state = getState() as { auth: AuthState };
    const res = await fetch('/api/transactions', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${state.auth.token}` },
        body: JSON.stringify(tran),
    });
    const data = await res.json();
    return data.data as Transaction;
});

export const updateTransaction = createAsyncThunk('transactions/update', async ({ id, ...tran }: Partial<Transaction> & { id: string }, { getState }) => {
    const state = getState() as { auth: AuthState };
    const res = await fetch(`/api/transactions/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${state.auth.token}` },
        body: JSON.stringify(tran),
    });
    const data = await res.json();
    return data.data as Transaction;
});

export const deleteTransaction = createAsyncThunk('transactions/delete', async (id: string, { getState }) => {
    const state = getState() as { auth: AuthState };
    await fetch(`/api/transactions/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${state.auth.token}` },
    });
    return id;
});

const transactionsSlice = createSlice({
    name: 'transactions',
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
            state.pagination.currentPage = 1;
        },
    },
    extraReducers: (builder) => {
        builder
            .addCase(fetchTransactions.pending, (state) => { state.status = 'loading'; })
            .addCase(fetchTransactions.fulfilled, (state, action) => {
                state.status = 'succeeded';
                state.list = action.payload;
            })
            .addCase(fetchTransactions.rejected, (state, action) => {
                state.status = 'failed';
                state.error = action.error.message || null;
            })
            .addCase(createTransaction.fulfilled, (state, action) => {
                state.list.push(action.payload);
            })
            .addCase(updateTransaction.fulfilled, (state, action) => {
                const idx = state.list.findIndex((t) => t.id === action.payload.id);
                if (idx !== -1) state.list[idx] = action.payload;
            })
            .addCase(deleteTransaction.fulfilled, (state, action) => {
                state.list = state.list.filter((t) => t.id !== action.payload);
            });
    },
});

export const { setPage, setItemsPerPage, setSearchTerm } = transactionsSlice.actions;
export default transactionsSlice.reducer;
