<?php
require_once 'includes/config.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$error = '';
$success = '';

// Handle email submission for password reset link
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = 'Please enter your email address';
    } else {
        // Check if email exists
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Generate token and expiry (1 hour from now)
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store token in database
            $updateStmt = $db->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE user_id = ?");
            $updateStmt->execute([$token, $expiry, $user['user_id']]);
            
            // Send email with reset link
            $resetLink = "" . $_SERVER['HTTP_HOST'] . "/resetpassword.php?token=$token";
            
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
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $mail->Body    = "
                    <html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; line-height: 1.6; }
                            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                            .button {
                                display: inline-block;
                                padding: 10px 20px;
                                background-color: #007bff;
                                color: #ffffff;
                                text-decoration: none;
                                border-radius: 5px;
                                margin: 15px 0;
                            }
                            .footer { margin-top: 20px; font-size: 12px; color: #666; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <h2>Password Reset Request</h2>
                            <p>Hello,</p>
                            <p>You have requested to reset your password for Khair-ul-Quran Academy.</p>
                            <p>Please click the button below to reset your password:</p>
                            <a href='$resetLink' class='button'>Reset Password</a>
                            <p>Or copy and paste this link into your browser:<br>
                            <code>$resetLink</code></p>
                            <p>This link will expire in 1 hour.</p>
                            <p>If you didn't request this password reset, please ignore this email.</p>
                            <div class='footer'>
                                <p>Thank you,<br>Khair-ul-Quran Academy Team</p>
                            </div>
                        </div>
                    </body>
                    </html>
                ";

                // Plain text version for non-HTML email clients
                $mail->AltBody = "Password Reset Request\n\n"
                                . "Hello,\n\n"
                                . "You have requested to reset your password for Khair-ul-Quran Academy.\n\n"
                                . "Please visit the following link to reset your password:\n"
                                . "$resetLink\n\n"
                                . "This link will expire in 1 hour.\n\n"
                                . "If you didn't request this password reset, please ignore this email.\n\n"
                                . "Thank you,\n"
                                . "Khair-ul-Quran Academy Team";

                $mail->send();
                $success = 'Password reset link has been sent to your email. Please check your inbox (and spam folder if not found).';
            } catch (Exception $e) {
                error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
                $error = 'Failed to send reset email. Please try again later.';
            }
        } else {
            $error = 'No account found with that email address';
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
    <div class="container py-5">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <img src="assets/images/logo.png" alt="Logo" class="logo img-fluid">
                            <h3 class="fw-bold mt-3">Reset Password</h3>
                            <p class="text-muted">Enter your new password below</p>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                            <?php if (!isset($_GET['token'])): ?>
                                <div class="text-center mt-3">
                                    <a href="forgot-password.php" class="btn btn-outline-primary">Request New Reset Link</a>
                                </div>
                            <?php endif; ?>
                        <?php elseif ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php elseif (isset($_GET['token'])): ?>
                            <form method="POST" id="resetForm">
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required minlength="8" placeholder="Enter new password">
                                    <div class="form-text">Minimum 8 characters</div>
                                </div>
                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-dark btn-lg">Update Password</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <form method="POST" id="emailForm">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email address">
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-dark btn-lg">Send Reset Link</button>
                                </div>
                            </form>
                            <div class="text-center mt-3">
                                <a href="login.php" class="text-decoration-none">Remember your password? Login</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Client-side password validation
        if (document.getElementById('resetForm')) {
            document.getElementById('resetForm').addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;

                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match!');
                }
            });
        }
    </script>
</body>
</html>