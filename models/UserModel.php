<?php
class UserModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(): array
    {
        return $this->db->query('SELECT id, full_name, username, email, role, created_at, updated_at FROM users ORDER BY created_at')->fetchAll();
    }

    public function findById(string $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): string
    {
        $id = generate_uuid();
        $stmt = $this->db->prepare(
            'INSERT INTO users (id, full_name, username, email, password, role, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())'
        );
        $stmt->execute([
            $id,
            $data['full_name'],
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['role'],
        ]);
        return $id;
    }

    public function update(string $id, array $data): void
    {
        $fields = [];
        $values = [];
        foreach ($data as $key => $val) {
            if ($key === 'password') {
                $fields[] = 'password = ?';
                $values[] = password_hash($val, PASSWORD_BCRYPT);
            } else {
                $fields[] = "{$key} = ?";
                $values[] = $val;
            }
        }
        $fields[] = 'updated_at = NOW()';
        $values[] = $id;

        $this->db->prepare('UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?')
                 ->execute($values);
    }

    public function delete(string $id): void
    {
        $this->db->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
    }
}
