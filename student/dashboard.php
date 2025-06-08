<?php
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// First check if user exists in users table
$user_check = $db->prepare("SELECT user_id, email, role FROM users WHERE user_id = ?");
$user_check->execute([$user_id]);
$user = $user_check->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User account not found.");
}

// Then fetch student details
$stmt = $db->prepare("SELECT student_id, full_name, address, contact_number, 
                      parent_name, parent_contact, previous_education, profile_image
                      FROM students WHERE user_id = ?");
$stmt->execute([$user_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    // If student record doesn't exist, create basic info from user table
    $student = [
        'full_name' => 'Student',
        'profile_image' => null,
        'email' => $user['email']
    ];
} else {
    // Add email from users table to student data
    $student['email'] = $user['email'];
}

// Fetch enrolled courses count
$enrolled_stmt = $db->prepare("SELECT COUNT(*) FROM enrollments WHERE student_id = ?");
$enrolled_stmt->execute([$student['student_id'] ?? 0]);
$enrolled_count = $enrolled_stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
 
</head>
<body>
    <?php include './includes/sidebar.php'; ?>
    <div class="main-content">
        
<div class="container-fluid">
    <div class="row">
    
        
        <!-- Main Content -->
        <main class="col-12 ms-sm-auto">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card stats-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted">Enrolled Courses</h6>
                                    <h3><?= $enrolled_count ?></h3>
                                </div>
                                <div class="icon-circle text-primary">
                                    <i class="bi bi-book"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card stats-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted">Courses in Progress</h6>
                                    <h3>0</h3>
                                </div>
                                <div class="icon-circle text-warning">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card stats-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted">Completed Courses</h6>
                                    <h3>0</h3>
                                </div>
                                <div class="icon-circle text-success">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Courses Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">My Courses</h5>
                </div>
                <div class="card-body">
                    <?php if ($enrolled_count > 0): ?>
                        <p>You are enrolled in <?= $enrolled_count ?> course(s).</p>
                        <a href="my-courses.php" class="btn btn-primary">View All Courses</a>
                    <?php else: ?>
                        <div class="alert alert-info">
                            You are not enrolled in any courses yet.
                        </div>
                        <a href="../courses.php" class="btn btn-primary">Browse Courses</a>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>


    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Simple script to highlight active sidebar item
    document.addEventListener('DOMContentLoaded', function() {
        const currentPage = location.pathname.split('/').pop();
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentPage) {
                link.classList.add('active');
            }
        });
    });
</script>
</body>
</html>