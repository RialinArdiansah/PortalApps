<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MarketingNameResource;
use App\Models\MarketingName;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketingController extends Controller
{
    /**
     * GET /api/marketing
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        return response()->json([
            'success' => true,
            'data' => MarketingNameResource::collection(MarketingName::all()),
        ]);
    }

    /**
     * POST /api/marketing
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $marketing = MarketingName::create($validated);

        return response()->json([
            'success' => true,
            'data' => new MarketingNameResource($marketing),
        ], 201);
    }

    /**
     * PUT /api/marketing/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $this->authorizeAdmin($request);

        $marketing = MarketingName::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $marketing->update($validated);

        return response()->json([
            'success' => true,
            'data' => new MarketingNameResource($marketing->fresh()),
        ]);
    }

    /**
     * DELETE /api/marketing/{id}
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $this->authorizeAdmin($request);

        $marketing = MarketingName::findOrFail($id);
        $marketing->delete();

        return response()->json(['success' => true]);
    }

    private function authorizeAdmin(Request $request): void
    {
        if (!$request->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
    }
}
