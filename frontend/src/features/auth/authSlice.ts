import { createSlice, createAsyncThunk, PayloadAction } from '@reduxjs/toolkit';
import type { User, AuthState, LoginCredentials } from '@/types';

const initialState: AuthState = {
    user: null,
    token: null,
    status: 'idle',
    error: null,
};

export const login = createAsyncThunk('auth/login', async (credentials: LoginCredentials, { rejectWithValue }) => {
    const response = await fetch('/api/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(credentials),
    });
    const data = await response.json();
    if (!data.success) return rejectWithValue(data.message || 'Login gagal');
    return data.data as { user: User; token: string };
});

export const logout = createAsyncThunk('auth/logout', async (_, { getState }) => {
    const state = getState() as { auth: AuthState };
    await fetch('/api/logout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${state.auth.token}`,
        },
    });
});

export const fetchCurrentUser = createAsyncThunk('auth/fetchCurrentUser', async (_, { getState, rejectWithValue }) => {
    const state = getState() as { auth: AuthState };
    if (!state.auth.token) return rejectWithValue('No token');
    const response = await fetch('/api/me', {
        headers: { Authorization: `Bearer ${state.auth.token}` },
    });
    const data = await response.json();
    if (!data.success) return rejectWithValue(data.message);
    return data.data as User;
});

const authSlice = createSlice({
    name: 'auth',
    initialState,
    reducers: {
        clearError(state) {
            state.error = null;
        },
    },
    extraReducers: (builder) => {
        builder
            .addCase(login.pending, (state) => {
                state.status = 'loading';
                state.error = null;
            })
            .addCase(login.fulfilled, (state, action: PayloadAction<{ user: User; token: string }>) => {
                state.status = 'succeeded';
                state.user = action.payload.user;
                state.token = action.payload.token;
            })
            .addCase(login.rejected, (state, action) => {
                state.status = 'failed';
                state.error = action.payload as string || 'Login failed';
            })
            .addCase(logout.fulfilled, (state) => {
                state.user = null;
                state.token = null;
                state.status = 'idle';
            })
            .addCase(fetchCurrentUser.fulfilled, (state, action: PayloadAction<User>) => {
                state.user = action.payload;
            });
    },
});

export const { clearError } = authSlice.actions;
export default authSlice.reducer;
