<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FeeP3smResource;
use App\Models\FeeP3sm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeeP3smController extends Controller
{
    /**
     * GET /api/fee-p3sm
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => FeeP3smResource::collection(FeeP3sm::all()),
        ]);
    }

    /**
     * POST /api/fee-p3sm
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cost' => 'required|integer|min:0',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $fee = FeeP3sm::create($validated);

        return response()->json([
            'success' => true,
            'data' => new FeeP3smResource($fee),
        ], 201);
    }

    /**
     * PUT /api/fee-p3sm/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $fee = FeeP3sm::findOrFail($id);

        $validated = $request->validate([
            'cost' => 'sometimes|integer|min:0',
            'month' => 'sometimes|integer|min:1|max:12',
            'year' => 'sometimes|integer|min:2000|max:2100',
        ]);

        $fee->update($validated);

        return response()->json([
            'success' => true,
            'data' => new FeeP3smResource($fee->fresh()),
        ]);
    }

    /**
     * DELETE /api/fee-p3sm/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $fee = FeeP3sm::findOrFail($id);
        $fee->delete();

        return response()->json(['success' => true]);
    }
}
