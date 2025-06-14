<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables
$_SESSION = array();

// Set logout alert
$_SESSION['alert'] = [
    'type' => 'success',
    'message' => 'You have been logged out successfully.'
];

// Destroy the session
session_destroy();

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect to login page
header('Location: /khairulquran/login.php');
exit;
?>