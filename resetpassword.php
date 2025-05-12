<?php
require_once 'includes/config.php';

$error = '';
$success = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Check if token exists and is valid
    $stmt = $db->prepare("SELECT * FROM users WHERE reset_token = ? AND token_expiry > NOW() LIMIT 1");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = 'Invalid or expired token';
    }
} else {
    $error = 'No token provided';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all fields';
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Khairulkhan Academy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg my-5">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h3>Reset Password</h3>
                        </div>

                        <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                        <div class="text-center">
                            <a href="forgot-password.php" class="btn btn-link">Request new reset link</a>
                        </div>
                        <?php elseif ($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                        <?php else: ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Update Password</button>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>