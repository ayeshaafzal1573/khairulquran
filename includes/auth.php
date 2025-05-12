<?php
require_once 'config.php';

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
 * Redirect if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Redirect if not admin
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ../index.php');
        exit;
    }
}

/**
 * Redirect if not teacher
 */
function requireTeacher() {
    requireLogin();
    if (!isTeacher()) {
        header('Location: ../index.php');
        exit;
    }
}

/**
 * Redirect if not student
 */
function requireStudent() {
    requireLogin();
    if (!isStudent()) {
        header('Location: ../index.php');
        exit;
    }
}

/**
 * Get current user data
 */
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    
    global $db;
    $stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}
?>