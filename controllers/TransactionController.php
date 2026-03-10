<?php
class TransactionController
{
    public function index(): void
    {
        $model = new TransactionModel();
        $transactions = Auth::canViewAll() ? $model->all() : $model->findByUserId(Auth::id());
        view('transactions/index', compact('transactions'));
    }

    public function store(): void
    {
        verify_csrf();

        $date = $_POST['transaction_date'] ?? '';
        $name = trim($_POST['transaction_name'] ?? '');
        $cost = (int) ($_POST['cost'] ?? 0);
        $type = $_POST['transaction_type'] ?? '';
        $proof = trim($_POST['proof'] ?? '') ?: null;

        if (empty($date) || empty($name) || empty($type)) {
            flash('error', 'Semua field wajib diisi');
            redirect('/transactions');
        }

        if (!in_array($type, ['Keluar', 'Tabungan', 'Kas'])) {
            flash('error', 'Tipe transaksi tidak valid');
            redirect('/transactions');
        }

        (new TransactionModel())->create([
            'transaction_date' => $date,
            'transaction_name' => $name,
            'cost' => $cost,
            'transaction_type' => $type,
            'submitted_by_id' => Auth::id(),
            'proof' => $proof,
        ]);

        flash('success', 'Transaksi berhasil ditambahkan');
        redirect('/transactions');
    }

    public function update(string $id): void
    {
        verify_csrf();
        $model = new TransactionModel();
        $tx = $model->findById($id);

        if (!$tx) { flash('error', 'Transaksi tidak ditemukan'); redirect('/transactions'); }
        if (!Auth::canViewAll() && $tx['submitted_by_id'] !== Auth::id()) {
            flash('error', 'Akses ditolak'); redirect('/transactions');
        }

        $data = [];
        if (!empty($_POST['transaction_date'])) $data['transaction_date'] = $_POST['transaction_date'];
        if (!empty($_POST['transaction_name'])) $data['transaction_name'] = trim($_POST['transaction_name']);
        if (isset($_POST['cost'])) $data['cost'] = (int) $_POST['cost'];
        if (!empty($_POST['transaction_type'])) $data['transaction_type'] = $_POST['transaction_type'];
        if (array_key_exists('proof', $_POST)) $data['proof'] = trim($_POST['proof']) ?: null;

        if (!empty($data)) { $model->update($id, $data); }

        flash('success', 'Transaksi berhasil diperbarui');
        redirect('/transactions');
    }

    public function destroy(string $id): void
    {
        verify_csrf();
        $model = new TransactionModel();
        $tx = $model->findById($id);

        if (!$tx) { flash('error', 'Transaksi tidak ditemukan'); redirect('/transactions'); }
        if (!Auth::canViewAll() && $tx['submitted_by_id'] !== Auth::id()) {
            flash('error', 'Akses ditolak'); redirect('/transactions');
        }

        $model->delete($id);
        flash('success', 'Transaksi berhasil dihapus');
        redirect('/transactions');
    }
}
