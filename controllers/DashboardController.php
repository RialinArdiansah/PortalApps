<?php
class DashboardController
{
    public function index(): void
    {
        $userId = Auth::canViewAll() ? null : Auth::id();
        $user = Auth::user();

        $submissionModel = new SubmissionModel();
        $transactionModel = new TransactionModel();
        $feeP3smModel = new FeeP3smModel();

        // Summary stats
        $totalKeuntungan = $submissionModel->sumKeuntungan($userId) + $feeP3smModel->sumAll();
        $totalPemasukan = $submissionModel->sumBiayaSetor($userId);
        $totalSertifikat = $submissionModel->countAll($userId);
        $totalPengeluaran = $transactionModel->sumByType('Keluar', $userId);
        $totalTabungan = $transactionModel->sumByType('Tabungan', $userId);

        // Raw data for charts
        $submissions = $submissionModel->allRaw($userId);
        $transactions = $transactionModel->allRaw($userId);

        // Certificate distribution for doughnut
        $certDistribution = [];
        foreach ($submissions as $sub) {
            $type = $sub['certificate_type'] ?? 'Unknown';
            if (!isset($certDistribution[$type])) {
                $certDistribution[$type] = ['count' => 0, 'revenue' => 0, 'profit' => 0];
            }
            $certDistribution[$type]['count']++;
            $certDistribution[$type]['revenue'] += $sub['biaya_setor_kantor'] ?? 0;
            $certDistribution[$type]['profit'] += $sub['keuntungan'] ?? 0;
        }

        // Marketing ranking
        $rankMap = [];
        foreach ($submissions as $sub) {
            $name = $sub['marketing_name'] ?? '';
            if (!$name) continue;
            if (!isset($rankMap[$name])) {
                $rankMap[$name] = ['count' => 0, 'totalKeuntungan' => 0];
            }
            $rankMap[$name]['count']++;
            $rankMap[$name]['totalKeuntungan'] += $sub['keuntungan'] ?? 0;
        }
        $marketingRanking = [];
        foreach ($rankMap as $name => $data) {
            $marketingRanking[] = ['name' => $name, 'count' => $data['count'], 'totalKeuntungan' => $data['totalKeuntungan']];
        }
        usort($marketingRanking, fn($a, $b) => $b['count'] - $a['count']);

        // Marketing distribution (for donut chart)
        $marketingDistribution = [];
        foreach ($rankMap as $name => $data) {
            $marketingDistribution[] = ['name' => $name, 'value' => $data['count']];
        }

        // Monthly profits for area chart
        $monthlyProfits = [];
        foreach ($submissions as $sub) {
            $date = $sub['input_date'] ?? '';
            if (!$date) continue;
            $monthKey = date('M Y', strtotime($date));
            $sortKey = date('Y-m', strtotime($date));
            if (!isset($monthlyProfits[$sortKey])) {
                $monthlyProfits[$sortKey] = ['month' => $monthKey, 'keuntungan' => 0, 'pemasukan' => 0, 'sort' => $sortKey];
            }
            $monthlyProfits[$sortKey]['keuntungan'] += $sub['keuntungan'] ?? 0;
            $monthlyProfits[$sortKey]['pemasukan'] += $sub['biaya_setor_kantor'] ?? 0;
        }

        // Add pengeluaran to monthly data from transactions
        foreach ($transactions as $t) {
            $date = $t['transaction_date'] ?? '';
            if (!$date) continue;
            $monthKey = date('M Y', strtotime($date));
            $sortKey = date('Y-m', strtotime($date));
            if (!isset($monthlyProfits[$sortKey])) {
                $monthlyProfits[$sortKey] = ['month' => $monthKey, 'keuntungan' => 0, 'pemasukan' => 0, 'sort' => $sortKey];
            }
            if (!isset($monthlyProfits[$sortKey]['pengeluaran'])) {
                $monthlyProfits[$sortKey]['pengeluaran'] = 0;
            }
            if (($t['transaction_type'] ?? '') === 'Keluar') {
                $monthlyProfits[$sortKey]['pengeluaran'] += $t['cost'] ?? 0;
            }
        }

        ksort($monthlyProfits);
        $monthlyProfits = array_values($monthlyProfits);

        // Financial KPIs
        $netMarginPct = $totalPemasukan > 0 ? round(($totalKeuntungan / $totalPemasukan) * 100, 1) : 0;
        $avgRevenuePerCert = $totalSertifikat > 0 ? round($totalPemasukan / $totalSertifikat) : 0;
        $totalCertTypes = count($certDistribution);
        $highestEarningType = '-';
        $topRevenueSource = '-';
        $maxProfit = 0;
        $maxRevenue = 0;
        foreach ($certDistribution as $type => $data) {
            if ($data['profit'] > $maxProfit) {
                $maxProfit = $data['profit'];
                $highestEarningType = $type;
            }
            if ($data['revenue'] > $maxRevenue) {
                $maxRevenue = $data['revenue'];
                $topRevenueSource = $type;
            }
        }

        // Cert by type sorted (for bar charts)
        $certByType = [];
        foreach ($certDistribution as $name => $data) {
            $certByType[] = ['name' => $name, 'count' => $data['count'], 'revenue' => $data['revenue'], 'keuntungan' => $data['profit']];
        }
        usort($certByType, fn($a, $b) => $b['count'] - $a['count']);

        // Greeting
        $hour = (int) date('G');
        if ($hour < 11) {
            $greeting = ['icon' => '☀️', 'text' => 'Selamat Pagi'];
        } elseif ($hour < 15) {
            $greeting = ['icon' => '🌤️', 'text' => 'Selamat Siang'];
        } elseif ($hour < 18) {
            $greeting = ['icon' => '🌅', 'text' => 'Selamat Sore'];
        } else {
            $greeting = ['icon' => '🌙', 'text' => 'Selamat Malam'];
        }

        $useCharts = true;

        view('dashboard/index', compact(
            'useCharts', 'user', 'greeting',
            'totalKeuntungan', 'totalPemasukan', 'totalSertifikat',
            'totalPengeluaran', 'totalTabungan',
            'certDistribution', 'certByType', 'marketingRanking',
            'marketingDistribution', 'monthlyProfits',
            'netMarginPct', 'avgRevenuePerCert', 'totalCertTypes',
            'highestEarningType', 'topRevenueSource',
            'submissions', 'transactions'
        ));
    }

    // AJAX endpoints remain the same
    public function summaryJson(): void
    {
        $userId = Auth::canViewAll() ? null : Auth::id();
        $submissionModel = new SubmissionModel();
        $transactionModel = new TransactionModel();
        $feeP3smModel = new FeeP3smModel();

        json_response([
            'success' => true,
            'data' => [
                'totalKeuntungan' => $submissionModel->sumKeuntungan($userId) + $feeP3smModel->sumAll(),
                'totalPemasukan' => $submissionModel->sumBiayaSetor($userId),
                'totalSertifikat' => $submissionModel->countAll($userId),
                'totalPengeluaran' => $transactionModel->sumByType('Keluar', $userId),
                'totalTabungan' => $transactionModel->sumByType('Tabungan', $userId),
            ],
        ]);
    }

    public function rankingJson(): void
    {
        $userId = Auth::canViewAll() ? null : Auth::id();
        $submissions = (new SubmissionModel())->allRaw($userId);
        $rankMap = [];
        foreach ($submissions as $sub) {
            $name = $sub['marketing_name'] ?? '';
            if (!$name) continue;
            if (!isset($rankMap[$name])) {
                $rankMap[$name] = ['count' => 0, 'totalKeuntungan' => 0];
            }
            $rankMap[$name]['count']++;
            $rankMap[$name]['totalKeuntungan'] += $sub['keuntungan'] ?? 0;
        }
        $ranking = [];
        foreach ($rankMap as $name => $data) {
            $ranking[] = ['name' => $name, 'count' => $data['count'], 'totalKeuntungan' => $data['totalKeuntungan']];
        }
        usort($ranking, fn($a, $b) => $b['count'] - $a['count']);
        json_response(['success' => true, 'data' => $ranking]);
    }

    public function chartDataJson(): void
    {
        $userId = Auth::canViewAll() ? null : Auth::id();
        $submissions = (new SubmissionModel())->allRaw($userId);

        $certDistribution = [];
        foreach ($submissions as $sub) {
            $type = $sub['certificate_type'] ?? 'Unknown';
            if (!isset($certDistribution[$type])) {
                $certDistribution[$type] = ['count' => 0, 'revenue' => 0, 'profit' => 0];
            }
            $certDistribution[$type]['count']++;
            $certDistribution[$type]['revenue'] += $sub['biaya_setor_kantor'] ?? 0;
            $certDistribution[$type]['profit'] += $sub['keuntungan'] ?? 0;
        }

        json_response(['success' => true, 'data' => ['certDistribution' => $certDistribution]]);
    }
}
