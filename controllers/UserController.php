<?php
class UserController
{
    public function index(): void
    {
        $users = (new UserModel())->all();
        view('users/index', compact('users'));
    }

    public function store(): void
    {
        verify_csrf();
        $model = new UserModel();

        $fullName = trim($_POST['full_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'karyawan';

        if (empty($fullName) || empty($username) || empty($email) || empty($password)) {
            flash('error', 'Semua field wajib diisi');
            redirect('/users');
        }

        // Check unique username
        if ($model->findByUsername($username)) {
            flash('error', 'Username sudah digunakan');
            redirect('/users');
        }

        $model->create([
            'full_name' => $fullName,
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => $role,
        ]);

        flash('success', 'Pengguna berhasil ditambahkan');
        redirect('/users');
    }

    public function update(string $id): void
    {
        verify_csrf();
        $model = new UserModel();
        $user = $model->findById($id);
        if (!$user) { flash('error', 'Pengguna tidak ditemukan'); redirect('/users'); }

        $data = [];
        if (!empty($_POST['full_name'])) $data['full_name'] = trim($_POST['full_name']);
        if (!empty($_POST['username'])) $data['username'] = trim($_POST['username']);
        if (!empty($_POST['email'])) $data['email'] = trim($_POST['email']);
        if (!empty($_POST['password'])) $data['password'] = $_POST['password'];
        if (!empty($_POST['role'])) $data['role'] = $_POST['role'];

        if (!empty($data)) {
            $model->update($id, $data);
        }

        flash('success', 'Pengguna berhasil diperbarui');
        redirect('/users');
    }

    public function destroy(string $id): void
    {
        verify_csrf();
        (new UserModel())->delete($id);
        flash('success', 'Pengguna berhasil dihapus');
        redirect('/users');
    }
}
