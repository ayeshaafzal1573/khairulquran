<?php
require_once 'includes/config.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';
$showResetForm = false;

// STEP 1: Handle GET token
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $token = $_GET['token'];
    $stmt = $db->prepare("SELECT * FROM users WHERE reset_token = ? AND token_expiry > NOW() LIMIT 1");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        $showResetForm = true;
    } else {
        $error = 'Invalid or expired password reset link.';
    }
}

// STEP 2: Handle POST password update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'], $_POST['confirm_password'], $_POST['token'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $token = $_POST['token'];

    if ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
        $showResetForm = true;
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
        $showResetForm = true;
    } else {
        $stmt = $db->prepare("SELECT * FROM users WHERE reset_token = ? AND token_expiry > NOW() LIMIT 1");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $update = $db->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE user_id = ?");
            $update->execute([$hashedPassword, $user['user_id']]);

            // ✅ Redirect to login page with success message
            header("Location: login.php?reset=success");
            exit;
        } else {
            $error = 'Invalid or expired token.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Khair-ul-Quran Academy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('./assets/images/reset.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px 0 rgba(244, 98, 57, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .text-danger, .text-success {
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <img src="assets/images/logo.png" alt="Logo" class="logo img-fluid">
                        <h3 class="fw-bold mt-3">Reset Password</h3>
                    </div>

                    <?php if ($error): ?>
                        <div class="text-danger mb-3 text-center"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <?php if ($showResetForm): ?>
                        <form method="POST">
                            <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token']) ?>">

                            <label class="mt-2 mb-1">New Password:</label>
                            <input type="password" name="password" class="form-control" required>

                            <label class="mt-2 mb-1">Confirm Password:</label>
                            <input type="password" name="confirm_password" class="form-control" required>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-dark btn-lg">Update Password</button>
                            </div>
                        </form>
                    <?php elseif (!$error): ?>
                        <p class="text-success">Password has been reset. Redirecting to login...</p>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
