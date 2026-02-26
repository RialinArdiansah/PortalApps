import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import type { Certificate, BiayaData, SbuData, KlasifikasiData, DynamicRefData, AuthState, MenuConfig } from '@/types';

interface CertificatesState {
    list: Certificate[];
    status: 'idle' | 'loading' | 'succeeded' | 'failed';
    error: string | null;
    // Reference data for cascading forms
    sbuKonstruksiData: SbuData[];
    konstruksiKlasifikasiData: KlasifikasiData[];
    p3smKualifikasiData: BiayaData[];
    p3smBiayaSetorData: BiayaData[];
    p3smBiayaLainnyaData: BiayaData[];
    gapeknasKualifikasiData: BiayaData[];
    gapeknasBiayaSetorData: BiayaData[];
    gapeknasBiayaLainnyaData: BiayaData[];
    sbuKonsultanData: SbuData[];
    konsultanKlasifikasiData: KlasifikasiData[];
    konsultanKualifikasiData: BiayaData[];
    konsultanBiayaSetorData: BiayaData[];
    konsultanBiayaLainnyaData: BiayaData[];
    skkKonstruksiData: SbuData[];
    skkKlasifikasiData: KlasifikasiData[];
    skkKualifikasiData: BiayaData[];
    skkBiayaSetorData: BiayaData[];
    skkBiayaLainnyaData: BiayaData[];
    smapBiayaSetorData: BiayaData[];
    simpkBiayaSetorData: BiayaData[];
    notarisBiayaSetorData: BiayaData[];
    notarisKualifikasiData: BiayaData[];
    notarisBiayaLainnyaData: BiayaData[];
    // Dynamic reference data for all types (including new ones)
    dynamicReferenceData: Record<string, DynamicRefData>;
}

const initialState: CertificatesState = {
    list: [],
    status: 'idle',
    error: null,
    sbuKonstruksiData: [],
    konstruksiKlasifikasiData: [],
    p3smKualifikasiData: [],
    p3smBiayaSetorData: [],
    p3smBiayaLainnyaData: [],
    gapeknasKualifikasiData: [],
    gapeknasBiayaSetorData: [],
    gapeknasBiayaLainnyaData: [],
    sbuKonsultanData: [],
    konsultanKlasifikasiData: [],
    konsultanKualifikasiData: [],
    konsultanBiayaSetorData: [],
    konsultanBiayaLainnyaData: [],
    skkKonstruksiData: [],
    skkKlasifikasiData: [],
    skkKualifikasiData: [],
    skkBiayaSetorData: [],
    skkBiayaLainnyaData: [],
    smapBiayaSetorData: [],
    simpkBiayaSetorData: [],
    notarisBiayaSetorData: [],
    notarisKualifikasiData: [],
    notarisBiayaLainnyaData: [],
    dynamicReferenceData: {},
};

export const fetchCertificates = createAsyncThunk('certificates/fetch', async (_, { getState }) => {
    const state = getState() as { auth: AuthState };
    const res = await fetch('/api/certificates', {
        headers: { Authorization: `Bearer ${state.auth.token}` },
    });
    const data = await res.json();
    return data.data;
});

export const createCertificate = createAsyncThunk('certificates/create', async (cert: { name: string; subMenus?: string[]; menuConfig?: MenuConfig }, { getState }) => {
    const state = getState() as { auth: AuthState };
    const res = await fetch('/api/certificates', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${state.auth.token}` },
        body: JSON.stringify(cert),
    });
    const data = await res.json();
    return data.data as Certificate;
});

export const updateCertificate = createAsyncThunk('certificates/update', async ({ id, ...cert }: { id: string; name?: string; subMenus?: string[]; menuConfig?: MenuConfig }, { getState }) => {
    const state = getState() as { auth: AuthState };
    const res = await fetch(`/api/certificates/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${state.auth.token}` },
        body: JSON.stringify(cert),
    });
    const data = await res.json();
    return data.data as Certificate;
});

export const deleteCertificate = createAsyncThunk('certificates/delete', async (id: string, { getState }) => {
    const state = getState() as { auth: AuthState };
    await fetch(`/api/certificates/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${state.auth.token}` },
    });
    return id;
});

const certificatesSlice = createSlice({
    name: 'certificates',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(fetchCertificates.pending, (state) => { state.status = 'loading'; })
            .addCase(fetchCertificates.fulfilled, (state, action) => {
                state.status = 'succeeded';
                state.list = action.payload.certificates;
                const ref = action.payload.referenceData;
                state.sbuKonstruksiData = ref.sbuKonstruksiData;
                state.konstruksiKlasifikasiData = ref.konstruksiKlasifikasiData;
                state.p3smKualifikasiData = ref.p3smKualifikasiData;
                state.p3smBiayaSetorData = ref.p3smBiayaSetorData;
                state.p3smBiayaLainnyaData = ref.p3smBiayaLainnyaData;
                state.gapeknasKualifikasiData = ref.gapeknasKualifikasiData;
                state.gapeknasBiayaSetorData = ref.gapeknasBiayaSetorData;
                state.gapeknasBiayaLainnyaData = ref.gapeknasBiayaLainnyaData;
                state.sbuKonsultanData = ref.sbuKonsultanData;
                state.konsultanKlasifikasiData = ref.konsultanKlasifikasiData;
                state.konsultanKualifikasiData = ref.konsultanKualifikasiData;
                state.konsultanBiayaSetorData = ref.konsultanBiayaSetorData;
                state.konsultanBiayaLainnyaData = ref.konsultanBiayaLainnyaData;
                state.skkKonstruksiData = ref.skkKonstruksiData;
                state.skkKlasifikasiData = ref.skkKlasifikasiData;
                state.skkKualifikasiData = ref.skkKualifikasiData;
                state.skkBiayaSetorData = ref.skkBiayaSetorData;
                state.skkBiayaLainnyaData = ref.skkBiayaLainnyaData;
                state.smapBiayaSetorData = ref.smapBiayaSetorData;
                state.simpkBiayaSetorData = ref.simpkBiayaSetorData;
                state.notarisBiayaSetorData = ref.notarisBiayaSetorData;
                state.notarisKualifikasiData = ref.notarisKualifikasiData;
                state.notarisBiayaLainnyaData = ref.notarisBiayaLainnyaData;
                state.dynamicReferenceData = ref.dynamicReferenceData || {};
            })
            .addCase(fetchCertificates.rejected, (state, action) => {
                state.status = 'failed';
                state.error = action.error.message || null;
            })
            .addCase(createCertificate.fulfilled, (state, action) => {
                state.list.push(action.payload);
            })
            .addCase(updateCertificate.fulfilled, (state, action) => {
                const idx = state.list.findIndex((c) => c.id === action.payload.id);
                if (idx !== -1) state.list[idx] = action.payload;
            })
            .addCase(deleteCertificate.fulfilled, (state, action) => {
                state.list = state.list.filter((c) => c.id !== action.payload);
            });
    },
});

export default certificatesSlice.reducer;
