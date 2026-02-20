<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CertificateResource;
use App\Models\Certificate;
use App\Services\ReferenceDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function __construct(
        private ReferenceDataService $referenceDataService,
    ) {
    }

    /**
     * GET /api/certificates
     * Returns certificates + all 22-key reference data
     */
    public function index(Request $request): JsonResponse
    {
        // All authenticated users can read certificates (needed for submission form)

        return response()->json([
            'success' => true,
            'data' => [
                'certificates' => CertificateResource::collection(Certificate::all()),
                'referenceData' => $this->referenceDataService->getAllReferenceData(),
            ],
        ]);
    }

    /**
     * POST /api/certificates
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subMenus' => 'sometimes|array',
            'subMenus.*' => 'string',
        ]);

        $cert = Certificate::create([
            'name' => $validated['name'],
            'sub_menus' => $validated['subMenus'] ?? [],
        ]);

        return response()->json([
            'success' => true,
            'data' => new CertificateResource($cert),
        ], 201);
    }

    /**
     * PUT /api/certificates/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $this->authorizeAdmin($request);

        $cert = Certificate::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'subMenus' => 'sometimes|array',
            'subMenus.*' => 'string',
        ]);

        $updateData = [];
        if (isset($validated['name']))
            $updateData['name'] = $validated['name'];
        if (isset($validated['subMenus']))
            $updateData['sub_menus'] = $validated['subMenus'];

        $cert->update($updateData);

        return response()->json([
            'success' => true,
            'data' => new CertificateResource($cert->fresh()),
        ]);
    }

    /**
     * DELETE /api/certificates/{id}
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $this->authorizeAdmin($request);

        $cert = Certificate::findOrFail($id);
        $cert->delete();

        return response()->json(['success' => true]);
    }

    /**
     * PUT /api/certificates/reference-data
     * Transactional batch upsert of reference data.
     */
    public function updateReferenceData(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'sbuType' => 'required|string|in:konstruksi,konsultan,skk,smap,simpk,notaris',
        ]);

        $this->referenceDataService->updateReferenceData(
            $validated['sbuType'],
            $request->except('sbuType'),
        );

        return response()->json([
            'success' => true,
            'message' => 'Reference data updated successfully',
        ]);
    }

    private function authorizeAdmin(Request $request): void
    {
        if (!$request->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
    }
}
