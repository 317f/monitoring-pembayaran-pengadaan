<?php
require_once 'init.php';

// Basic routing
$page = $_GET['page'] ?? 'login';
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

// Handle routing
try {
    switch ($page) {
        case 'login':
            $authController->login();
            break;

        case 'logout':
            $authController->logout();
            break;

        case 'register':
            $authController->register();
            break;

        case 'change_password':
            $authController->changePassword();
            break;

        case 'manage_users':
            $authController->manageUsers();
            break;

        case 'dashboard':
        case 'payments':
            // Require login for all payment routes
            if (!isLoggedIn()) {
                header('Location: index.php?page=login');
                exit;
            }

            switch ($action) {
                case 'index':
                    $paymentController->index();
                    break;

                case 'create':
                    $paymentController->create();
                    break;

                case 'edit':
                    if ($id) {
                        $paymentController->edit($id);
                    }
                    break;

                case 'delete':
                    if ($id) {
                        $paymentController->delete($id);
                    }
                    break;

                case 'view':
                    if ($id) {
                        $paymentController->view($id);
                    }
                    break;

                case 'export':
                    $paymentController->export();
                    break;

                case 'print':
                    $paymentController->print();
                    break;

                default:
                    throw new Exception('Action not found');
            }
            break;

        default:
            throw new Exception('Page not found');
    }
} catch (Exception $e) {
    // Log error and show error page
    error_log($e->getMessage());
    include 'views/errors/404.php';
}
?>
