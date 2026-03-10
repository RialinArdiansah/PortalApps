<?php
class FeeP3smModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM fee_p3sm ORDER BY year DESC, month DESC')->fetchAll();
    }

    public function findById(string $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM fee_p3sm WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): string
    {
        $id = generate_uuid();
        $stmt = $this->db->prepare(
            'INSERT INTO fee_p3sm (id, cost, month, year, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())'
        );
        $stmt->execute([$id, $data['cost'], $data['month'], $data['year']]);
        return $id;
    }

    public function update(string $id, array $data): void
    {
        $fields = [];
        $values = [];
        foreach ($data as $key => $val) {
            $fields[] = "{$key} = ?";
            $values[] = $val;
        }
        $fields[] = 'updated_at = NOW()';
        $values[] = $id;
        $this->db->prepare('UPDATE fee_p3sm SET ' . implode(', ', $fields) . ' WHERE id = ?')
                 ->execute($values);
    }

    public function delete(string $id): void
    {
        $this->db->prepare('DELETE FROM fee_p3sm WHERE id = ?')->execute([$id]);
    }

    public function sumAll(): int
    {
        return (int) $this->db->query('SELECT COALESCE(SUM(cost), 0) FROM fee_p3sm')->fetchColumn();
    }
}
