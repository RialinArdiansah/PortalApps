<?php
class FeeP3smController
{
    public function index(): void
    {
        $fees = (new FeeP3smModel())->all();
        view('fee-p3sm/index', compact('fees'));
    }

    public function store(): void
    {
        verify_csrf();
        $cost = (int) ($_POST['cost'] ?? 0);
        $month = (int) ($_POST['month'] ?? 0);
        $year = (int) ($_POST['year'] ?? 0);

        if ($month < 1 || $month > 12 || $year < 2000) {
            flash('error', 'Bulan atau tahun tidak valid');
            redirect('/fee-p3sm');
        }

        (new FeeP3smModel())->create(['cost' => $cost, 'month' => $month, 'year' => $year]);
        flash('success', 'Fee P3SM berhasil ditambahkan');
        redirect('/fee-p3sm');
    }

    public function update(string $id): void
    {
        verify_csrf();
        $model = new FeeP3smModel();
        $fee = $model->findById($id);
        if (!$fee) { flash('error', 'Data tidak ditemukan'); redirect('/fee-p3sm'); }

        $data = [];
        if (isset($_POST['cost'])) $data['cost'] = (int) $_POST['cost'];
        if (isset($_POST['month'])) $data['month'] = (int) $_POST['month'];
        if (isset($_POST['year'])) $data['year'] = (int) $_POST['year'];

        if (!empty($data)) { $model->update($id, $data); }
        flash('success', 'Fee P3SM berhasil diperbarui');
        redirect('/fee-p3sm');
    }

    public function destroy(string $id): void
    {
        verify_csrf();
        (new FeeP3smModel())->delete($id);
        flash('success', 'Fee P3SM berhasil dihapus');
        redirect('/fee-p3sm');
    }
}
