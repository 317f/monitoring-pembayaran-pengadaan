<?php
/**
 * Test Script for Monitoring Pembayaran Pengadaan
 * Run this script to verify core functionality
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to run tests
function runTest($name, $callback) {
    echo "\nTesting $name... ";
    try {
        $result = $callback();
        if ($result === true) {
            echo "\033[32mPASSED\033[0m\n";
            return true;
        } else {
            echo "\033[31mFAILED\033[0m\n";
            if (is_string($result)) {
                echo "Error: $result\n";
            }
            return false;
        }
    } catch (Exception $e) {
        echo "\033[31mERROR\033[0m\n";
        echo "Exception: " . $e->getMessage() . "\n";
        return false;
    }
}

// Test database connection
function testDatabase() {
    require_once 'config/database.php';
    try {
        $pdo->query('SELECT 1');
        return true;
    } catch (PDOException $e) {
        return "Database connection failed: " . $e->getMessage();
    }
}

// Test directory permissions
function testDirectories() {
    $directories = [
        'public/uploads',
        'views',
        'models',
        'controllers',
        'config'
    ];

    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            return "Directory not found: $dir";
        }
        if (!is_writable($dir)) {
            return "Directory not writable: $dir";
        }
    }
    return true;
}

// Test file uploads
function testFileUploads() {
    $uploadDir = 'public/uploads';
    $testFile = $uploadDir . '/test.txt';
    
    if (!file_put_contents($testFile, 'test')) {
        return "Failed to create test file";
    }
    
    if (!file_exists($testFile)) {
        return "Test file not found";
    }
    
    unlink($testFile);
    return true;
}

// Test session handling
function testSessions() {
    session_start();
    $_SESSION['test'] = 'test_value';
    session_write_close();
    
    session_start();
    if ($_SESSION['test'] !== 'test_value') {
        return "Session test failed";
    }
    unset($_SESSION['test']);
    return true;
}

// Test user authentication
function testAuthentication() {
    require_once 'models/User.php';
    require_once 'config/database.php';
    
    $user = new User($pdo);
    $testUser = $user->getUserByUsername('admin');
    
    if (!$testUser) {
        return "Admin user not found";
    }
    
    if (!password_verify('admin123', $testUser['password'])) {
        return "Default admin password verification failed";
    }
    
    return true;
}

// Test payment model
function testPaymentModel() {
    require_once 'models/Payment.php';
    require_once 'config/database.php';
    
    $payment = new Payment($pdo);
    $stats = $payment->getPaymentStatistics();
    
    if (!is_array($stats)) {
        return "Failed to get payment statistics";
    }
    
    return true;
}

// Test required PHP extensions
function testPhpExtensions() {
    $required = ['pdo', 'pdo_mysql', 'gd', 'fileinfo'];
    $missing = [];
    
    foreach ($required as $ext) {
        if (!extension_loaded($ext)) {
            $missing[] = $ext;
        }
    }
    
    if (!empty($missing)) {
        return "Missing extensions: " . implode(', ', $missing);
    }
    
    return true;
}

// Test configuration files
function testConfiguration() {
    $required = [
        'config/database.php',
        'config/config.php',
        'config/env.php'
    ];
    
    foreach ($required as $file) {
        if (!file_exists($file)) {
            return "Missing configuration file: $file";
        }
    }
    
    return true;
}

// Run all tests
echo "\n=== Running System Tests ===\n";

$tests = [
    'Configuration Files' => 'testConfiguration',
    'PHP Extensions' => 'testPhpExtensions',
    'Database Connection' => 'testDatabase',
    'Directory Permissions' => 'testDirectories',
    'File Uploads' => 'testFileUploads',
    'Session Handling' => 'testSessions',
    'User Authentication' => 'testAuthentication',
    'Payment Model' => 'testPaymentModel'
];

$results = [
    'passed' => 0,
    'failed' => 0
];

foreach ($tests as $name => $test) {
    $success = runTest($name, $test);
    $success ? $results['passed']++ : $results['failed']++;
}

echo "\n=== Test Results ===\n";
echo "Tests Run: " . ($results['passed'] + $results['failed']) . "\n";
echo "\033[32mPassed: " . $results['passed'] . "\033[0m\n";
echo "\033[31mFailed: " . $results['failed'] . "\033[0m\n";

if ($results['failed'] > 0) {
    echo "\nPlease fix the failed tests before deploying to production.\n";
    exit(1);
} else {
    echo "\nAll tests passed! The application is ready for use.\n";
    exit(0);
}
