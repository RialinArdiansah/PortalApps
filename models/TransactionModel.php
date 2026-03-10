<?php
class TransactionModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(): array
    {
        return $this->db->query(
            'SELECT t.*, u.full_name as submitted_by_name
             FROM transactions t
             LEFT JOIN users u ON t.submitted_by_id = u.id
             ORDER BY t.transaction_date DESC, t.created_at DESC'
        )->fetchAll();
    }

    public function findByUserId(string $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT t.*, u.full_name as submitted_by_name
             FROM transactions t
             LEFT JOIN users u ON t.submitted_by_id = u.id
             WHERE t.submitted_by_id = ?
             ORDER BY t.transaction_date DESC, t.created_at DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function findById(string $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM transactions WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): string
    {
        $id = generate_uuid();
        $stmt = $this->db->prepare(
            'INSERT INTO transactions (id, transaction_date, transaction_name, cost, transaction_type, submitted_by_id, proof, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())'
        );
        $stmt->execute([
            $id,
            $data['transaction_date'],
            $data['transaction_name'],
            $data['cost'],
            $data['transaction_type'],
            $data['submitted_by_id'],
            $data['proof'] ?? null,
        ]);
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
        $this->db->prepare('UPDATE transactions SET ' . implode(', ', $fields) . ' WHERE id = ?')
                 ->execute($values);
    }

    public function delete(string $id): void
    {
        $this->db->prepare('DELETE FROM transactions WHERE id = ?')->execute([$id]);
    }

    // ── Aggregate methods for dashboard ──

    public function sumByType(string $type, ?string $userId = null): int
    {
        $sql = 'SELECT COALESCE(SUM(cost), 0) FROM transactions WHERE transaction_type = ?';
        if ($userId) {
            $stmt = $this->db->prepare($sql . ' AND submitted_by_id = ?');
            $stmt->execute([$type, $userId]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$type]);
        }
        return (int) $stmt->fetchColumn();
    }

    public function allRaw(?string $userId = null): array
    {
        if ($userId) {
            $stmt = $this->db->prepare('SELECT * FROM transactions WHERE submitted_by_id = ?');
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        }
        return $this->db->query('SELECT * FROM transactions')->fetchAll();
    }
}
