<?php
require_once 'includes/config.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$error = '';
$success = '';

// Password Reset Token Validation
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $db->prepare("SELECT * FROM users WHERE reset_token = ? AND token_expiry > NOW() LIMIT 1");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = 'Invalid or expired token. Please request a new reset link.';
    }
} else {
    $error = 'No token provided';
}

// Password Reset Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all fields';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Update password and clear reset token
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updateStmt = $db->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE user_id = ?");
        $updateStmt->execute([$hashedPassword, $user['user_id']]);

        $success = 'Password updated successfully. You can now <a href="login.php">login</a> with your new password.';
    }
}

// PHPMailer Configuration
if(isset($_GET['email'])){
    // echo "HELLO WORLD";
    sendResetEmail();
}
function sendResetEmail()
{
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'khairulquranonline@gmail.com';
        $mail->Password   = 'ahrd rnej zmcc xsqv';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('no-reply@khairulquran.com', 'Khair-ul-Quran Academy');
        $mail->addAddress("arbaxkhax8@gmail.com");

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = "Please click the following link to reset your password:<br><br>
                         ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
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
            background-color: #f8f9fa;
        }

        .card {
            border-radius: 15px;
        }

        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <form ><input type="submit" name="email" value="check mail"></form>
    <!-- <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <img src="assets/images/logo.png" alt="Logo" class="logo">
                            <h3 class="fw-bold">Reset Password</h3>
                            <p class="text-muted">Enter your new password below</p>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                            <div class="text-center mt-3">
                                <a href="forgot-password.php" class="btn btn-outline-primary">Request New Reset Link</a>
                            </div>
                        <?php elseif ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php else: ?>
                            <form method="POST" id="resetForm">
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required minlength="8">
                                    <div class="form-text">Minimum 8 characters</div>
                                </div>
                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">Update Password</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Client-side password validation
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
            }
        });
    </script>
</body>

</html>