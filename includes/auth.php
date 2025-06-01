<?php
require_once 'config.php';

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}

/**
 * Check if user is teacher
 */
function isTeacher() {
    return isLoggedIn() && $_SESSION['role'] === 'teacher';
}

/**
 * Check if user is student
 */
function isStudent() {
    return isLoggedIn() && $_SESSION['role'] === 'student';
}

/**
 * Redirect with alert if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'You must be logged in to access this page.'
        ];
        header('Location: /khairulquran/login.php');
        exit;
    }
}

/**
 * Redirect with alert if not admin
 */
function requireAdmin() {
    if (!isLoggedIn()) {
        $_SESSION['alert'] = [
            'type' => 'danger',
           'message' => 'Please log in to continue.'

        ];
        header('Location: /khairulquran/login.php');
        exit;
    }
    if (!isAdmin()) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'You do not have admin privileges to access this page.'
        ];
        header('Location: /khairulquran/index.php');
        exit;
    }
}

/**
 * Redirect with alert if not teacher
 */
function requireTeacher() {
    if (!isLoggedIn()) {
        $_SESSION['alert'] = [
            'type' => 'danger',
           'message' => 'Please log in to continue.'

        ];
        header('Location: /khairulquran/login.php');
        exit;
    }
    if (!isTeacher()) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'You do not have teacher privileges to access this page.'
        ];
        header('Location: /khairulquran/login.php');
        exit;
    }
}

/**
 * Redirect with alert if not student
 */
function requireStudent() {
    if (!isLoggedIn()) {
        $_SESSION['alert'] = [
            'type' => 'danger',
     'message' => 'Please log in to continue.'

        ];
        header('Location: /khairulquran/login.php');
        exit;
    }
    if (!isStudent()) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'You do not have student privileges to access this page.'
        ];
        header('Location: /khairulquran/login.php');
        exit;
    }
}

/**
 * Get current user data
 */
function getCurrentUser() {
    if (!isLoggedIn()) return null;

    global $db;
    try {
        $stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Error fetching user: " . $e->getMessage());
        return null;
    }
}

/**
 * Display and clear session alerts
 */
function displayAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        echo '<div class="alert alert-' . htmlspecialchars($alert['type']) . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($alert['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        unset($_SESSION['alert']); // Clear the alert after displaying
    }
}
?>
