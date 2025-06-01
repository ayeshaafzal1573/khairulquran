<?php
// Database Configuration
define('DB_HOST', 'localhost');
//------------------------------------- LOCAL --------------------------------------
define('DB_USER', 'root');
define('DB_PASS', '');
//------------------------------------- PRODUCTION --------------------------------------
//define('DB_USER', 'khaiwxmh_khairulq');
//define('DB_PASS', 'Q2WUfXXUN%CX');
define('DB_NAME', 'khaiwxmh_khairulquran');

// Site Configuration
define('SITE_NAME', 'Khair-ul-Quran Academy');
define('SITE_URL', 'https://khairulquran.com');

// Start session ONLY if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Set session cookie parameters before starting session
    session_set_cookie_params([
        'lifetime' => 86400, // 1 day
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict'
    ]);

    session_start(); // Start the session
}

// Disable caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

try {
    // Create database connection using constants
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>