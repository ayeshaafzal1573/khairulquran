<?php
// Include configuration and authentication utilities
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set JSON content type
header('Content-Type: application/json');

// Return session status and role
echo json_encode([
    'loggedIn' => isLoggedIn(),
    'role' => isset($_SESSION['role']) ? $_SESSION['role'] : null
]);
?>