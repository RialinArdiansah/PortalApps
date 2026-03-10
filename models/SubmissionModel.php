<?php
class SubmissionModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(): array
    {
        return $this->db->query(
            'SELECT s.*, u.full_name as submitted_by_name
             FROM submissions s
             LEFT JOIN users u ON s.submitted_by_id = u.id
             ORDER BY s.input_date DESC, s.created_at DESC'
        )->fetchAll();
    }

    public function findByUserId(string $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT s.*, u.full_name as submitted_by_name
             FROM submissions s
             LEFT JOIN users u ON s.submitted_by_id = u.id
             WHERE s.submitted_by_id = ?
             ORDER BY s.input_date DESC, s.created_at DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function findById(string $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM submissions WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): string
    {
        $id = generate_uuid();
        $stmt = $this->db->prepare(
            'INSERT INTO submissions (id, company_name, marketing_name, input_date, submitted_by_id,
             certificate_type, sbu_type, selected_sub, selected_klasifikasi, selected_sub_klasifikasi,
             selected_kualifikasi, selected_biaya_lainnya, biaya_setor_kantor, keuntungan, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())'
        );
        $stmt->execute([
            $id,
            $data['company_name'],
            $data['marketing_name'],
            $data['input_date'],
            $data['submitted_by_id'],
            $data['certificate_type'],
            $data['sbu_type'],
            isset($data['selected_sub']) ? json_encode($data['selected_sub']) : null,
            isset($data['selected_klasifikasi']) ? json_encode($data['selected_klasifikasi']) : null,
            $data['selected_sub_klasifikasi'] ?? null,
            isset($data['selected_kualifikasi']) ? json_encode($data['selected_kualifikasi']) : null,
            isset($data['selected_biaya_lainnya']) ? json_encode($data['selected_biaya_lainnya']) : null,
            $data['biaya_setor_kantor'] ?? 0,
            $data['keuntungan'] ?? 0,
        ]);
        return $id;
    }

    public function update(string $id, array $data): void
    {
        $fields = [];
        $values = [];
        foreach ($data as $key => $val) {
            $fields[] = "{$key} = ?";
            $values[] = is_array($val) ? json_encode($val) : $val;
        }
        $fields[] = 'updated_at = NOW()';
        $values[] = $id;
        $this->db->prepare('UPDATE submissions SET ' . implode(', ', $fields) . ' WHERE id = ?')
                 ->execute($values);
    }

    public function delete(string $id): void
    {
        $this->db->prepare('DELETE FROM submissions WHERE id = ?')->execute([$id]);
    }

    // ── Aggregate methods for dashboard ──

    public function allRaw(?string $userId = null): array
    {
        if ($userId) {
            $stmt = $this->db->prepare('SELECT * FROM submissions WHERE submitted_by_id = ?');
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        }
        return $this->db->query('SELECT * FROM submissions')->fetchAll();
    }

    public function countAll(?string $userId = null): int
    {
        if ($userId) {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM submissions WHERE submitted_by_id = ?');
            $stmt->execute([$userId]);
            return (int) $stmt->fetchColumn();
        }
        return (int) $this->db->query('SELECT COUNT(*) FROM submissions')->fetchColumn();
    }

    public function sumBiayaSetor(?string $userId = null): int
    {
        $sql = 'SELECT COALESCE(SUM(biaya_setor_kantor), 0) FROM submissions';
        if ($userId) {
            $stmt = $this->db->prepare($sql . ' WHERE submitted_by_id = ?');
            $stmt->execute([$userId]);
            return (int) $stmt->fetchColumn();
        }
        return (int) $this->db->query($sql)->fetchColumn();
    }

    public function sumKeuntungan(?string $userId = null): int
    {
        $sql = 'SELECT COALESCE(SUM(keuntungan), 0) FROM submissions';
        if ($userId) {
            $stmt = $this->db->prepare($sql . ' WHERE submitted_by_id = ?');
            $stmt->execute([$userId]);
            return (int) $stmt->fetchColumn();
        }
        return (int) $this->db->query($sql)->fetchColumn();
    }
}
