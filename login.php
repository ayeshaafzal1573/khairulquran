<?php
require_once 'includes/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        // Check user credentials
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

      if ($user && $password === $user['password']){

            // Login successful
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect based on role
            switch ($user['role']) {
                case 'admin':
                    header('Location: admin/dashboard.php');
                    break;
                case 'teacher':
                    header('Location: teacher/dashboard.php');
                    break;
                case 'student':
                    header('Location: student/dashboard.php');
                    break;
                default:
                    header('Location: index.php');
            }
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Khairulkhan Academy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link href="assets/css/auth.css" rel="stylesheet"> -->
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg my-5">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <!-- <img src="assets/images/logo.png" alt="Logo" width="100"> -->
                            <h3 class="mt-3">Khairulkhan Academy</h3>
                            <p class="text-muted">Sign in to your account</p>
                        </div>

                        <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username or Email</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <a href="forgot-password.php">Forgot Password?</a>
                        </div>
                        <div class="text-center mt-2">
                            Don't have an account? <a href="register.php">Register</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div