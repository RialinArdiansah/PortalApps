import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import type { User, AuthState } from '@/types';

interface UsersState {
    list: User[];
    status: 'idle' | 'loading' | 'succeeded' | 'failed';
    error: string | null;
}

const initialState: UsersState = {
    list: [],
    status: 'idle',
    error: null,
};

export const fetchUsers = createAsyncThunk('users/fetchUsers', async (_, { getState }) => {
    const state = getState() as { auth: AuthState };
    const res = await fetch('/api/users', {
        headers: { Authorization: `Bearer ${state.auth.token}` },
    });
    const data = await res.json();
    return data.data as User[];
});

export const createUser = createAsyncThunk('users/createUser', async (user: Partial<User>, { getState }) => {
    const state = getState() as { auth: AuthState };
    const res = await fetch('/api/users', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${state.auth.token}` },
        body: JSON.stringify(user),
    });
    const data = await res.json();
    return data.data as User;
});

export const updateUser = createAsyncThunk('users/updateUser', async ({ id, ...user }: Partial<User> & { id: string }, { getState }) => {
    const state = getState() as { auth: AuthState };
    const res = await fetch(`/api/users/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${state.auth.token}` },
        body: JSON.stringify(user),
    });
    const data = await res.json();
    return data.data as User;
});

export const deleteUser = createAsyncThunk('users/deleteUser', async (id: string, { getState }) => {
    const state = getState() as { auth: AuthState };
    await fetch(`/api/users/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${state.auth.token}` },
    });
    return id;
});

const usersSlice = createSlice({
    name: 'users',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(fetchUsers.pending, (state) => { state.status = 'loading'; })
            .addCase(fetchUsers.fulfilled, (state, action) => {
                state.status = 'succeeded';
                state.list = action.payload;
            })
            .addCase(fetchUsers.rejected, (state, action) => {
                state.status = 'failed';
                state.error = action.error.message || null;
            })
            .addCase(createUser.fulfilled, (state, action) => {
                state.list.push(action.payload);
            })
            .addCase(updateUser.fulfilled, (state, action) => {
                const idx = state.list.findIndex((u) => u.id === action.payload.id);
                if (idx !== -1) state.list[idx] = action.payload;
            })
            .addCase(deleteUser.fulfilled, (state, action) => {
                state.list = state.list.filter((u) => u.id !== action.payload);
            });
    },
});

export default usersSlice.reducer;
