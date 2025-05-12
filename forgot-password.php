<?php
require_once 'includes/config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $error = 'Please enter your email address';
    } else {
        // Check if email exists
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Generate reset token (valid for 1 hour)
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Store token in database
            $updateStmt = $db->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE user_id = ?");
            $updateStmt->execute([$token, $expiry, $user['user_id']]);

            // Send reset email (in production)
            $resetLink = SITE_URL . "/reset-password.php?token=$token";
            
            // For development, just show the link
            $message = "Password reset link: <a href='$resetLink'>$resetLink</a>";
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
    <title>Forgot Password - Khairulkhan Academy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg my-5">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h3>Forgot Password</h3>
                            <p class="text-muted">Enter your email to reset your password</p>
                        </div>

                        <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <?php if ($message): ?>
                        <div class="alert alert-info"><?= $message ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Reset Password</button>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <a href="login.php">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>