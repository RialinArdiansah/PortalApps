<?php $pageTitle = 'Dashboard'; $useCharts = true; ?>

<!-- Hero Section -->
<div class="relative bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl sm:rounded-3xl p-6 sm:p-10 text-white shadow-xl overflow-hidden mb-6 sm:mb-8">
    <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/4 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-48 h-48 bg-indigo-400 opacity-10 rounded-full blur-2xl translate-y-1/3 -translate-x-1/4 pointer-events-none"></div>
    <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 sm:gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <span class="text-2xl sm:text-3xl"><?= $greeting['icon'] ?></span>
                <h1 class="text-xl sm:text-3xl font-bold tracking-tight">
                    <?= $greeting['text'] ?>, <?= e($user['full_name'] ?? 'Admin') ?>!
                </h1>
            </div>
            <p class="text-indigo-100 text-sm sm:text-lg opacity-90 max-w-lg">
                Selamat datang di dashboard operasional Sulthan Group.
            </p>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-5 mb-6 sm:mb-8">
    <?php
    $cards = [
        ['label' => 'Total Keuntungan', 'value' => format_rupiah($totalKeuntungan), 'icon' => '💰', 'gradient' => 'from-emerald-400 to-emerald-600'],
        ['label' => 'Total Pemasukan',  'value' => format_rupiah($totalPemasukan),  'icon' => '📥', 'gradient' => 'from-blue-400 to-blue-600'],
        ['label' => 'Total Sertifikat', 'value' => number_format($totalSertifikat), 'icon' => '📜', 'gradient' => 'from-violet-400 to-violet-600'],
        ['label' => 'Total Pengeluaran','value' => format_rupiah($totalPengeluaran), 'icon' => '📤', 'gradient' => 'from-rose-400 to-rose-600'],
        ['label' => 'Total Tabungan',   'value' => format_rupiah($totalTabungan),   'icon' => '🏦', 'gradient' => 'from-amber-400 to-amber-600'],
    ];
    foreach ($cards as $card):
    ?>
    <div class="relative overflow-hidden bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 hover:shadow-lg transition-all duration-300 group">
        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br <?= $card['gradient'] ?> opacity-10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="p-4 sm:p-6 relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 rounded-xl bg-opacity-10 dark:bg-opacity-20 text-xl"><?= $card['icon'] ?></div>
            </div>
            <h3 class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm font-medium mb-1"><?= $card['label'] ?></h3>
            <p class="text-lg sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight"><?= $card['value'] ?></p>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Content Tabs -->
<div class="flex gap-3 border-b border-slate-200 dark:border-slate-700 pb-1 overflow-x-auto mb-6 sm:mb-8" id="dashTabs">
    <button onclick="switchTab('ringkasan')" data-tab="ringkasan" class="tab-btn active px-4 sm:px-6 py-2.5 text-sm font-medium rounded-full transition-all duration-200 whitespace-nowrap bg-indigo-600 text-white shadow-md">📊 Ringkasan</button>
    <button onclick="switchTab('analisisSertifikat')" data-tab="analisisSertifikat" class="tab-btn px-4 sm:px-6 py-2.5 text-sm font-medium rounded-full transition-all duration-200 whitespace-nowrap bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700">📜 Analisis Sertifikat</button>
    <button onclick="switchTab('laporanKeuangan')" data-tab="laporanKeuangan" class="tab-btn px-4 sm:px-6 py-2.5 text-sm font-medium rounded-full transition-all duration-200 whitespace-nowrap bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700">💹 Laporan Keuangan</button>
    <button onclick="switchTab('pencapaianMarketing')" data-tab="pencapaianMarketing" class="tab-btn px-4 sm:px-6 py-2.5 text-sm font-medium rounded-full transition-all duration-200 whitespace-nowrap bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700">🏆 Pencapaian Marketing</button>
</div>

<!-- ========== TAB: Ringkasan ========== -->
<div id="tab-ringkasan" class="tab-content">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
        <!-- Tren Keuntungan Bulanan (Area Chart) -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white mb-6">Tren Keuntungan Bulanan</h3>
            <div style="height:350px"><canvas id="profitAreaChart"></canvas></div>
        </div>
        <!-- Distribusi Marketing (Donut) -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white mb-2">Distribusi Marketing</h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">Persentase kontribusi sertifikat</p>
            <div class="flex-1 min-h-[300px]"><canvas id="marketingDonutChart"></canvas></div>
        </div>
    </div>
</div>

<!-- ========== TAB: Analisis Sertifikat ========== -->
<div id="tab-analisisSertifikat" class="tab-content hidden">
    <!-- Mini Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-2xl p-4 hover:bg-white dark:hover:bg-slate-700 transition-all border border-transparent hover:border-slate-200 dark:hover:border-slate-600 hover:shadow-md">
            <div class="flex items-center gap-3 mb-2"><span class="text-lg p-2 rounded-xl bg-indigo-500 bg-opacity-10">📋</span><span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Total Diproses</span></div>
            <p class="text-xl font-bold text-slate-800 dark:text-white"><?= number_format($totalSertifikat) ?></p>
        </div>
        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-2xl p-4 hover:bg-white dark:hover:bg-slate-700 transition-all border border-transparent hover:border-slate-200 dark:hover:border-slate-600 hover:shadow-md">
            <div class="flex items-center gap-3 mb-2"><span class="text-lg p-2 rounded-xl bg-emerald-500 bg-opacity-10">📊</span><span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Rata-rata Revenue</span></div>
            <p class="text-xl font-bold text-slate-800 dark:text-white"><?= format_rupiah($avgRevenuePerCert) ?></p>
        </div>
        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-2xl p-4 hover:bg-white dark:hover:bg-slate-700 transition-all border border-transparent hover:border-slate-200 dark:hover:border-slate-600 hover:shadow-md">
            <div class="flex items-center gap-3 mb-2"><span class="text-lg p-2 rounded-xl bg-amber-500 bg-opacity-10">🏆</span><span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Tipe Tertinggi</span></div>
            <p class="text-xl font-bold text-slate-800 dark:text-white"><?= e($highestEarningType) ?></p>
        </div>
        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-2xl p-4 hover:bg-white dark:hover:bg-slate-700 transition-all border border-transparent hover:border-slate-200 dark:hover:border-slate-600 hover:shadow-md">
            <div class="flex items-center gap-3 mb-2"><span class="text-lg p-2 rounded-xl bg-violet-500 bg-opacity-10">🗂️</span><span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Jenis Sertifikat</span></div>
            <p class="text-xl font-bold text-slate-800 dark:text-white"><?= $totalCertTypes ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 sm:gap-8 mb-6">
        <!-- Bar Chart: Certificate Distribution -->
        <div class="lg:col-span-3 bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white mb-2">Distribusi Sertifikat per Tipe</h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">Jumlah sertifikat yang diproses berdasarkan kategori</p>
            <div style="height:400px"><canvas id="certDistBarChart"></canvas></div>
        </div>
        <!-- Doughnut: Certificate Distribution -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white mb-2">Top 5 Revenue</h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm mb-4">Sertifikat dengan pendapatan tertinggi</p>
            <div style="height:320px"><canvas id="topRevenueChart"></canvas></div>
            <div class="space-y-2 mt-4" id="topRevenueLegend"></div>
        </div>
    </div>

    <!-- Revenue by Type -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-100 dark:border-slate-700">
        <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white mb-2">Keuntungan per Tipe Sertifikat</h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">Perbandingan keuntungan yang dihasilkan tiap tipe sertifikat</p>
        <div style="height:300px"><canvas id="certRevenueBarChart"></canvas></div>
    </div>
</div>

<!-- ========== TAB: Laporan Keuangan ========== -->
<div id="tab-laporanKeuangan" class="tab-content hidden">
    <!-- KPI Badges -->
    <div class="flex flex-wrap gap-4 mb-6">
        <div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-opacity-5 dark:bg-opacity-10 border backdrop-blur-sm <?= $netMarginPct >= 0 ? 'bg-emerald-500 border-emerald-200 dark:border-emerald-800' : 'bg-red-500 border-red-200 dark:border-red-800' ?>">
            <span class="text-sm text-slate-500 dark:text-slate-400 font-medium">Net Margin</span>
            <span class="text-lg font-bold text-slate-800 dark:text-white"><?= $netMarginPct ?>%</span>
        </div>
        <div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-opacity-5 dark:bg-opacity-10 border backdrop-blur-sm bg-indigo-500 border-indigo-200 dark:border-indigo-800">
            <span class="text-sm text-slate-500 dark:text-slate-400 font-medium">Rata-rata per Sertifikat</span>
            <span class="text-lg font-bold text-slate-800 dark:text-white"><?= format_rupiah($avgRevenuePerCert) ?></span>
        </div>
        <div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-opacity-5 dark:bg-opacity-10 border backdrop-blur-sm bg-amber-500 border-amber-200 dark:border-amber-800">
            <span class="text-sm text-slate-500 dark:text-slate-400 font-medium">Sumber Terbesar</span>
            <span class="text-lg font-bold text-slate-800 dark:text-white"><?= e($topRevenueSource) ?></span>
        </div>
    </div>

    <!-- Revenue vs Expenses Composed Chart -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-100 dark:border-slate-700 mb-6">
        <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white mb-2">Pemasukan vs Pengeluaran Bulanan</h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">Tren arus kas masuk, keluar, dan keuntungan bersih</p>
        <div style="height:400px"><canvas id="revExpChart"></canvas></div>
    </div>

    <!-- Financial Summary Gradient Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
        <div class="bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white mb-2">Pemasukan per Sertifikat</h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">Breakdown pemasukan berdasarkan tipe</p>
            <div style="height:300px"><canvas id="revenuePieChart"></canvas></div>
        </div>
        <div class="space-y-4">
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl sm:rounded-3xl p-6 text-white shadow-lg">
                <p class="text-indigo-100 text-sm font-medium mb-1">Total Pemasukan</p>
                <p class="text-3xl font-bold tracking-tight"><?= format_rupiah($totalPemasukan) ?></p>
                <div class="w-full bg-white bg-opacity-20 rounded-full h-2 mt-4">
                    <div class="bg-white rounded-full h-2 transition-all" style="width:<?= $totalPemasukan > 0 ? min(($totalKeuntungan / $totalPemasukan) * 100, 100) : 0 ?>%"></div>
                </div>
                <p class="text-indigo-200 text-xs mt-2">Keuntungan: <?= format_rupiah($totalKeuntungan) ?></p>
            </div>
            <div class="bg-gradient-to-br from-rose-500 to-red-600 rounded-2xl sm:rounded-3xl p-6 text-white shadow-lg">
                <p class="text-rose-100 text-sm font-medium mb-1">Total Pengeluaran</p>
                <p class="text-3xl font-bold tracking-tight"><?= format_rupiah($totalPengeluaran) ?></p>
                <div class="w-full bg-white bg-opacity-20 rounded-full h-2 mt-4">
                    <div class="bg-white rounded-full h-2 transition-all" style="width:<?= $totalPemasukan > 0 ? min(($totalPengeluaran / $totalPemasukan) * 100, 100) : 0 ?>%"></div>
                </div>
                <p class="text-rose-200 text-xs mt-2">Rasio: <?= $totalPemasukan > 0 ? round(($totalPengeluaran / $totalPemasukan) * 100, 1) : 0 ?>% dari pemasukan</p>
            </div>
            <div class="bg-gradient-to-br from-emerald-500 to-green-600 rounded-2xl sm:rounded-3xl p-6 text-white shadow-lg">
                <p class="text-emerald-100 text-sm font-medium mb-1">Total Tabungan</p>
                <p class="text-3xl font-bold tracking-tight"><?= format_rupiah($totalTabungan) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- ========== TAB: Pencapaian Marketing ========== -->
<div id="tab-pencapaianMarketing" class="tab-content hidden">
    <div class="bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-slate-100 dark:border-slate-700">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white">Peringkat Kinerja Marketing</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 dark:bg-slate-700/50">
                    <tr>
                        <th class="py-4 px-4 sm:px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Peringkat</th>
                        <th class="py-4 px-4 sm:px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Marketing</th>
                        <th class="py-4 px-4 sm:px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden sm:table-cell">Performa</th>
                        <th class="py-4 px-4 sm:px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Total Profit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    <?php if (empty($marketingRanking)): ?>
                    <tr><td colspan="4" class="py-12 text-center text-slate-400 dark:text-slate-500">Belum ada data marketing.</td></tr>
                    <?php else: ?>
                    <?php foreach ($marketingRanking as $i => $r): ?>
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="py-4 px-4 sm:px-6">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-sm <?php
                                if ($i === 0) echo 'bg-gradient-to-br from-yellow-300 to-amber-400 text-white ring-2 ring-yellow-100 dark:ring-yellow-900';
                                elseif ($i === 1) echo 'bg-gradient-to-br from-slate-300 to-slate-400 text-white ring-2 ring-slate-100 dark:ring-slate-700';
                                elseif ($i === 2) echo 'bg-gradient-to-br from-orange-300 to-orange-400 text-white ring-2 ring-orange-100 dark:ring-orange-900';
                                else echo 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400';
                            ?>"><?= $i + 1 ?></div>
                        </td>
                        <td class="py-4 px-4 sm:px-6">
                            <p class="font-semibold text-slate-800 dark:text-white"><?= e($r['name']) ?></p>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Marketing Specialist</p>
                        </td>
                        <td class="py-4 px-4 sm:px-6 hidden sm:table-cell">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 w-24 h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-500 rounded-full" style="width:<?= min($r['count'] * 10, 100) ?>%"></div>
                                </div>
                                <span class="text-sm font-medium text-slate-600 dark:text-slate-300"><?= $r['count'] ?> Berkas</span>
                            </div>
                        </td>
                        <td class="py-4 px-4 sm:px-6 text-right">
                            <span class="font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-3 py-1 rounded-lg"><?= format_rupiah($r['totalKeuntungan']) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Tab switching
function switchTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('tab-' + tabId).classList.remove('hidden');

    document.querySelectorAll('.tab-btn').forEach(btn => {
        if (btn.dataset.tab === tabId) {
            btn.className = btn.className.replace(/bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700/, 'bg-indigo-600 text-white shadow-md');
        } else {
            btn.className = btn.className.replace(/bg-indigo-600 text-white shadow-md/, 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700');
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor = isDark ? '#334155' : '#e2e8f0';
    const COLORS = ['#6366f1','#22c55e','#f59e0b','#ef4444','#8b5cf6','#ec4899','#14b8a6','#f97316'];

    // ===== TAB RINGKASAN =====

    // 1) Monthly Profit Area Chart
    const monthlyData = <?= json_encode($monthlyProfits) ?>;
    if (monthlyData.length > 0) {
        const ctx1 = document.getElementById('profitAreaChart').getContext('2d');
        const gradient = ctx1.createLinearGradient(0, 0, 0, 350);
        gradient.addColorStop(0, 'rgba(99,102,241,0.2)');
        gradient.addColorStop(1, 'rgba(99,102,241,0)');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: monthlyData.map(d => d.month),
                datasets: [{
                    label: 'Keuntungan',
                    data: monthlyData.map(d => d.keuntungan),
                    borderColor: '#6366f1',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#4f46e5',
                    pointRadius: 4,
                    pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false },
                    tooltip: { callbacks: { label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID') } } },
                scales: {
                    y: { beginAtZero: true, ticks: { color: textColor, callback: v => (v/1000000).toFixed(0) + 'jt' }, grid: { color: gridColor } },
                    x: { ticks: { color: textColor }, grid: { display: false } }
                }
            }
        });
    }

    // 2) Marketing Donut
    const mktDist = <?= json_encode($marketingDistribution) ?>;
    if (mktDist.length > 0) {
        new Chart(document.getElementById('marketingDonutChart'), {
            type: 'doughnut',
            data: {
                labels: mktDist.map(d => d.name),
                datasets: [{ data: mktDist.map(d => d.value), backgroundColor: COLORS.slice(0, mktDist.length), borderWidth: 0, borderRadius: 4 }]
            },
            options: { responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { color: textColor, padding: 15, usePointStyle: true } } } }
        });
    }

    // ===== TAB ANALISIS SERTIFIKAT =====
    const certByType = <?= json_encode($certByType) ?>;

    // 3) Horizontal Bar: Cert distribution
    if (certByType.length > 0) {
        new Chart(document.getElementById('certDistBarChart'), {
            type: 'bar',
            data: {
                labels: certByType.map(d => d.name),
                datasets: [{ label: 'Jumlah', data: certByType.map(d => d.count), backgroundColor: COLORS.slice(0, certByType.length), borderWidth: 0, borderRadius: 8, barThickness: 22 }]
            },
            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ctx.parsed.x + ' sertifikat' } } },
                scales: { x: { ticks: { color: textColor }, grid: { color: gridColor } }, y: { ticks: { color: textColor }, grid: { display: false } } } }
        });
    }

    // 4) Top 5 Revenue Doughnut
    const top5 = certByType.slice(0, 5);
    if (top5.length > 0) {
        new Chart(document.getElementById('topRevenueChart'), {
            type: 'doughnut',
            data: {
                labels: top5.map(d => d.name),
                datasets: [{ data: top5.map(d => d.revenue), backgroundColor: COLORS.slice(0, top5.length), borderWidth: 0, borderRadius: 4 }]
            },
            options: { responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false },
                    tooltip: { callbacks: { label: ctx => 'Rp ' + ctx.parsed.toLocaleString('id-ID') } } } }
        });
        // Custom legend
        const legendEl = document.getElementById('topRevenueLegend');
        top5.forEach((d, i) => {
            legendEl.innerHTML += `<div class="flex items-center justify-between text-sm"><div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full" style="background:${COLORS[i]}"></div><span class="text-slate-600 dark:text-slate-300 truncate max-w-[150px]">${d.name}</span></div><span class="font-semibold text-slate-800 dark:text-white">Rp ${d.revenue.toLocaleString('id-ID')}</span></div>`;
        });
    }

    // 5) Revenue & Keuntungan by Type
    if (certByType.length > 0) {
        new Chart(document.getElementById('certRevenueBarChart'), {
            type: 'bar',
            data: {
                labels: certByType.map(d => d.name),
                datasets: [
                    { label: 'Pemasukan', data: certByType.map(d => d.revenue), backgroundColor: '#6366f1', borderRadius: 6, barPercentage: 0.6 },
                    { label: 'Keuntungan', data: certByType.map(d => d.keuntungan), backgroundColor: '#22c55e', borderRadius: 6, barPercentage: 0.6 }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false,
                plugins: { legend: { labels: { color: textColor, usePointStyle: true } },
                    tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': Rp ' + ctx.parsed.y.toLocaleString('id-ID') } } },
                scales: {
                    y: { beginAtZero: true, ticks: { color: textColor, callback: v => (v/1000000).toFixed(0) + 'jt' }, grid: { color: gridColor } },
                    x: { ticks: { color: textColor, maxRotation: 25 }, grid: { display: false } }
                }
            }
        });
    }

    // ===== TAB LAPORAN KEUANGAN =====

    // 6) Revenue vs Expenses Composed
    if (monthlyData.length > 0) {
        new Chart(document.getElementById('revExpChart'), {
            type: 'bar',
            data: {
                labels: monthlyData.map(d => d.month),
                datasets: [
                    { label: 'Pemasukan', data: monthlyData.map(d => d.pemasukan || 0), backgroundColor: 'rgba(99,102,241,0.7)', borderRadius: 6, order: 2 },
                    { label: 'Pengeluaran', data: monthlyData.map(d => d.pengeluaran || 0), backgroundColor: 'rgba(239,68,68,0.7)', borderRadius: 6, order: 2 },
                    { label: 'Keuntungan', data: monthlyData.map(d => d.keuntungan || 0), type: 'line', borderColor: '#22c55e', backgroundColor: 'transparent', borderWidth: 3, tension: 0.4, pointBackgroundColor: '#22c55e', pointRadius: 5, pointHoverRadius: 7, order: 1 }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false,
                plugins: { legend: { labels: { color: textColor, usePointStyle: true } },
                    tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': Rp ' + ctx.parsed.y.toLocaleString('id-ID') } } },
                scales: {
                    y: { beginAtZero: true, ticks: { color: textColor, callback: v => (v/1000000).toFixed(0) + 'jt' }, grid: { color: gridColor } },
                    x: { ticks: { color: textColor }, grid: { display: false } }
                }
            }
        });
    }

    // 7) Revenue Pie by Type
    if (certByType.length > 0) {
        new Chart(document.getElementById('revenuePieChart'), {
            type: 'doughnut',
            data: {
                labels: certByType.map(d => d.name),
                datasets: [{ data: certByType.map(d => d.revenue), backgroundColor: COLORS.slice(0, certByType.length), borderWidth: 0, borderRadius: 6 }]
            },
            options: { responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { color: textColor, padding: 12, usePointStyle: true, font: { size: 11 } } },
                    tooltip: { callbacks: { label: ctx => 'Rp ' + ctx.parsed.toLocaleString('id-ID') } } } }
        });
    }
});
</script>
