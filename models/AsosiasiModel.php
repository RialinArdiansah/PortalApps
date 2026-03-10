<?php
class AsosiasiModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findBySbuTypeId(string $sbuTypeId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM asosiasi WHERE sbu_type_id = ? ORDER BY name');
        $stmt->execute([$sbuTypeId]);
        return $stmt->fetchAll();
    }

    public function findByName(string $sbuTypeId, string $name): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM asosiasi WHERE sbu_type_id = ? AND name = ?');
        $stmt->execute([$sbuTypeId, $name]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): string
    {
        $id = generate_uuid();
        $stmt = $this->db->prepare(
            'INSERT INTO asosiasi (id, sbu_type_id, name, sub_klasifikasi, created_at, updated_at)
             VALUES (?, ?, ?, ?, NOW(), NOW())'
        );
        $stmt->execute([
            $id,
            $data['sbu_type_id'],
            $data['name'],
            isset($data['sub_klasifikasi']) ? json_encode($data['sub_klasifikasi']) : null,
        ]);
        return $id;
    }

    public function deleteBySbuTypeId(string $sbuTypeId): void
    {
        $this->db->prepare('DELETE FROM asosiasi WHERE sbu_type_id = ?')->execute([$sbuTypeId]);
    }
}
