<?php

namespace App\Services;

class SubmissionService
{
    /**
     * Calculate keuntungan (profit) for a submission.
     * Formula: biayaSetorKantor - biayaKualifikasi - (biayaLainnya || 0)
     */
    public function calculateKeuntungan(int $biayaSetorKantor, ?array $selectedKualifikasi, ?array $selectedBiayaLainnya): int
    {
        $biayaKualifikasi = $selectedKualifikasi['biaya'] ?? 0;
        $biayaLainnya = $selectedBiayaLainnya['biaya'] ?? 0;

        return $biayaSetorKantor - $biayaKualifikasi - $biayaLainnya;
    }
}
