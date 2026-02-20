// =====================================================
// MSW — In-Memory Mock Database
// Initialized from legacy seed data
// =====================================================

import type { User, Certificate, MarketingName, Submission, Transaction, FeeP3SM, BiayaData, SbuData, KlasifikasiData } from '@/types';
import {
    initialUsers, initialCertificates, initialMarketingNames,
    initialUserSubmissions, initialTransactions, initialFeeP3SMData,
    initialSbuKonstruksiData, initialKonstruksiKlasifikasiData,
    initialP3SMKualifikasiData, initialP3SMBiayaSetorData, initialP3SMBiayaLainnyaData,
    initialGapeknasKualifikasiData, initialGapeknasBiayaSetorData, initialGapeknasBiayaLainnyaData,
    initialSbuKonsultanData, initialKonsultanKlasifikasiData,
    initialKonsultanKualifikasiData, initialKonsultanBiayaSetorData, initialKonsultanBiayaLainnyaData,
    initialSkkKonstruksiData, initialSkkKlasifikasiData,
    initialSkkKualifikasiData, initialSkkBiayaSetorData, initialSkkBiayaLainnyaData,
    initialSmapBiayaSetorData, initialSimpkBiayaSetorData,
    initialNotarisBiayaSetorData, initialNotarisKualifikasiData, initialNotarisBiayaLainnyaData,
} from '@/data/seed';

// Deep clone to avoid mutation of seed data
const deepClone = <T>(data: T): T => JSON.parse(JSON.stringify(data));

export interface MockDB {
    users: User[];
    certificates: Certificate[];
    marketingNames: MarketingName[];
    submissions: Submission[];
    transactions: Transaction[];
    feeP3SM: FeeP3SM[];
    // Reference data
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
    // Auth
    activeToken: string | null;
    activeUserId: string | null;
}

export const db: MockDB = {
    users: deepClone(initialUsers),
    certificates: deepClone(initialCertificates),
    marketingNames: deepClone(initialMarketingNames),
    submissions: deepClone(initialUserSubmissions),
    transactions: deepClone(initialTransactions),
    feeP3SM: deepClone(initialFeeP3SMData),
    sbuKonstruksiData: deepClone(initialSbuKonstruksiData),
    konstruksiKlasifikasiData: deepClone(initialKonstruksiKlasifikasiData),
    p3smKualifikasiData: deepClone(initialP3SMKualifikasiData),
    p3smBiayaSetorData: deepClone(initialP3SMBiayaSetorData),
    p3smBiayaLainnyaData: deepClone(initialP3SMBiayaLainnyaData),
    gapeknasKualifikasiData: deepClone(initialGapeknasKualifikasiData),
    gapeknasBiayaSetorData: deepClone(initialGapeknasBiayaSetorData),
    gapeknasBiayaLainnyaData: deepClone(initialGapeknasBiayaLainnyaData),
    sbuKonsultanData: deepClone(initialSbuKonsultanData),
    konsultanKlasifikasiData: deepClone(initialKonsultanKlasifikasiData),
    konsultanKualifikasiData: deepClone(initialKonsultanKualifikasiData),
    konsultanBiayaSetorData: deepClone(initialKonsultanBiayaSetorData),
    konsultanBiayaLainnyaData: deepClone(initialKonsultanBiayaLainnyaData),
    skkKonstruksiData: deepClone(initialSkkKonstruksiData),
    skkKlasifikasiData: deepClone(initialSkkKlasifikasiData),
    skkKualifikasiData: deepClone(initialSkkKualifikasiData),
    skkBiayaSetorData: deepClone(initialSkkBiayaSetorData),
    skkBiayaLainnyaData: deepClone(initialSkkBiayaLainnyaData),
    smapBiayaSetorData: deepClone(initialSmapBiayaSetorData),
    simpkBiayaSetorData: deepClone(initialSimpkBiayaSetorData),
    notarisBiayaSetorData: deepClone(initialNotarisBiayaSetorData),
    notarisKualifikasiData: deepClone(initialNotarisKualifikasiData),
    notarisBiayaLainnyaData: deepClone(initialNotarisBiayaLainnyaData),
    activeToken: null,
    activeUserId: null,
};
