<?php

namespace App\Services;

use App\Models\SbuType;
use App\Models\Asosiasi;
use App\Models\Klasifikasi;
use App\Models\BiayaItem;
use Illuminate\Support\Facades\DB;

class ReferenceDataService
{
    /**
     * Build the 22-key referenceData structure matching frontend exactly.
     */
    public function getAllReferenceData(): array
    {
        $konstruksi = SbuType::where('slug', 'konstruksi')->first();
        $konsultan = SbuType::where('slug', 'konsultan')->first();
        $skk = SbuType::where('slug', 'skk')->first();
        $smap = SbuType::where('slug', 'smap')->first();
        $simpk = SbuType::where('slug', 'simpk')->first();
        $notaris = SbuType::where('slug', 'notaris')->first();

        // Helper: format asosiasi as SbuData
        $formatAsosiasi = fn($sbuTypeId) => Asosiasi::where('sbu_type_id', $sbuTypeId)
            ->get()
            ->map(fn($a) => ['id' => $a->id, 'name' => $a->name])
            ->values()
            ->toArray();

        // Helper: format klasifikasi as KlasifikasiData
        $formatKlasifikasi = fn($sbuTypeId) => Klasifikasi::where('sbu_type_id', $sbuTypeId)
            ->get()
            ->map(fn($k) => array_filter([
                'id' => $k->id,
                'name' => $k->name,
                'subKlasifikasi' => $k->sub_klasifikasi ?? [],
                'kualifikasi' => $k->kualifikasi,
                'subBidang' => $k->sub_bidang,
            ], fn($v) => $v !== null))
            ->values()
            ->toArray();

        // Helper: format biaya items as BiayaData
        $formatBiaya = fn($sbuTypeId, $category, $asosiasiId = null) => BiayaItem::where('sbu_type_id', $sbuTypeId)
            ->where('category', $category)
            ->when($asosiasiId, fn($q) => $q->where('asosiasi_id', $asosiasiId))
            ->when($asosiasiId === null && !in_array($category, ['kualifikasi', 'biaya_setor', 'biaya_lainnya']), fn($q) => $q->whereNull('asosiasi_id'))
            ->get()
            ->map(fn($b) => ['id' => $b->id, 'name' => $b->name, 'biaya' => $b->biaya])
            ->values()
            ->toArray();

        // Find P3SM and GAPEKNAS asosiasi IDs
        $p3sm = $konstruksi ? Asosiasi::where('sbu_type_id', $konstruksi->id)->where('name', 'P3SM')->first() : null;
        $gapeknas = $konstruksi ? Asosiasi::where('sbu_type_id', $konstruksi->id)->where('name', 'GAPEKNAS')->first() : null;

        return [
            'sbuKonstruksiData' => $konstruksi ? $formatAsosiasi($konstruksi->id) : [],
            'konstruksiKlasifikasiData' => $konstruksi ? $formatKlasifikasi($konstruksi->id) : [],
            'p3smKualifikasiData' => $p3sm ? $formatBiaya($konstruksi->id, 'kualifikasi', $p3sm->id) : [],
            'p3smBiayaSetorData' => $p3sm ? $formatBiaya($konstruksi->id, 'biaya_setor', $p3sm->id) : [],
            'p3smBiayaLainnyaData' => $p3sm ? $formatBiaya($konstruksi->id, 'biaya_lainnya', $p3sm->id) : [],
            'gapeknasKualifikasiData' => $gapeknas ? $formatBiaya($konstruksi->id, 'kualifikasi', $gapeknas->id) : [],
            'gapeknasBiayaSetorData' => $gapeknas ? $formatBiaya($konstruksi->id, 'biaya_setor', $gapeknas->id) : [],
            'gapeknasBiayaLainnyaData' => $gapeknas ? $formatBiaya($konstruksi->id, 'biaya_lainnya', $gapeknas->id) : [],
            'sbuKonsultanData' => $konsultan ? $formatAsosiasi($konsultan->id) : [],
            'konsultanKlasifikasiData' => $konsultan ? $formatKlasifikasi($konsultan->id) : [],
            'konsultanKualifikasiData' => $konsultan ? $formatBiaya($konsultan->id, 'kualifikasi') : [],
            'konsultanBiayaSetorData' => $konsultan ? $formatBiaya($konsultan->id, 'biaya_setor') : [],
            'konsultanBiayaLainnyaData' => $konsultan ? $formatBiaya($konsultan->id, 'biaya_lainnya') : [],
            'skkKonstruksiData' => $skk ? $formatAsosiasi($skk->id) : [],
            'skkKlasifikasiData' => $skk ? $formatKlasifikasi($skk->id) : [],
            'skkKualifikasiData' => $skk ? $formatBiaya($skk->id, 'kualifikasi') : [],
            'skkBiayaSetorData' => $skk ? $formatBiaya($skk->id, 'biaya_setor') : [],
            'skkBiayaLainnyaData' => $skk ? $formatBiaya($skk->id, 'biaya_lainnya') : [],
            'smapBiayaSetorData' => $smap ? $formatBiaya($smap->id, 'biaya_setor') : [],
            'simpkBiayaSetorData' => $simpk ? $formatBiaya($simpk->id, 'biaya_setor') : [],
            'notarisBiayaSetorData' => $notaris ? $formatBiaya($notaris->id, 'biaya_setor') : [],
            'notarisKualifikasiData' => $notaris ? $formatBiaya($notaris->id, 'kualifikasi') : [],
            'notarisBiayaLainnyaData' => $notaris ? $formatBiaya($notaris->id, 'biaya_lainnya') : [],
        ];
    }

    /**
     * Transactional batch upsert of reference data for a given sbuType.
     */
    public function updateReferenceData(string $sbuTypeSlug, array $data): void
    {
        DB::transaction(function () use ($sbuTypeSlug, $data) {
            $sbuType = SbuType::where('slug', $sbuTypeSlug)->firstOrFail();

            // Map frontend keys to [category, asosiasiName]
            $mappings = $this->getMappings($sbuTypeSlug);

            foreach ($mappings as $frontendKey => [$category, $asosiasiName]) {
                if (!isset($data[$frontendKey]))
                    continue;

                $asosiasiId = null;
                if ($asosiasiName) {
                    $asosiasi = Asosiasi::where('sbu_type_id', $sbuType->id)->where('name', $asosiasiName)->first();
                    $asosiasiId = $asosiasi?->id;
                }

                // Delete existing items for this category + asosiasi
                BiayaItem::where('sbu_type_id', $sbuType->id)
                    ->where('category', $category)
                    ->when($asosiasiId, fn($q) => $q->where('asosiasi_id', $asosiasiId))
                    ->when(!$asosiasiId, fn($q) => $q->whereNull('asosiasi_id'))
                    ->delete();

                // Insert new items
                foreach ($data[$frontendKey] as $item) {
                    BiayaItem::create([
                        'sbu_type_id' => $sbuType->id,
                        'asosiasi_id' => $asosiasiId,
                        'category' => $category,
                        'name' => $item['name'],
                        'biaya' => $item['biaya'] ?? 0,
                    ]);
                }
            }

            // Handle asosiasi data (sbuData)
            if (isset($data['sbuData'])) {
                Asosiasi::where('sbu_type_id', $sbuType->id)->delete();
                foreach ($data['sbuData'] as $item) {
                    Asosiasi::create([
                        'sbu_type_id' => $sbuType->id,
                        'name' => $item['name'],
                        'sub_klasifikasi' => $item['subKlasifikasi'] ?? null,
                    ]);
                }
            }

            // Handle klasifikasi data
            if (isset($data['klasifikasiData'])) {
                Klasifikasi::where('sbu_type_id', $sbuType->id)->delete();
                foreach ($data['klasifikasiData'] as $item) {
                    Klasifikasi::create([
                        'sbu_type_id' => $sbuType->id,
                        'name' => $item['name'],
                        'sub_klasifikasi' => $item['subKlasifikasi'] ?? [],
                        'kualifikasi' => $item['kualifikasi'] ?? null,
                        'sub_bidang' => $item['subBidang'] ?? null,
                    ]);
                }
            }
        });
    }

    private function getMappings(string $slug): array
    {
        return match ($slug) {
            'konstruksi' => [
                'p3smKualifikasiData' => ['kualifikasi', 'P3SM'],
                'p3smBiayaSetorData' => ['biaya_setor', 'P3SM'],
                'p3smBiayaLainnyaData' => ['biaya_lainnya', 'P3SM'],
                'gapeknasKualifikasiData' => ['kualifikasi', 'GAPEKNAS'],
                'gapeknasBiayaSetorData' => ['biaya_setor', 'GAPEKNAS'],
                'gapeknasBiayaLainnyaData' => ['biaya_lainnya', 'GAPEKNAS'],
            ],
            'konsultan' => [
                'kualifikasiData' => ['kualifikasi', null],
                'biayaSetorData' => ['biaya_setor', null],
                'biayaLainnyaData' => ['biaya_lainnya', null],
            ],
            'skk' => [
                'kualifikasiData' => ['kualifikasi', null],
                'biayaSetorData' => ['biaya_setor', null],
                'biayaLainnyaData' => ['biaya_lainnya', null],
            ],
            'smap' => [
                'biayaSetorData' => ['biaya_setor', null],
            ],
            'simpk' => [
                'biayaSetorData' => ['biaya_setor', null],
            ],
            'notaris' => [
                'kualifikasiData' => ['kualifikasi', null],
                'biayaSetorData' => ['biaya_setor', null],
                'biayaLainnyaData' => ['biaya_lainnya', null],
            ],
            default => [],
        };
    }
}
