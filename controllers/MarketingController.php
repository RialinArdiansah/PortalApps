<?php
class MarketingController
{
    public function index(): void
    {
        $marketingNames = (new MarketingNameModel())->all();
        view('marketing/index', compact('marketingNames'));
    }

    public function store(): void
    {
        verify_csrf();
        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            flash('error', 'Nama marketing wajib diisi');
            redirect('/marketing');
        }
        (new MarketingNameModel())->create(['name' => $name]);
        flash('success', 'Nama marketing berhasil ditambahkan');
        redirect('/marketing');
    }

    public function update(string $id): void
    {
        verify_csrf();
        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            flash('error', 'Nama marketing wajib diisi');
            redirect('/marketing');
        }
        (new MarketingNameModel())->update($id, ['name' => $name]);
        flash('success', 'Nama marketing berhasil diperbarui');
        redirect('/marketing');
    }

    public function destroy(string $id): void
    {
        verify_csrf();
        (new MarketingNameModel())->delete($id);
        flash('success', 'Nama marketing berhasil dihapus');
        redirect('/marketing');
    }

    // AJAX endpoint for submission form dropdown
    public function listJson(): void
    {
        $names = (new MarketingNameModel())->all();
        json_response(['success' => true, 'data' => $names]);
    }
}
