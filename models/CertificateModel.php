<?php
class CertificateModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM certificates ORDER BY created_at')->fetchAll();
    }

    public function findById(string $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM certificates WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): string
    {
        $id = generate_uuid();
        $stmt = $this->db->prepare(
            'INSERT INTO certificates (id, name, sub_menus, sbu_type_slug, created_at, updated_at)
             VALUES (?, ?, ?, ?, NOW(), NOW())'
        );
        $stmt->execute([
            $id,
            $data['name'],
            json_encode($data['sub_menus'] ?? []),
            $data['sbu_type_slug'] ?? null,
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
        $this->db->prepare('UPDATE certificates SET ' . implode(', ', $fields) . ' WHERE id = ?')
                 ->execute($values);
    }

    public function delete(string $id): void
    {
        $this->db->prepare('DELETE FROM certificates WHERE id = ?')->execute([$id]);
    }
}
