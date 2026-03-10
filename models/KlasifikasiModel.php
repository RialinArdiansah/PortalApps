<?php
class KlasifikasiModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findBySbuTypeId(string $sbuTypeId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM klasifikasi WHERE sbu_type_id = ? ORDER BY name');
        $stmt->execute([$sbuTypeId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): string
    {
        $id = generate_uuid();
        $stmt = $this->db->prepare(
            'INSERT INTO klasifikasi (id, sbu_type_id, asosiasi_id, name, sub_klasifikasi, kualifikasi, sub_bidang, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())'
        );
        $stmt->execute([
            $id,
            $data['sbu_type_id'],
            $data['asosiasi_id'] ?? null,
            $data['name'],
            json_encode($data['sub_klasifikasi'] ?? []),
            isset($data['kualifikasi']) ? json_encode($data['kualifikasi']) : null,
            isset($data['sub_bidang']) ? json_encode($data['sub_bidang']) : null,
        ]);
        return $id;
    }

    public function deleteBySbuTypeId(string $sbuTypeId): void
    {
        $this->db->prepare('DELETE FROM klasifikasi WHERE sbu_type_id = ?')->execute([$sbuTypeId]);
    }
}
