<?php
class SubmissionController
{
    public function index(): void
    {
        $model = new SubmissionModel();
        $submissions = Auth::canViewAll() ? $model->all() : $model->findByUserId(Auth::id());
        view('submissions/index', compact('submissions'));
    }

    public function create(): void
    {
        // Load certificates & reference data for dropdown
        $certificates = (new CertificateModel())->all();
        $sbuTypes = (new SbuTypeModel())->all();
        $marketingNames = (new MarketingNameModel())->all();

        // Build sbuTypes map with menuConfig
        $sbuTypesMap = [];
        foreach ($sbuTypes as $sbt) {
            $sbuTypesMap[$sbt['slug']] = $sbt;
            $sbuTypesMap[$sbt['slug']]['menu_config'] = json_decode($sbt['menu_config'] ?? '{}', true);
        }

        view('submissions/form', compact('certificates', 'sbuTypesMap', 'marketingNames'));
    }

    public function store(): void
    {
        verify_csrf();

        $prefix = trim($_POST['company_prefix'] ?? 'PT.');
        $rawName = trim($_POST['company_name'] ?? '');
        $companyName = $prefix === '-' ? $rawName : "{$prefix} {$rawName}";
        $companyName = mb_convert_case(trim($companyName), MB_CASE_TITLE, 'UTF-8');

        $marketingName = trim($_POST['marketing_name'] ?? '');
        $inputDate = $_POST['input_date'] ?? date('Y-m-d');
        $certificateType = $_POST['certificate_type'] ?? '';
        $sbuType = $_POST['sbu_type'] ?? '';
        $biayaSetorKantor = (int) preg_replace('/\D/', '', $_POST['biaya_setor_kantor'] ?? '0');

        if (empty($rawName) || empty($marketingName) || empty($certificateType)) {
            flash('error', 'Semua field wajib diisi');
            redirect('/submissions/new');
        }

        // Parse JSON fields
        $selectedSub = !empty($_POST['selected_sub']) ? json_decode($_POST['selected_sub'], true) : null;
        $selectedKlasifikasi = !empty($_POST['selected_klasifikasi']) ? json_decode($_POST['selected_klasifikasi'], true) : null;
        $selectedSubKlasifikasi = $_POST['selected_sub_klasifikasi'] ?? null;
        $selectedKualifikasi = !empty($_POST['selected_kualifikasi']) ? json_decode($_POST['selected_kualifikasi'], true) : null;
        $selectedBiayaLainnya = !empty($_POST['selected_biaya_lainnya']) ? json_decode($_POST['selected_biaya_lainnya'], true) : null;

        // Server-side profit calculation
        $biayaKualifikasi = $selectedKualifikasi['biaya'] ?? 0;
        $biayaLainnya = $selectedBiayaLainnya['biaya'] ?? 0;
        $keuntungan = $biayaSetorKantor - $biayaKualifikasi - $biayaLainnya;

        (new SubmissionModel())->create([
            'company_name' => $companyName,
            'marketing_name' => $marketingName,
            'input_date' => $inputDate,
            'submitted_by_id' => Auth::id(),
            'certificate_type' => $certificateType,
            'sbu_type' => $sbuType,
            'selected_sub' => $selectedSub,
            'selected_klasifikasi' => $selectedKlasifikasi,
            'selected_sub_klasifikasi' => $selectedSubKlasifikasi,
            'selected_kualifikasi' => $selectedKualifikasi,
            'selected_biaya_lainnya' => $selectedBiayaLainnya,
            'biaya_setor_kantor' => $biayaSetorKantor,
            'keuntungan' => $keuntungan,
        ]);

        flash('success', 'Data sertifikat berhasil disimpan');
        redirect('/submissions');
    }

    public function update(string $id): void
    {
        verify_csrf();
        $model = new SubmissionModel();
        $sub = $model->findById($id);

        if (!$sub) {
            flash('error', 'Data tidak ditemukan');
            redirect('/submissions');
        }

        // Check authorization
        if (!Auth::canViewAll() && $sub['submitted_by_id'] !== Auth::id()) {
            flash('error', 'Akses ditolak');
            redirect('/submissions');
        }

        $data = [];
        if (!empty($_POST['company_name'])) $data['company_name'] = trim($_POST['company_name']);
        if (!empty($_POST['marketing_name'])) $data['marketing_name'] = trim($_POST['marketing_name']);
        if (!empty($_POST['input_date'])) $data['input_date'] = $_POST['input_date'];
        if (!empty($_POST['certificate_type'])) $data['certificate_type'] = $_POST['certificate_type'];
        if (!empty($_POST['sbu_type'])) $data['sbu_type'] = $_POST['sbu_type'];
        if (isset($_POST['biaya_setor_kantor'])) $data['biaya_setor_kantor'] = (int) $_POST['biaya_setor_kantor'];

        // JSON fields
        foreach (['selected_sub', 'selected_klasifikasi', 'selected_kualifikasi', 'selected_biaya_lainnya'] as $field) {
            if (isset($_POST[$field])) {
                $data[$field] = json_decode($_POST[$field], true);
            }
        }
        if (isset($_POST['selected_sub_klasifikasi'])) {
            $data['selected_sub_klasifikasi'] = $_POST['selected_sub_klasifikasi'];
        }

        // Recalculate keuntungan
        $biayaSetor = $data['biaya_setor_kantor'] ?? $sub['biaya_setor_kantor'];
        $kualifikasi = $data['selected_kualifikasi'] ?? json_decode($sub['selected_kualifikasi'] ?? 'null', true);
        $biayaLainnya = $data['selected_biaya_lainnya'] ?? json_decode($sub['selected_biaya_lainnya'] ?? 'null', true);
        $data['keuntungan'] = $biayaSetor - ($kualifikasi['biaya'] ?? 0) - ($biayaLainnya['biaya'] ?? 0);

        if (!empty($data)) {
            $model->update($id, $data);
        }

        flash('success', 'Data berhasil diperbarui');
        redirect('/submissions');
    }

    public function destroy(string $id): void
    {
        verify_csrf();
        $model = new SubmissionModel();
        $sub = $model->findById($id);

        if (!$sub) {
            flash('error', 'Data tidak ditemukan');
            redirect('/submissions');
        }

        if (!Auth::canViewAll() && $sub['submitted_by_id'] !== Auth::id()) {
            flash('error', 'Akses ditolak');
            redirect('/submissions');
        }

        $model->delete($id);
        flash('success', 'Data berhasil dihapus');
        redirect('/submissions');
    }
}
