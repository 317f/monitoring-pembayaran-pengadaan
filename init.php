<?php
/**
 * Application Initialization Script
 * This file contains common functions and configurations used throughout the application
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set default timezone
date_default_timezone_set('Asia/Jakarta');

// Error reporting - set to 0 in production
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base path constants
define('BASE_PATH', __DIR__);
define('CONFIG_PATH', BASE_PATH . '/config');
define('CONTROLLER_PATH', BASE_PATH . '/controllers');
define('MODEL_PATH', BASE_PATH . '/models');
define('VIEW_PATH', BASE_PATH . '/views');
define('UPLOAD_PATH', BASE_PATH . '/public/uploads');

// Include database configuration
require_once CONFIG_PATH . '/database.php';
require_once CONFIG_PATH . '/config.php';

/**
 * Custom error handler
 */
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $error = date('Y-m-d H:i:s') . " - Error [$errno]: $errstr in $errfile on line $errline\n";
    error_log($error, 3, BASE_PATH . '/error.log');
    
    if (ini_get('display_errors')) {
        printf("<pre>%s</pre>", htmlspecialchars($error));
    }
    
    return true;
}
set_error_handler('customErrorHandler');

/**
 * Custom exception handler
 */
function customExceptionHandler($exception) {
    $error = date('Y-m-d H:i:s') . " - Exception: " . $exception->getMessage() . 
            " in " . $exception->getFile() . " on line " . $exception->getLine() . "\n";
    error_log($error, 3, BASE_PATH . '/error.log');
    
    if (ini_get('display_errors')) {
        printf("<pre>%s</pre>", htmlspecialchars($error));
    } else {
        include VIEW_PATH . '/errors/404.php';
    }
}
set_exception_handler('customExceptionHandler');

/**
 * Security Functions
 */

// Clean user input
function cleanInput($input) {
    if (is_array($input)) {
        return array_map('cleanInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Generate CSRF token
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * File Functions
 */

// Get allowed file extensions
function getAllowedFileExtensions() {
    return ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];
}

// Validate file upload
function validateFileUpload($file) {
    $allowedExtensions = getAllowedFileExtensions();
    $maxFileSize = 10 * 1024 * 1024; // 10MB
    
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($fileExtension, $allowedExtensions)) {
        return ['success' => false, 'message' => 'File type not allowed'];
    }
    
    if ($file['size'] > $maxFileSize) {
        return ['success' => false, 'message' => 'File size exceeds limit (10MB)'];
    }
    
    return ['success' => true];
}

/**
 * Date Functions
 */

// Format date to Indonesian format
function formatDateIndo($date) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $split = explode('-', $date);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

// Calculate days difference between two dates
function calculateDaysDiff($date1, $date2) {
    $datetime1 = new DateTime($date1);
    $datetime2 = new DateTime($date2);
    $interval = $datetime1->diff($datetime2);
    return $interval->days;
}

/**
 * Currency Functions
 */

// Format currency to Indonesian Rupiah
function formatRupiah($nominal) {
    return 'Rp ' . number_format($nominal, 0, ',', '.');
}

// Convert currency string to number
function currencyToNumber($currency) {
    return (float) str_replace(['Rp ', '.', ','], '', $currency);
}

/**
 * Notification Functions
 */

// Set flash message
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Get flash message
function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Navigation Functions
 */

// Get current page
function getCurrentPage() {
    return $_GET['page'] ?? 'login';
}

// Check if current page is active
function isActivePage($page) {
    return getCurrentPage() === $page ? 'active' : '';
}

/**
 * Load controllers
 */
require_once CONTROLLER_PATH . '/AuthController.php';
require_once CONTROLLER_PATH . '/PaymentController.php';

// Initialize controllers
$authController = new AuthController($pdo);
$paymentController = new PaymentController($pdo);
?>
