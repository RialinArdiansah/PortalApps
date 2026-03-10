<?php
class AuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect('/');
        }
        $pageTitle = 'Login';
        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);
        require BASE_PATH . '/views/auth/login.php';
    }

    public function login(): void
    {
        verify_csrf();

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $_SESSION['login_error'] = 'Username dan password wajib diisi';
            redirect('/login');
        }

        $userModel = new UserModel();
        $user = $userModel->findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['login_error'] = 'Username atau password salah';
            redirect('/login');
        }

        Auth::login($user);
        redirect('/');
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('/login');
    }
}
