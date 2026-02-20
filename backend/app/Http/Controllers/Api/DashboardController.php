<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService,
    ) {
    }

    /**
     * GET /api/dashboard/summary
     */
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        $userId = $user->canViewAll() ? null : $user->id;

        return response()->json([
            'success' => true,
            'data' => $this->dashboardService->getSummary($userId),
        ]);
    }

    /**
     * GET /api/dashboard/ranking
     */
    public function ranking(Request $request): JsonResponse
    {
        $user = $request->user();
        $userId = $user->canViewAll() ? null : $user->id;

        return response()->json([
            'success' => true,
            'data' => $this->dashboardService->getRanking($userId),
        ]);
    }
}
