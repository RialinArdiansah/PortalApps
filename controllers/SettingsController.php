<?php
class SettingsController
{
    public function index(): void
    {
        view('settings/index');
    }

    /**
     * Export all database tables as JSON download.
     */
    public function exportJson(): void
    {
        $db = Database::getInstance();

        $tables = [
            'users', 'certificates', 'sbu_types', 'marketing_names',
            'asosiasi', 'klasifikasi', 'biaya_items',
            'submissions', 'transactions', 'fee_p3sm'
        ];

        $data = [
            'version' => '1.0.0',
            'exportedAt' => date('c'),
        ];

        foreach ($tables as $table) {
            $rows = $db->query("SELECT * FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
            $data[$table] = $rows;
        }

        $filename = 'portalapp-keu-backup-' . date('Y-m-d') . '.json';
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Import data from uploaded JSON file.
     */
    public function importJson(): void
    {
        verify_csrf();

        if (!isset($_FILES['json_file']) || $_FILES['json_file']['error'] !== UPLOAD_ERR_OK) {
            flash('error', 'File tidak ditemukan atau terjadi kesalahan upload.');
            redirect('/settings');
        }

        $file = $_FILES['json_file'];

        // Validate file type
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'json') {
            flash('error', 'File harus berformat JSON.');
            redirect('/settings');
        }

        $content = file_get_contents($file['tmp_name']);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            flash('error', 'File JSON tidak valid: ' . json_last_error_msg());
            redirect('/settings');
        }

        if (!is_array($data) || !isset($data['version'])) {
            flash('error', 'Format file tidak dikenali. Pastikan file berasal dari backup sistem ini.');
            redirect('/settings');
        }

        $db = Database::getInstance();

        // The order matters due to foreign key constraints
        // Delete child tables first, then parent tables
        $importOrder = [
            'fee_p3sm', 'transactions', 'submissions',
            'biaya_items', 'klasifikasi', 'asosiasi',
            'sbu_types', 'marketing_names', 'certificates', 'users'
        ];

        try {
            $db->beginTransaction();

            // Disable FK checks temporarily
            $db->exec('SET FOREIGN_KEY_CHECKS = 0');

            foreach ($importOrder as $table) {
                if (!isset($data[$table]) || !is_array($data[$table])) {
                    continue;
                }

                // Truncate the table
                $db->exec("TRUNCATE TABLE `{$table}`");

                $rows = $data[$table];
                if (empty($rows)) continue;

                // Build INSERT for each row
                foreach ($rows as $row) {
                    $columns = array_keys($row);
                    $placeholders = array_fill(0, count($columns), '?');
                    $colList = implode(', ', array_map(fn($c) => "`{$c}`", $columns));
                    $phList = implode(', ', $placeholders);

                    $stmt = $db->prepare("INSERT INTO `{$table}` ({$colList}) VALUES ({$phList})");
                    $stmt->execute(array_values($row));
                }
            }

            // Re-enable FK checks
            $db->exec('SET FOREIGN_KEY_CHECKS = 1');

            $db->commit();

            // Count imported records
            $totalRows = 0;
            foreach ($importOrder as $table) {
                if (isset($data[$table]) && is_array($data[$table])) {
                    $totalRows += count($data[$table]);
                }
            }

            flash('success', "Import berhasil! {$totalRows} data telah diimpor dari backup.");
        } catch (\Exception $e) {
            $db->rollBack();
            $db->exec('SET FOREIGN_KEY_CHECKS = 1');
            flash('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }

        redirect('/settings');
    }
}
