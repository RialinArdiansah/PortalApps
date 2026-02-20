<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubmissionResource;
use App\Models\Submission;
use App\Services\SubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function __construct(
        private SubmissionService $submissionService,
    ) {
    }

    /**
     * GET /api/submissions
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Submission::query();

        // Role-based filtering: marketing/mitra/karyawan see only their own
        if (!$user->canViewAll()) {
            $query->where('submitted_by_id', $user->id);
        }

        return response()->json([
            'success' => true,
            'data' => SubmissionResource::collection($query->get()),
        ]);
    }

    /**
     * POST /api/submissions
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'companyName' => 'required|string|max:255',
            'marketingName' => 'required|string|max:255',
            'inputDate' => 'required|date',
            'certificateType' => 'required|string|max:255',
            'sbuType' => 'required|string|max:50',
            'selectedSub' => 'nullable|array',
            'selectedKlasifikasi' => 'nullable|array',
            'selectedSubKlasifikasi' => 'nullable|string',
            'selectedKualifikasi' => 'nullable|array',
            'selectedBiayaLainnya' => 'nullable|array',
            'biayaSetorKantor' => 'required|integer|min:0',
        ]);

        // Server-side keuntungan calculation — never trust frontend value
        $keuntungan = $this->submissionService->calculateKeuntungan(
            (int) $validated['biayaSetorKantor'],
            $validated['selectedKualifikasi'] ?? null,
            $validated['selectedBiayaLainnya'] ?? null,
        );

        $submission = Submission::create([
            'company_name' => $validated['companyName'],
            'marketing_name' => $validated['marketingName'],
            'input_date' => $validated['inputDate'],
            'submitted_by_id' => $request->user()->id,
            'certificate_type' => $validated['certificateType'],
            'sbu_type' => $validated['sbuType'],
            'selected_sub' => $validated['selectedSub'] ?? null,
            'selected_klasifikasi' => $validated['selectedKlasifikasi'] ?? null,
            'selected_sub_klasifikasi' => $validated['selectedSubKlasifikasi'] ?? null,
            'selected_kualifikasi' => $validated['selectedKualifikasi'] ?? null,
            'selected_biaya_lainnya' => $validated['selectedBiayaLainnya'] ?? null,
            'biaya_setor_kantor' => $validated['biayaSetorKantor'],
            'keuntungan' => $keuntungan,
        ]);

        return response()->json([
            'success' => true,
            'data' => new SubmissionResource($submission),
        ], 201);
    }

    /**
     * PUT /api/submissions/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $submission = Submission::findOrFail($id);

        // Authorize: admin/manager can edit all, others only their own
        if (!$request->user()->canViewAll() && $submission->submitted_by_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'companyName' => 'sometimes|string|max:255',
            'marketingName' => 'sometimes|string|max:255',
            'inputDate' => 'sometimes|date',
            'certificateType' => 'sometimes|string|max:255',
            'sbuType' => 'sometimes|string|max:50',
            'selectedSub' => 'nullable|array',
            'selectedKlasifikasi' => 'nullable|array',
            'selectedSubKlasifikasi' => 'nullable|string',
            'selectedKualifikasi' => 'nullable|array',
            'selectedBiayaLainnya' => 'nullable|array',
            'biayaSetorKantor' => 'sometimes|integer|min:0',
        ]);

        $updateData = [];
        if (isset($validated['companyName']))
            $updateData['company_name'] = $validated['companyName'];
        if (isset($validated['marketingName']))
            $updateData['marketing_name'] = $validated['marketingName'];
        if (isset($validated['inputDate']))
            $updateData['input_date'] = $validated['inputDate'];
        if (isset($validated['certificateType']))
            $updateData['certificate_type'] = $validated['certificateType'];
        if (isset($validated['sbuType']))
            $updateData['sbu_type'] = $validated['sbuType'];
        if (array_key_exists('selectedSub', $validated))
            $updateData['selected_sub'] = $validated['selectedSub'];
        if (array_key_exists('selectedKlasifikasi', $validated))
            $updateData['selected_klasifikasi'] = $validated['selectedKlasifikasi'];
        if (array_key_exists('selectedSubKlasifikasi', $validated))
            $updateData['selected_sub_klasifikasi'] = $validated['selectedSubKlasifikasi'];
        if (array_key_exists('selectedKualifikasi', $validated))
            $updateData['selected_kualifikasi'] = $validated['selectedKualifikasi'];
        if (array_key_exists('selectedBiayaLainnya', $validated))
            $updateData['selected_biaya_lainnya'] = $validated['selectedBiayaLainnya'];
        if (isset($validated['biayaSetorKantor']))
            $updateData['biaya_setor_kantor'] = $validated['biayaSetorKantor'];

        // Recalculate keuntungan if cost fields changed
        if (isset($updateData['biaya_setor_kantor']) || array_key_exists('selected_kualifikasi', $updateData) || array_key_exists('selected_biaya_lainnya', $updateData)) {
            $biayaSetor = $updateData['biaya_setor_kantor'] ?? $submission->biaya_setor_kantor;
            $kualifikasi = array_key_exists('selected_kualifikasi', $updateData) ? $updateData['selected_kualifikasi'] : $submission->selected_kualifikasi;
            $biayaLainnya = array_key_exists('selected_biaya_lainnya', $updateData) ? $updateData['selected_biaya_lainnya'] : $submission->selected_biaya_lainnya;

            $updateData['keuntungan'] = $this->submissionService->calculateKeuntungan(
                (int) $biayaSetor,
                $kualifikasi,
                $biayaLainnya,
            );
        }

        $submission->update($updateData);

        return response()->json([
            'success' => true,
            'data' => new SubmissionResource($submission->fresh()),
        ]);
    }

    /**
     * DELETE /api/submissions/{id}
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $submission = Submission::findOrFail($id);

        if (!$request->user()->canViewAll() && $submission->submitted_by_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }

        $submission->delete();

        return response()->json(['success' => true]);
    }
}
