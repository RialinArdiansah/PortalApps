<?php
class SbuTypeModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM sbu_types ORDER BY name')->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM sbu_types WHERE slug = ?');
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public function findById(string $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM sbu_types WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function slugExists(string $slug): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM sbu_types WHERE slug = ?');
        $stmt->execute([$slug]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(array $data): string
    {
        $id = generate_uuid();
        $stmt = $this->db->prepare(
            'INSERT INTO sbu_types (id, slug, name, menu_config, created_at, updated_at)
             VALUES (?, ?, ?, ?, NOW(), NOW())'
        );
        $stmt->execute([
            $id,
            $data['slug'],
            $data['name'],
            isset($data['menu_config']) ? json_encode($data['menu_config']) : null,
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
        $this->db->prepare('UPDATE sbu_types SET ' . implode(', ', $fields) . ' WHERE id = ?')
                 ->execute($values);
    }

    public function delete(string $id): void
    {
        $this->db->prepare('DELETE FROM sbu_types WHERE id = ?')->execute([$id]);
    }

    public function deleteBySlug(string $slug): void
    {
        $this->db->prepare('DELETE FROM sbu_types WHERE slug = ?')->execute([$slug]);
    }
}
