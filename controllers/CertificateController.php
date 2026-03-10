<?php
class CertificateController
{
    private const KNOWN_SLUGS = ['konstruksi', 'konsultan', 'skk', 'smap', 'simpk', 'notaris'];

    private const NAME_TO_SLUG = [
        'SBU Konstruksi'       => 'konstruksi',
        'SKK Konstruksi'       => 'skk',
        'SBU Konsultan'        => 'konsultan',
        'Dokumen SMAP'         => 'smap',
        'Akun SIMPK dan Alat'  => 'simpk',
        'Notaris'              => 'notaris',
    ];

    public function index(): void
    {
        $certModel = new CertificateModel();
        $sbuModel = new SbuTypeModel();

        $certificates = $certModel->all();
        $sbuTypes = $sbuModel->all();

        // Build reference data for each certificate
        $referenceData = $this->getAllReferenceData();

        view('certificates/index', compact('certificates', 'sbuTypes', 'referenceData'));
    }

    public function store(): void
    {
        verify_csrf();
        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            flash('error', 'Nama sertifikat wajib diisi');
            redirect('/certificates');
        }

        // Generate slug
        $slug = $this->generateSlug($name);

        // Default menu config
        $menuConfig = [
            'asosiasi' => false,
            'klasifikasi' => false,
            'kualifikasi' => true,
            'kualifikasiLabel' => 'Kualifikasi',
            'biayaSetor' => true,
            'biayaSetorLabel' => '',
            'biayaLainnya' => true,
            'kodeField' => ['enabled' => false, 'label' => 'Kode'],
        ];

        // Override with POST data if present
        if (!empty($_POST['menuConfig'])) {
            $posted = json_decode($_POST['menuConfig'], true);
            if ($posted) $menuConfig = array_merge($menuConfig, $posted);
        }

        // Create SbuType
        $sbuModel = new SbuTypeModel();
        if (!$sbuModel->slugExists($slug)) {
            $sbuModel->create(['slug' => $slug, 'name' => $name, 'menu_config' => $menuConfig]);
        }

        // Create Certificate
        (new CertificateModel())->create([
            'name' => $name,
            'sub_menus' => json_decode($_POST['subMenus'] ?? '[]', true) ?? [],
            'sbu_type_slug' => $slug,
        ]);

        flash('success', 'Sertifikat berhasil ditambahkan');
        redirect('/certificates');
    }

    public function update(string $id): void
    {
        verify_csrf();
        $certModel = new CertificateModel();
        $cert = $certModel->findById($id);
        if (!$cert) {
            flash('error', 'Sertifikat tidak ditemukan');
            redirect('/certificates');
        }

        $data = [];
        if (!empty($_POST['name'])) $data['name'] = trim($_POST['name']);
        if (isset($_POST['subMenus'])) $data['sub_menus'] = json_decode($_POST['subMenus'], true) ?? [];

        if (!empty($data)) {
            $certModel->update($id, $data);
        }

        // Update menu config on SbuType
        if (!empty($_POST['menuConfig']) && $cert['sbu_type_slug']) {
            $sbuModel = new SbuTypeModel();
            $sbuType = $sbuModel->findBySlug($cert['sbu_type_slug']);
            if ($sbuType) {
                $menuConfig = json_decode($_POST['menuConfig'], true);
                if ($menuConfig) {
                    $sbuModel->update($sbuType['id'], ['menu_config' => $menuConfig]);
                }
            }
        }

        flash('success', 'Sertifikat berhasil diperbarui');
        redirect('/certificates');
    }

    public function destroy(string $id): void
    {
        verify_csrf();
        $certModel = new CertificateModel();
        $cert = $certModel->findById($id);
        if (!$cert) {
            flash('error', 'Sertifikat tidak ditemukan');
            redirect('/certificates');
        }

        $slug = $cert['sbu_type_slug'];
        $certModel->delete($id);

        // Clean up SbuType for dynamic types
        if ($slug && !in_array($slug, self::KNOWN_SLUGS)) {
            (new SbuTypeModel())->deleteBySlug($slug);
        }

        flash('success', 'Sertifikat berhasil dihapus');
        redirect('/certificates');
    }

    public function updateReferenceData(): void
    {
        verify_csrf();
        $input = json_input();
        $sbuTypeSlug = $input['sbuType'] ?? $_POST['sbuType'] ?? '';

        if (empty($sbuTypeSlug)) {
            flash('error', 'Tipe SBU tidak valid');
            redirect('/certificates');
        }

        $sbuModel = new SbuTypeModel();
        $sbuType = $sbuModel->findBySlug($sbuTypeSlug);
        if (!$sbuType) {
            json_response(['success' => false, 'message' => 'SBU type not found'], 404);
        }

        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            $biayaModel = new BiayaItemModel();
            $klasModel = new KlasifikasiModel();
            $asosModel = new AsosiasiModel();

            // Wipe existing
            $biayaModel->deleteBySbuTypeId($sbuType['id']);
            $klasModel->deleteBySbuTypeId($sbuType['id']);
            $asosModel->deleteBySbuTypeId($sbuType['id']);

            // Recreate asosiasi
            if (isset($input['sbuData'])) {
                foreach ($input['sbuData'] as $item) {
                    $asosModel->create([
                        'sbu_type_id' => $sbuType['id'],
                        'name' => $item['name'],
                        'sub_klasifikasi' => $item['subKlasifikasi'] ?? null,
                    ]);
                }
            }

            // Recreate klasifikasi
            if (isset($input['klasifikasiData'])) {
                foreach ($input['klasifikasiData'] as $item) {
                    $klasModel->create([
                        'sbu_type_id' => $sbuType['id'],
                        'name' => $item['name'],
                        'sub_klasifikasi' => $item['subKlasifikasi'] ?? [],
                        'kualifikasi' => $item['kualifikasi'] ?? null,
                        'sub_bidang' => $item['subBidang'] ?? null,
                    ]);
                }
            }

            // Recreate biaya items
            $mappings = $this->getMappings($sbuTypeSlug);
            foreach ($mappings as $frontendKey => [$category, $asosiasiName]) {
                if (!isset($input[$frontendKey])) continue;

                $asosiasiId = null;
                if ($asosiasiName) {
                    $asosiasi = $asosModel->findByName($sbuType['id'], $asosiasiName);
                    $asosiasiId = $asosiasi['id'] ?? null;
                }

                foreach ($input[$frontendKey] as $item) {
                    $biayaModel->create([
                        'sbu_type_id' => $sbuType['id'],
                        'asosiasi_id' => $asosiasiId,
                        'category' => $category,
                        'name' => $item['name'],
                        'kode' => $item['kode'] ?? null,
                        'biaya' => $item['biaya'] ?? 0,
                    ]);
                }
            }

            $db->commit();
            json_response(['success' => true, 'message' => 'Reference data updated successfully']);
        } catch (\Exception $e) {
            $db->rollBack();
            json_response(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // AJAX endpoint for dynamic reference data
    public function getReferenceDataJson(string $slug): void
    {
        $sbuModel = new SbuTypeModel();
        $sbuType = $sbuModel->findBySlug($slug);
        if (!$sbuType) {
            json_response(['success' => false], 404);
        }

        $asosiasiModel = new AsosiasiModel();
        $klasModel = new KlasifikasiModel();
        $biayaModel = new BiayaItemModel();

        $asosiasi = $asosiasiModel->findBySbuTypeId($sbuType['id']);
        $klasifikasi = $klasModel->findBySbuTypeId($sbuType['id']);
        $allBiaya = $biayaModel->findBySbuTypeId($sbuType['id']);

        // Format klasifikasi
        $formattedKlasifikasi = array_map(function ($k) {
            return array_filter([
                'id' => $k['id'],
                'name' => $k['name'],
                'subKlasifikasi' => json_decode($k['sub_klasifikasi'] ?? '[]', true),
                'kualifikasi' => json_decode($k['kualifikasi'] ?? 'null', true),
                'subBidang' => json_decode($k['sub_bidang'] ?? 'null', true),
            ], fn($v) => $v !== null);
        }, $klasifikasi);

        // Group biaya by category
        $kualifikasi = [];
        $biayaSetor = [];
        $biayaLainnya = [];
        foreach ($allBiaya as $b) {
            $item = array_filter([
                'id' => $b['id'],
                'name' => $b['name'],
                'kode' => $b['kode'],
                'biaya' => (int) $b['biaya'],
            ], fn($v) => $v !== null);

            match ($b['category']) {
                'kualifikasi' => $kualifikasi[] = $item,
                'biaya_setor' => $biayaSetor[] = $item,
                'biaya_lainnya' => $biayaLainnya[] = $item,
            };
        }

        json_response([
            'success' => true,
            'data' => [
                'asosiasi' => array_map(fn($a) => ['id' => $a['id'], 'name' => $a['name']], $asosiasi),
                'klasifikasi' => $formattedKlasifikasi,
                'kualifikasi' => $kualifikasi,
                'biayaSetor' => $biayaSetor,
                'biayaLainnya' => $biayaLainnya,
                'menuConfig' => json_decode($sbuType['menu_config'] ?? '{}', true),
            ],
        ]);
    }

    // ── Private helpers ──

    private function getAllReferenceData(): array
    {
        $sbuTypes = (new SbuTypeModel())->all();
        $dynamicData = [];

        foreach ($sbuTypes as $sbt) {
            $asosiasi = (new AsosiasiModel())->findBySbuTypeId($sbt['id']);
            $klasifikasi = (new KlasifikasiModel())->findBySbuTypeId($sbt['id']);
            $biayaItems = (new BiayaItemModel())->findBySbuTypeId($sbt['id']);

            $dynamicData[$sbt['slug']] = [
                'sbuType' => $sbt,
                'asosiasi' => $asosiasi,
                'klasifikasi' => $klasifikasi,
                'biayaItems' => $biayaItems,
            ];
        }

        return $dynamicData;
    }

    private function generateSlug(string $name): string
    {
        if (isset(self::NAME_TO_SLUG[$name])) {
            return self::NAME_TO_SLUG[$name];
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $name), '-'));
        $baseSlug = $slug;
        $counter = 1;
        $sbuModel = new SbuTypeModel();

        while ($sbuModel->slugExists($slug)) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }

    private function getMappings(string $slug): array
    {
        return match ($slug) {
            'konstruksi' => [
                'p3smKualifikasiData' => ['kualifikasi', 'P3SM'],
                'p3smBiayaSetorData' => ['biaya_setor', 'P3SM'],
                'p3smBiayaLainnyaData' => ['biaya_lainnya', 'P3SM'],
                'gapeknasKualifikasiData' => ['kualifikasi', 'GAPEKNAS'],
                'gapeknasBiayaSetorData' => ['biaya_setor', 'GAPEKNAS'],
                'gapeknasBiayaLainnyaData' => ['biaya_lainnya', 'GAPEKNAS'],
            ],
            'konsultan', 'skk', 'notaris' => [
                'kualifikasiData' => ['kualifikasi', null],
                'biayaSetorData' => ['biaya_setor', null],
                'biayaLainnyaData' => ['biaya_lainnya', null],
            ],
            'smap', 'simpk' => [
                'biayaSetorData' => ['biaya_setor', null],
            ],
            default => [
                'kualifikasiData' => ['kualifikasi', null],
                'biayaSetorData' => ['biaya_setor', null],
                'biayaLainnyaData' => ['biaya_lainnya', null],
            ],
        };
    }
}
