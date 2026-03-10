<?php
class BiayaItemModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findBySbuTypeId(string $sbuTypeId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM biaya_items WHERE sbu_type_id = ? ORDER BY category, name');
        $stmt->execute([$sbuTypeId]);
        return $stmt->fetchAll();
    }

    public function findByFilter(string $sbuTypeId, string $category, ?string $asosiasiId = null): array
    {
        if ($asosiasiId) {
            $stmt = $this->db->prepare('SELECT * FROM biaya_items WHERE sbu_type_id = ? AND category = ? AND asosiasi_id = ? ORDER BY name');
            $stmt->execute([$sbuTypeId, $category, $asosiasiId]);
        } else {
            $stmt = $this->db->prepare('SELECT * FROM biaya_items WHERE sbu_type_id = ? AND category = ? ORDER BY name');
            $stmt->execute([$sbuTypeId, $category]);
        }
        return $stmt->fetchAll();
    }

    public function create(array $data): string
    {
        $id = generate_uuid();
        $stmt = $this->db->prepare(
            'INSERT INTO biaya_items (id, sbu_type_id, asosiasi_id, category, name, kode, biaya, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())'
        );
        $stmt->execute([
            $id,
            $data['sbu_type_id'],
            $data['asosiasi_id'] ?? null,
            $data['category'],
            $data['name'],
            $data['kode'] ?? null,
            $data['biaya'] ?? 0,
        ]);
        return $id;
    }

    public function deleteBySbuTypeId(string $sbuTypeId): void
    {
        $this->db->prepare('DELETE FROM biaya_items WHERE sbu_type_id = ?')->execute([$sbuTypeId]);
    }
}
