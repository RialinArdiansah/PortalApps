<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\Transaction;
use App\Models\FeeP3sm;

class DashboardService
{
    /**
     * Calculate dashboard summary statistics.
     * Matches frontend calculateDashboardSummary exactly.
     */
    public function getSummary(?string $userId = null): array
    {
        $subsQuery = Submission::query();
        $transQuery = Transaction::query();

        if ($userId) {
            $subsQuery->where('submitted_by_id', $userId);
            $transQuery->where('submitted_by_id', $userId);
        }

        $submissions = $subsQuery->get();
        $transactions = $transQuery->get();
        $feeP3sm = FeeP3sm::all();

        $totalKeuntungan = $submissions->sum('keuntungan') + $feeP3sm->sum('cost');
        $totalPemasukan = $submissions->sum('biaya_setor_kantor');
        $totalSertifikat = $submissions->count();
        $totalPengeluaran = $transactions->where('transaction_type', 'Keluar')->sum('cost');
        $totalTabungan = $transactions->where('transaction_type', 'Tabungan')->sum('cost');

        return [
            'totalKeuntungan' => $totalKeuntungan,
            'totalPemasukan' => $totalPemasukan,
            'totalSertifikat' => $totalSertifikat,
            'totalPengeluaran' => $totalPengeluaran,
            'totalTabungan' => $totalTabungan,
        ];
    }

    /**
     * Calculate marketing ranking.
     * Matches frontend calculateMarketingRanking exactly.
     */
    public function getRanking(?string $userId = null): array
    {
        $query = Submission::query();

        if ($userId) {
            $query->where('submitted_by_id', $userId);
        }

        $submissions = $query->get();
        $rankMap = [];

        foreach ($submissions as $sub) {
            $name = $sub->marketing_name;
            if (!$name)
                continue;

            if (!isset($rankMap[$name])) {
                $rankMap[$name] = ['count' => 0, 'totalKeuntungan' => 0];
            }
            $rankMap[$name]['count']++;
            $rankMap[$name]['totalKeuntungan'] += $sub->keuntungan ?? 0;
        }

        $ranking = [];
        foreach ($rankMap as $name => $data) {
            $ranking[] = [
                'name' => $name,
                'count' => $data['count'],
                'totalKeuntungan' => $data['totalKeuntungan'],
            ];
        }

        // Sort by count DESC
        usort($ranking, fn($a, $b) => $b['count'] - $a['count']);

        return $ranking;
    }
}
