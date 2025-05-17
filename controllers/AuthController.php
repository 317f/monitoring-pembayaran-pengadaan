<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }

    public function login() {
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = sanitize($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $error = 'Username dan password harus diisi';
            } else {
                if ($this->userModel->login($username, $password)) {
                    header('Location: index.php?page=dashboard');
                    exit;
                } else {
                    $error = 'Username atau password salah';
                }
            }
        }

        // If already logged in, redirect to dashboard
        if (isLoggedIn()) {
            header('Location: index.php?page=dashboard');
            exit;
        }

        // Include login view
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function register() {
        // Only administrators can register new users
        if (!isAdmin()) {
            header('Location: index.php?page=login');
            exit;
        }

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = sanitize($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $role = sanitize($_POST['role'] ?? 'Pengguna');

            // Validate input
            if (empty($username) || empty($password)) {
                $error = 'Semua field harus diisi';
            } elseif ($password !== $confirmPassword) {
                $error = 'Password tidak cocok';
            } elseif ($this->userModel->usernameExists($username)) {
                $error = 'Username sudah digunakan';
            } else {
                // Attempt to register the user
                if ($this->userModel->register($username, $password, $role)) {
                    $success = 'User berhasil didaftarkan';
                } else {
                    $error = 'Gagal mendaftarkan user';
                }
            }
        }

        // Include register view
        require_once __DIR__ . '/../views/auth/register.php';
    }

    public function logout() {
        // Destroy session and redirect to login
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    }

    public function changePassword() {
        if (!isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                $error = 'Semua field harus diisi';
            } elseif ($newPassword !== $confirmPassword) {
                $error = 'Password baru tidak cocok';
            } else {
                // Verify current password and update to new password
                $user = $this->userModel->getUserById($_SESSION['user_id']);
                if ($user && password_verify($currentPassword, $user['password'])) {
                    if ($this->userModel->updateUser($_SESSION['user_id'], ['password' => $newPassword])) {
                        $success = 'Password berhasil diubah';
                    } else {
                        $error = 'Gagal mengubah password';
                    }
                } else {
                    $error = 'Password saat ini salah';
                }
            }
        }

        // Include change password view
        require_once __DIR__ . '/../views/auth/change_password.php';
    }

    public function manageUsers() {
        if (!isAdmin()) {
            header('Location: index.php?page=dashboard');
            exit;
        }

        $users = $this->userModel->getAllUsers();
        require_once __DIR__ . '/../views/auth/manage_users.php';
    }

    public function deleteUser() {
        if (!isAdmin()) {
            header('Location: index.php?page=dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
            $userId = (int)$_POST['user_id'];
            
            // Prevent deleting self
            if ($userId === $_SESSION['user_id']) {
                $_SESSION['error'] = 'Tidak dapat menghapus akun sendiri';
            } else {
                if ($this->userModel->deleteUser($userId)) {
                    $_SESSION['success'] = 'User berhasil dihapus';
                } else {
                    $_SESSION['error'] = 'Gagal menghapus user';
                }
            }
        }

        header('Location: index.php?page=manage_users');
        exit;
    }
}
