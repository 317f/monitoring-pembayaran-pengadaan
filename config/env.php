<?php
/**
 * Environment Configuration
 * This file contains environment-specific settings
 * Copy this file to env.local.php for local development settings
 */

// Environment type: 'development' or 'production'
define('APP_ENV', 'production');

// Application settings
define('APP_NAME', 'Monitoring Pembayaran Pengadaan');
define('APP_URL', 'http://localhost:8000');
define('APP_VERSION', '1.0.0');

// Debug settings
define('APP_DEBUG', APP_ENV === 'development');
ini_set('display_errors', APP_ENV === 'development' ? 1 : 0);
error_reporting(APP_ENV === 'development' ? E_ALL : 0);

// Session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
if (APP_ENV === 'production') {
    ini_set('session.cookie_secure', 1);
}

// Upload settings
define('UPLOAD_MAX_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_FILE_TYPES', [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'image/jpeg',
    'image/png'
]);
define('UPLOAD_FILE_EXTENSIONS', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png']);

// Security settings
define('PASSWORD_MIN_LENGTH', 6);
define('PASSWORD_HASH_ALGO', PASSWORD_DEFAULT);
define('PASSWORD_HASH_OPTIONS', ['cost' => 12]);
define('SESSION_LIFETIME', 7200); // 2 hours
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 300); // 5 minutes

// Pagination settings
define('ITEMS_PER_PAGE', 10);

// Date settings
define('DATE_FORMAT', 'd/m/Y');
define('DATE_FORMAT_SQL', 'Y-m-d');
define('DATETIME_FORMAT', 'd/m/Y H:i:s');
define('DATETIME_FORMAT_SQL', 'Y-m-d H:i:s');

// Currency settings
define('CURRENCY_SYMBOL', 'Rp');
define('CURRENCY_DECIMAL_POINT', ',');
define('CURRENCY_THOUSANDS_SEP', '.');

// Email settings (for future use)
define('MAIL_HOST', '');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', '');
define('MAIL_PASSWORD', '');
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_FROM_ADDRESS', '');
define('MAIL_FROM_NAME', APP_NAME);

// Cache settings (for future use)
define('CACHE_DRIVER', 'file');
define('CACHE_PATH', __DIR__ . '/../storage/cache');
define('CACHE_LIFETIME', 3600); // 1 hour

// API settings (for future use)
define('API_DEBUG', APP_ENV === 'development');
define('API_TIMEOUT', 30);
define('API_RETRY_ATTEMPTS', 3);
define('API_RETRY_DELAY', 1000); // milliseconds

// Custom error pages
define('ERROR_PAGES', [
    '403' => '/views/errors/404.php',
    '404' => '/views/errors/404.php',
    '500' => '/views/errors/404.php'
]);

// Feature flags
define('FEATURES', [
    'dark_mode' => true,
    'file_upload' => true,
    'export_excel' => true,
    'print_report' => true,
    'due_date_reminder' => true,
    'user_management' => true
]);

// System requirements
define('REQUIREMENTS', [
    'php' => '7.4.0',
    'mysql' => '5.7.0',
    'extensions' => [
        'pdo',
        'pdo_mysql',
        'gd',
        'fileinfo'
    ]
]);

// Default user roles
define('USER_ROLES', [
    'Administrator',
    'Pengguna'
]);

// Payment status options
define('PAYMENT_STATUS', [
    'Dalam Proses',
    'Tertunda',
    'Selesai'
]);

// Procurement methods
define('PROCUREMENT_METHODS', [
    'Tender',
    'Pemilihan Langsung',
    'Penunjukan Langsung',
    'Pengadaan Langsung'
]);

/**
 * Load local environment settings if available
 * This allows developers to have their own settings without modifying this file
 */
if (file_exists(__DIR__ . '/env.local.php')) {
    require_once __DIR__ . '/env.local.php';
}
