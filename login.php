    <?php
    // Prevent caching
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    // Start output buffering
    ob_start();

    // Include configuration and authentication files
    require_once 'includes/config.php';
    require_once 'includes/auth.php';

    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Redirect if already logged in
    if (isLoggedIn()) {
        switch ($_SESSION['role']) {
            case 'admin':
                header('Location: /khairulquran/admin/dashboard.php');
                exit;
            case 'teacher':
                header('Location: /khairulquran/teacher/dashboard.php');
                exit;
            case 'student':
                header('Location: /khairulquran/index.php');
                exit;
            default:
                header('Location: /khairulquran/index.php');
                exit;
        }
    }

    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            $error = 'Please enter both username and password';
        } else {
            try {
                $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
                $stmt->execute([$username, $username]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    // Regenerate session ID for security
                    session_regenerate_id(true);

                    // Set session variables
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];

                    // Redirect based on role
                    switch ($user['role']) {
                        case 'admin':
                            header('Location: /khairulquran/admin/dashboard.php');
                            exit;
                        case 'teacher':
                            header('Location: /khairulquran/teacher/dashboard.php');
                            exit;
                        case 'student':
                            header('Location: /khairulquran/index.php');
                            exit;
                        default:
                            header('Location: /khairulquran/index.php');
                            exit;
                    }
                } else {
                    $error = 'Invalid username or password';
                }
            } catch (Exception $e) {
                $error = 'An error occurred. Please try again later.';
            }
        }
    }

    // Flush output buffer
    ob_end_flush();
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Khair-ul-Quran Academy</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <script>
            // Prevent back button from showing login page if logged in
            window.onload = function () {
                fetch('/khairulquran/check_session.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.loggedIn) {
                            let redirectUrl = '';
                            switch (data.role) {
                                case 'admin':
                                    redirectUrl = '/khairulquran/admin/dashboard.php';
                                    break;
                                case 'teacher':
                                    redirectUrl = '/khairulquran/teacher/dashboard.php';
                                    break;
                                case 'student':
                                default:
                                    redirectUrl = '/khairulquran/index.php';
                                    break;
                            }
                            window.history.replaceState(null, null, redirectUrl);
                            // Redirect immediately to avoid showing login page
                            window.location.href = redirectUrl;
                        }
                    })
                    .catch(error => {
                        console.error('Error checking session:', error);
                    });
            };
        </script>
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
            .btn-login {
                border-radius: 10px;
                background-color: #473d32;
                font-weight: bold;
                color: white;
            }
            a {
                text-decoration: none;
                color: #ff9800;
            }
            .forgot-password {
                display: inline-block;
                color: white;
                font-weight: 500;
                margin-left: 5px;
                transition: color 0.3s ease;
                text-align: left;
            }
            .forgot-password:hover {
                color: #ffc107;
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div class="container h-100 d-flex align-items-center justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card glass-card shadow-lg p-4">
                    <div class="text-center mb-4">
                        <h3 class="mt-3 text-white">Khair-ul-Quran Academy</h3>
                        <p class="text-light">Sign in to your account</p>
                    </div>

                    <?php
                    // Only show session-based alerts if not logged in
                    if (!isLoggedIn()) {
                        displayAlert();
                    }
                    ?>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
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
                        <div class="text-end mb-2">
                            <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
                        </div>
                        <div class="d-grid gap-2">
                         <button type="submit" class="btn btn-login" id="loginBtn">
    <span id="btnText">Login</span>
    <span class="spinner-border spinner-border-sm ms-2 d-none" id="btnSpinner" role="status" aria-hidden="true"></span>
</button>

                        </div>
                    </form>

                    <div class="text-center mt-2">
                        <span class="text-white">Don't have an account?</span> <a href="register.php">Register</a>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html><script>
    document.querySelector("form").addEventListener("submit", function () {
        const btn = document.getElementById("loginBtn");
        const text = document.getElementById("btnText");
        const spinner = document.getElementById("btnSpinner");

        btn.disabled = true;
        text.textContent = "Logging in...";
        spinner.classList.remove("d-none");
    });
</script>
