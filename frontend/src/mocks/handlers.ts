// =====================================================
// MSW — REST API Handlers
// All mock endpoints matching legacy use cases
// =====================================================

import { http, HttpResponse, delay } from 'msw';
import { db } from './db';

const generateId = (): string => `id-${Date.now()}-${Math.floor(Math.random() * 1000)}`;
const generateToken = (): string => `mock-token-${Date.now()}`;

export const handlers = [
    // ========== AUTH ==========
    http.post('/api/login', async ({ request }) => {
        await delay(300);
        const body = await request.json() as { username: string; password: string };
        const user = db.users.find((u) => u.username === body.username);

        if (!user) {
            return HttpResponse.json({ success: false, message: 'Username tidak ditemukan' }, { status: 401 });
        }

        // Legacy uses base64; we compare decoded password
        const decodedPassword = atob(user.password);
        if (body.password !== decodedPassword) {
            return HttpResponse.json({ success: false, message: 'Password salah' }, { status: 401 });
        }

        const token = generateToken();
        db.activeToken = token;
        db.activeUserId = user.id;

        return HttpResponse.json({
            success: true,
            data: { user: { ...user, password: undefined }, token },
        });
    }),

    http.post('/api/logout', async () => {
        await delay(100);
        db.activeToken = null;
        db.activeUserId = null;
        return HttpResponse.json({ success: true });
    }),

    http.get('/api/me', async ({ request }) => {
        await delay(100);
        const authHeader = request.headers.get('Authorization');
        if (!authHeader || !db.activeToken || authHeader !== `Bearer ${db.activeToken}`) {
            return HttpResponse.json({ success: false, message: 'Unauthorized' }, { status: 401 });
        }
        const user = db.users.find((u) => u.id === db.activeUserId);
        if (!user) {
            return HttpResponse.json({ success: false, message: 'User not found' }, { status: 404 });
        }
        return HttpResponse.json({ success: true, data: { ...user, password: undefined } });
    }),

    // ========== USERS ==========
    http.get('/api/users', async () => {
        await delay(200);
        return HttpResponse.json({
            success: true,
            data: db.users.map((u) => ({ ...u, password: undefined })),
        });
    }),

    http.post('/api/users', async ({ request }) => {
        await delay(200);
        const body = await request.json() as Partial<typeof db.users[0]>;
        const newUser = {
            id: generateId(),
            fullName: body.fullName || '',
            username: body.username || '',
            email: body.email || '',
            password: btoa(body.password || ''),
            role: body.role || 'karyawan' as const,
        };
        db.users.push(newUser as typeof db.users[0]);
        return HttpResponse.json({ success: true, data: { ...newUser, password: undefined } }, { status: 201 });
    }),

    http.put('/api/users/:id', async ({ params, request }) => {
        await delay(200);
        const { id } = params;
        const body = await request.json() as Partial<typeof db.users[0]>;
        const idx = db.users.findIndex((u) => u.id === id);
        if (idx === -1) {
            return HttpResponse.json({ success: false, message: 'User not found' }, { status: 404 });
        }
        if (body.fullName) db.users[idx].fullName = body.fullName;
        if (body.username) db.users[idx].username = body.username;
        if (body.email) db.users[idx].email = body.email;
        if (body.password) db.users[idx].password = btoa(body.password);
        if (body.role) db.users[idx].role = body.role;
        return HttpResponse.json({ success: true, data: { ...db.users[idx], password: undefined } });
    }),

    http.delete('/api/users/:id', async ({ params }) => {
        await delay(200);
        const { id } = params;
        const idx = db.users.findIndex((u) => u.id === id);
        if (idx === -1) {
            return HttpResponse.json({ success: false, message: 'User not found' }, { status: 404 });
        }
        db.users.splice(idx, 1);
        return HttpResponse.json({ success: true });
    }),

    // ========== CERTIFICATES ==========
    http.get('/api/certificates', async () => {
        await delay(150);
        return HttpResponse.json({
            success: true,
            data: {
                certificates: db.certificates,
                referenceData: {
                    sbuKonstruksiData: db.sbuKonstruksiData,
                    konstruksiKlasifikasiData: db.konstruksiKlasifikasiData,
                    p3smKualifikasiData: db.p3smKualifikasiData,
                    p3smBiayaSetorData: db.p3smBiayaSetorData,
                    p3smBiayaLainnyaData: db.p3smBiayaLainnyaData,
                    gapeknasKualifikasiData: db.gapeknasKualifikasiData,
                    gapeknasBiayaSetorData: db.gapeknasBiayaSetorData,
                    gapeknasBiayaLainnyaData: db.gapeknasBiayaLainnyaData,
                    sbuKonsultanData: db.sbuKonsultanData,
                    konsultanKlasifikasiData: db.konsultanKlasifikasiData,
                    konsultanKualifikasiData: db.konsultanKualifikasiData,
                    konsultanBiayaSetorData: db.konsultanBiayaSetorData,
                    konsultanBiayaLainnyaData: db.konsultanBiayaLainnyaData,
                    skkKonstruksiData: db.skkKonstruksiData,
                    skkKlasifikasiData: db.skkKlasifikasiData,
                    skkKualifikasiData: db.skkKualifikasiData,
                    skkBiayaSetorData: db.skkBiayaSetorData,
                    skkBiayaLainnyaData: db.skkBiayaLainnyaData,
                    smapBiayaSetorData: db.smapBiayaSetorData,
                    simpkBiayaSetorData: db.simpkBiayaSetorData,
                    notarisBiayaSetorData: db.notarisBiayaSetorData,
                    notarisKualifikasiData: db.notarisKualifikasiData,
                    notarisBiayaLainnyaData: db.notarisBiayaLainnyaData,
                },
            },
        });
    }),

    http.post('/api/certificates', async ({ request }) => {
        await delay(200);
        const body = await request.json() as { name: string; subMenus?: string[] };
        const newCert = { id: generateId(), name: body.name, subMenus: body.subMenus || [] };
        db.certificates.push(newCert);
        return HttpResponse.json({ success: true, data: newCert }, { status: 201 });
    }),

    http.put('/api/certificates/:id', async ({ params, request }) => {
        await delay(200);
        const { id } = params;
        const body = await request.json() as Partial<typeof db.certificates[0]>;
        const idx = db.certificates.findIndex((c) => c.id === id);
        if (idx === -1) {
            return HttpResponse.json({ success: false, message: 'Certificate not found' }, { status: 404 });
        }
        if (body.name) db.certificates[idx].name = body.name;
        if (body.subMenus) db.certificates[idx].subMenus = body.subMenus;
        return HttpResponse.json({ success: true, data: db.certificates[idx] });
    }),

    http.delete('/api/certificates/:id', async ({ params }) => {
        await delay(200);
        const { id } = params;
        const idx = db.certificates.findIndex((c) => c.id === id);
        if (idx === -1) {
            return HttpResponse.json({ success: false, message: 'Certificate not found' }, { status: 404 });
        }
        db.certificates.splice(idx, 1);
        return HttpResponse.json({ success: true });
    }),

    // ========== REFERENCE DATA UPDATE ==========
    http.put('/api/certificates/reference-data', async ({ request }) => {
        await delay(300);
        const body = await request.json() as Record<string, unknown>;
        const sbuType = body.sbuType as string;

        if (sbuType === 'konstruksi') {
            if (body.sbuData) db.sbuKonstruksiData = body.sbuData as typeof db.sbuKonstruksiData;
            if (body.klasifikasiData) db.konstruksiKlasifikasiData = body.klasifikasiData as typeof db.konstruksiKlasifikasiData;
            if (body.p3smKualifikasiData) db.p3smKualifikasiData = body.p3smKualifikasiData as typeof db.p3smKualifikasiData;
            if (body.p3smBiayaSetorData) db.p3smBiayaSetorData = body.p3smBiayaSetorData as typeof db.p3smBiayaSetorData;
            if (body.p3smBiayaLainnyaData) db.p3smBiayaLainnyaData = body.p3smBiayaLainnyaData as typeof db.p3smBiayaLainnyaData;
            if (body.gapeknasKualifikasiData) db.gapeknasKualifikasiData = body.gapeknasKualifikasiData as typeof db.gapeknasKualifikasiData;
            if (body.gapeknasBiayaSetorData) db.gapeknasBiayaSetorData = body.gapeknasBiayaSetorData as typeof db.gapeknasBiayaSetorData;
            if (body.gapeknasBiayaLainnyaData) db.gapeknasBiayaLainnyaData = body.gapeknasBiayaLainnyaData as typeof db.gapeknasBiayaLainnyaData;
        } else if (sbuType === 'konsultan') {
            if (body.sbuData) db.sbuKonsultanData = body.sbuData as typeof db.sbuKonsultanData;
            if (body.klasifikasiData) db.konsultanKlasifikasiData = body.klasifikasiData as typeof db.konsultanKlasifikasiData;
            if (body.kualifikasiData) db.konsultanKualifikasiData = body.kualifikasiData as typeof db.konsultanKualifikasiData;
            if (body.biayaSetorData) db.konsultanBiayaSetorData = body.biayaSetorData as typeof db.konsultanBiayaSetorData;
            if (body.biayaLainnyaData) db.konsultanBiayaLainnyaData = body.biayaLainnyaData as typeof db.konsultanBiayaLainnyaData;
        } else if (sbuType === 'skk') {
            if (body.sbuData) db.skkKonstruksiData = body.sbuData as typeof db.skkKonstruksiData;
            if (body.klasifikasiData) db.skkKlasifikasiData = body.klasifikasiData as typeof db.skkKlasifikasiData;
            if (body.kualifikasiData) db.skkKualifikasiData = body.kualifikasiData as typeof db.skkKualifikasiData;
            if (body.biayaSetorData) db.skkBiayaSetorData = body.biayaSetorData as typeof db.skkBiayaSetorData;
            if (body.biayaLainnyaData) db.skkBiayaLainnyaData = body.biayaLainnyaData as typeof db.skkBiayaLainnyaData;
        } else if (sbuType === 'smap') {
            if (body.biayaSetorData) db.smapBiayaSetorData = body.biayaSetorData as typeof db.smapBiayaSetorData;
        } else if (sbuType === 'simpk') {
            if (body.biayaSetorData) db.simpkBiayaSetorData = body.biayaSetorData as typeof db.simpkBiayaSetorData;
        } else if (sbuType === 'notaris') {
            if (body.kualifikasiData) db.notarisKualifikasiData = body.kualifikasiData as typeof db.notarisKualifikasiData;
            if (body.biayaSetorData) db.notarisBiayaSetorData = body.biayaSetorData as typeof db.notarisBiayaSetorData;
            if (body.biayaLainnyaData) db.notarisBiayaLainnyaData = body.biayaLainnyaData as typeof db.notarisBiayaLainnyaData;
        }

        return HttpResponse.json({ success: true, message: 'Reference data updated' });
    }),

    // ========== MARKETING ==========
    http.get('/api/marketing', async () => {
        await delay(150);
        return HttpResponse.json({ success: true, data: db.marketingNames });
    }),

    http.post('/api/marketing', async ({ request }) => {
        await delay(200);
        const body = await request.json() as { name: string };
        const newMkt = { id: generateId(), name: body.name };
        db.marketingNames.push(newMkt);
        return HttpResponse.json({ success: true, data: newMkt }, { status: 201 });
    }),

    http.put('/api/marketing/:id', async ({ params, request }) => {
        await delay(200);
        const { id } = params;
        const body = await request.json() as { name: string };
        const idx = db.marketingNames.findIndex((m) => m.id === id);
        if (idx === -1) return HttpResponse.json({ success: false }, { status: 404 });
        db.marketingNames[idx].name = body.name;
        return HttpResponse.json({ success: true, data: db.marketingNames[idx] });
    }),

    http.delete('/api/marketing/:id', async ({ params }) => {
        await delay(200);
        const { id } = params;
        const idx = db.marketingNames.findIndex((m) => m.id === id);
        if (idx === -1) return HttpResponse.json({ success: false }, { status: 404 });
        db.marketingNames.splice(idx, 1);
        return HttpResponse.json({ success: true });
    }),

    // ========== SUBMISSIONS ==========
    http.get('/api/submissions', async () => {
        await delay(200);
        return HttpResponse.json({ success: true, data: db.submissions });
    }),

    http.post('/api/submissions', async ({ request }) => {
        await delay(300);
        const body = await request.json() as Omit<typeof db.submissions[0], 'id'>;
        const newSub = { ...body, id: generateId() };
        db.submissions.push(newSub as typeof db.submissions[0]);
        return HttpResponse.json({ success: true, data: newSub }, { status: 201 });
    }),

    http.put('/api/submissions/:id', async ({ params, request }) => {
        await delay(300);
        const { id } = params;
        const body = await request.json() as Partial<typeof db.submissions[0]>;
        const idx = db.submissions.findIndex((s) => s.id === id);
        if (idx === -1) return HttpResponse.json({ success: false }, { status: 404 });
        db.submissions[idx] = { ...db.submissions[idx], ...body, id: db.submissions[idx].id };
        return HttpResponse.json({ success: true, data: db.submissions[idx] });
    }),

    http.delete('/api/submissions/:id', async ({ params }) => {
        await delay(200);
        const { id } = params;
        const idx = db.submissions.findIndex((s) => s.id === id);
        if (idx === -1) return HttpResponse.json({ success: false }, { status: 404 });
        db.submissions.splice(idx, 1);
        return HttpResponse.json({ success: true });
    }),

    // ========== TRANSACTIONS ==========
    http.get('/api/transactions', async () => {
        await delay(200);
        return HttpResponse.json({ success: true, data: db.transactions });
    }),

    http.post('/api/transactions', async ({ request }) => {
        await delay(300);
        const body = await request.json() as Omit<typeof db.transactions[0], 'id'>;
        const newTran = { ...body, id: generateId() };
        db.transactions.push(newTran as typeof db.transactions[0]);
        return HttpResponse.json({ success: true, data: newTran }, { status: 201 });
    }),

    http.put('/api/transactions/:id', async ({ params, request }) => {
        await delay(300);
        const { id } = params;
        const body = await request.json() as Partial<typeof db.transactions[0]>;
        const idx = db.transactions.findIndex((t) => t.id === id);
        if (idx === -1) return HttpResponse.json({ success: false }, { status: 404 });
        db.transactions[idx] = { ...db.transactions[idx], ...body, id: db.transactions[idx].id };
        return HttpResponse.json({ success: true, data: db.transactions[idx] });
    }),

    http.delete('/api/transactions/:id', async ({ params }) => {
        await delay(200);
        const { id } = params;
        const idx = db.transactions.findIndex((t) => t.id === id);
        if (idx === -1) return HttpResponse.json({ success: false }, { status: 404 });
        db.transactions.splice(idx, 1);
        return HttpResponse.json({ success: true });
    }),

    // ========== FEE P3SM ==========
    http.get('/api/fee-p3sm', async () => {
        await delay(150);
        return HttpResponse.json({ success: true, data: db.feeP3SM });
    }),

    http.post('/api/fee-p3sm', async ({ request }) => {
        await delay(200);
        const body = await request.json() as { cost: number; month: number; year: number };
        const newFee = { id: generateId(), cost: body.cost, month: body.month, year: body.year };
        db.feeP3SM.push(newFee);
        return HttpResponse.json({ success: true, data: newFee }, { status: 201 });
    }),

    http.put('/api/fee-p3sm/:id', async ({ params, request }) => {
        await delay(200);
        const { id } = params;
        const body = await request.json() as Partial<{ cost: number; month: number; year: number }>;
        const idx = db.feeP3SM.findIndex((f) => f.id === id);
        if (idx === -1) return HttpResponse.json({ success: false }, { status: 404 });
        if (body.cost !== undefined) db.feeP3SM[idx].cost = body.cost;
        if (body.month !== undefined) db.feeP3SM[idx].month = body.month;
        if (body.year !== undefined) db.feeP3SM[idx].year = body.year;
        return HttpResponse.json({ success: true, data: db.feeP3SM[idx] });
    }),

    http.delete('/api/fee-p3sm/:id', async ({ params }) => {
        await delay(200);
        const { id } = params;
        const idx = db.feeP3SM.findIndex((f) => f.id === id);
        if (idx === -1) return HttpResponse.json({ success: false }, { status: 404 });
        db.feeP3SM.splice(idx, 1);
        return HttpResponse.json({ success: true });
    }),

    // ========== DASHBOARD ==========
    http.get('/api/dashboard/summary', async () => {
        await delay(200);
        const totalKeuntungan = db.submissions.reduce((acc, s) => acc + (s.keuntungan || 0), 0)
            + db.feeP3SM.reduce((acc, f) => acc + (f.cost || 0), 0);
        const totalPemasukan = db.submissions.reduce((acc, s) => acc + (s.biayaSetorKantor || 0), 0);
        const totalPengeluaran = db.transactions
            .filter((t) => t.transactionType === 'Keluar')
            .reduce((acc, t) => acc + (t.cost || 0), 0);
        const totalTabungan = db.transactions
            .filter((t) => t.transactionType === 'Tabungan')
            .reduce((acc, t) => acc + (t.cost || 0), 0);

        return HttpResponse.json({
            success: true,
            data: {
                totalKeuntungan,
                totalPemasukan,
                totalSertifikat: db.submissions.length,
                totalPengeluaran,
                totalTabungan,
            },
        });
    }),

    http.get('/api/dashboard/ranking', async () => {
        await delay(200);
        const rankMap = new Map<string, { count: number; totalKeuntungan: number }>();
        db.submissions.forEach((sub) => {
            const name = sub.marketingName;
            if (!name) return;
            const existing = rankMap.get(name) || { count: 0, totalKeuntungan: 0 };
            existing.count += 1;
            existing.totalKeuntungan += sub.keuntungan || 0;
            rankMap.set(name, existing);
        });

        const ranking = Array.from(rankMap.entries())
            .map(([name, data]) => ({ name, ...data }))
            .sort((a, b) => b.count - a.count);

        return HttpResponse.json({ success: true, data: ranking });
    }),
];
