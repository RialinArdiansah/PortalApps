<?php
class MarketingNameModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM marketing_names ORDER BY name')->fetchAll();
    }

    public function findById(string $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM marketing_names WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): string
    {
        $id = generate_uuid();
        $stmt = $this->db->prepare(
            'INSERT INTO marketing_names (id, name, created_at, updated_at) VALUES (?, ?, NOW(), NOW())'
        );
        $stmt->execute([$id, $data['name']]);
        return $id;
    }

    public function update(string $id, array $data): void
    {
        $this->db->prepare('UPDATE marketing_names SET name = ?, updated_at = NOW() WHERE id = ?')
                 ->execute([$data['name'], $id]);
    }

    public function delete(string $id): void
    {
        $this->db->prepare('DELETE FROM marketing_names WHERE id = ?')->execute([$id]);
    }
}
