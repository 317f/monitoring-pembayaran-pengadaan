<?php
session_start();
require_once 'database.php';

// Base URL - adjust according to your setup
define('BASE_URL', '/');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check user role
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Administrator';
}

// Function to sanitize input
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Function to format currency
function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

// Function to format date
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

// Function to check due date and return appropriate class
function getDueDateClass($dueDate) {
    $today = strtotime('today');
    $due = strtotime($dueDate);
    $daysUntilDue = floor(($due - $today) / (60 * 60 * 24));
    
    if ($daysUntilDue < 0) {
        return 'text-danger';
    } elseif ($daysUntilDue <= 7) {
        return 'text-warning';
    }
    return 'text-success';
}
?>
