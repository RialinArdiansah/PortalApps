<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CertificateResource;
use App\Models\Certificate;
use App\Models\SbuType;
use App\Services\ReferenceDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    // Known slugs for the 6 original certificate types
    private const KNOWN_SLUGS = ['konstruksi', 'konsultan', 'skk', 'smap', 'simpk', 'notaris'];

    // Map known certificate names to their slug
    private const NAME_TO_SLUG = [
        'SBU Konstruksi' => 'konstruksi',
        'SKK Konstruksi' => 'skk',
        'SBU Konsultan' => 'konsultan',
        'Dokumen SMAP' => 'smap',
        'Akun SIMPK dan Alat' => 'simpk',
        'Notaris' => 'notaris',
    ];

    public function __construct(
        private ReferenceDataService $referenceDataService,
    ) {
    }

    /**
     * GET /api/certificates
     * Returns certificates + all reference data (static 22-key + dynamic)
     */
    public function index(Request $request): JsonResponse
    {
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
     * Creates a certificate and auto-creates an SbuType record for it.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subMenus' => 'sometimes|array',
            'subMenus.*' => 'string',
            'menuConfig' => 'sometimes|array',
            'menuConfig.asosiasi' => 'sometimes|boolean',
            'menuConfig.klasifikasi' => 'sometimes|boolean',
            'menuConfig.kualifikasi' => 'sometimes|boolean',
            'menuConfig.kualifikasiLabel' => 'sometimes|string|max:50',
            'menuConfig.biayaSetor' => 'sometimes|boolean',
            'menuConfig.biayaSetorLabel' => 'sometimes|nullable|string|max:100',
            'menuConfig.biayaLainnya' => 'sometimes|boolean',
            'menuConfig.kodeField' => 'sometimes|array',
            'menuConfig.kodeField.enabled' => 'sometimes|boolean',
            'menuConfig.kodeField.label' => 'sometimes|string|max:50',
        ]);

        // Generate slug from name
        $slug = Str::slug($validated['name']);

        // Check if this is a known cert name → use its predefined slug
        if (isset(self::NAME_TO_SLUG[$validated['name']])) {
            $slug = self::NAME_TO_SLUG[$validated['name']];
        }

        // Ensure slug is unique
        $baseSlug = $slug;
        $counter = 1;
        while (SbuType::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        // Default menu config for new types
        $defaultMenuConfig = [
            'asosiasi' => false,
            'klasifikasi' => false,
            'kualifikasi' => true,
            'kualifikasiLabel' => 'Kualifikasi',
            'biayaSetor' => true,
            'biayaSetorLabel' => '',
            'biayaLainnya' => true,
            'kodeField' => ['enabled' => false, 'label' => 'Kode'],
        ];

        $menuConfig = array_merge($defaultMenuConfig, $validated['menuConfig'] ?? []);

        // Create SbuType record
        SbuType::firstOrCreate(
            ['slug' => $slug],
            ['name' => $validated['name'], 'menu_config' => $menuConfig]
        );

        $cert = Certificate::create([
            'name' => $validated['name'],
            'sub_menus' => $validated['subMenus'] ?? [],
            'sbu_type_slug' => $slug,
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
            'menuConfig' => 'sometimes|array',
            'menuConfig.asosiasi' => 'sometimes|boolean',
            'menuConfig.klasifikasi' => 'sometimes|boolean',
            'menuConfig.kualifikasi' => 'sometimes|boolean',
            'menuConfig.kualifikasiLabel' => 'sometimes|string|max:50',
            'menuConfig.biayaSetor' => 'sometimes|boolean',
            'menuConfig.biayaSetorLabel' => 'sometimes|nullable|string|max:100',
            'menuConfig.biayaLainnya' => 'sometimes|boolean',
            'menuConfig.kodeField' => 'sometimes|array',
            'menuConfig.kodeField.enabled' => 'sometimes|boolean',
            'menuConfig.kodeField.label' => 'sometimes|string|max:50',
        ]);

        $updateData = [];
        if (isset($validated['name']))
            $updateData['name'] = $validated['name'];
        if (isset($validated['subMenus']))
            $updateData['sub_menus'] = $validated['subMenus'];

        $cert->update($updateData);

        // Update menu config on linked SbuType
        if (isset($validated['menuConfig']) && $cert->sbu_type_slug) {
            $sbuType = SbuType::where('slug', $cert->sbu_type_slug)->first();
            if ($sbuType) {
                $sbuType->update(['menu_config' => $validated['menuConfig']]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => new CertificateResource($cert->fresh()),
        ]);
    }

    /**
     * DELETE /api/certificates/{id}
     * Also cleans up the associated SbuType if it's not a known type
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $this->authorizeAdmin($request);

        $cert = Certificate::findOrFail($id);
        $slug = $cert->sbu_type_slug;

        $cert->delete();

        // Clean up SbuType for dynamic (non-original) types
        if ($slug && !in_array($slug, self::KNOWN_SLUGS)) {
            SbuType::where('slug', $slug)->delete();
        }

        return response()->json(['success' => true]);
    }

    /**
     * PUT /api/certificates/reference-data
     * Transactional batch upsert of reference data.
     * Accepts any valid sbu_type slug (not just the 6 originals).
     */
    public function updateReferenceData(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'sbuType' => 'required|string|exists:sbu_types,slug',
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
