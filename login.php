<?php
require_once 'includes/config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

      if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    // Check for redirect after login
    $redirect = $_POST['redirect'] ?? '';

    if ($user['role'] === 'student' && $redirect === 'enroll_now') {
        // Redirect back to enroll page (adjust URL as needed)
        header('Location: enroll_now.php');
    } else {
        // Normal role-based redirects
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
    <title>Login - Khair-ul-Quran Academy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('./assets/images/login.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .glass-card h3 {
            font-weight: bold;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.8);
            border: none;
            border-radius: 10px;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .btn-login{
   border-radius: 10px;
            background-color: #473d32;
            font-weight: bold;
            color:white;
        }

      a{
    text-decoration:none;
color:#ff9800;

}
    </style>
</head>
<body>
    <div class="container h-100 d-flex align-items-center justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card glass-card shadow-lg p-4">
                <div class="text-center mb-4">
                    <!-- <img src="assets/images/logo.png" alt="Logo" width="100"> -->
                    <h3 class="mt-3 text-white">Khair-ul-Quran Academy</h3>
                    <p class="text-light">Sign in to your account</p>
                </div>

                <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label text-white">Username or Email</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label text-white">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-login">Login</button>
                    </div>
                </form>

                <div class="text-center mt-2">
                    <span class="text-white">Don't have an account?</span> <a href="register.php">Register</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
