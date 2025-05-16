<?php
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$user_stmt = $db->prepare("SELECT full_name, email FROM students WHERE student_id = ?");
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch enrolled courses count
$enrolled_stmt = $db->prepare("SELECT COUNT(*) FROM enrollments WHERE student_id = ?");
$enrolled_stmt->execute([$user_id]);
$enrolled_count = $enrolled_stmt->fetchColumn();

// Fetch all available courses
$stmt = $db->query("SELECT c.course_id, c.title, c.description, c.thumbnail, t.full_name AS teacher_name
                    FROM courses c
                    JOIN teachers t ON c.teacher_id = t.teacher_id
                    WHERE c.status = 'published'
                    ORDER BY c.course_id DESC");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #4e73df 0%, #224abe 100%);
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 5px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .user-profile {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .user-profile img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.2);
        }
        .card {
            transition: transform 0.3s;
            height: 100%;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .card-img-top {
            height: 160px;
            object-fit: cover;
        }
        .stats-card {
            border-left: 4px solid #4e73df;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block sidebar collapse p-0">
            <div class="user-profile">
                <img src="../assets/default-profile.jpg" alt="Profile" class="mb-2">
                <h5><?= htmlspecialchars($user['full_name']) ?></h5>
                <small><?= htmlspecialchars($user['email']) ?></small>
            </div>
            <div class="sidebar-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my-courses.php">
                            <i class="bi bi-book"></i> My Courses <span class="badge bg-light text-dark ms-2"><?= $enrolled_count ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                            <i class="bi bi-person"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="messages.php">
                            <i class="bi bi-envelope"></i> Messages
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="progress.php">
                            <i class="bi bi-graph-up"></i> Progress
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php">
                            <i class="bi bi-gear"></i> Settings
                        </a>
                    </li>
                    <li class="nav-item mt-3">
                        <a class="nav-link text-danger" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-calendar"></i> This week
                    </button>
                </div>
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
                                <div class="icon-circle bg-primary text-white">
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
                                    <h3>3</h3>
                                </div>
                                <div class="icon-circle bg-warning text-white">
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
                                    <h3>5</h3>
                                </div>
                                <div class="icon-circle bg-success text-white">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="mb-3">Available Courses</h4>
            
            <?php if (empty($courses)): ?>
                <div class="alert alert-info">No courses available at the moment. Please check back later.</div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($courses as $course): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <?php if ($course['thumbnail']): ?>
                                    <img src="../uploads/thumbnails/<?= htmlspecialchars($course['thumbnail']) ?>" class="card-img-top" alt="<?= htmlspecialchars($course['title']) ?>">
                                <?php else: ?>
                                    <img src="../assets/default-course.jpg" class="card-img-top" alt="Default course image">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($course['title']) ?></h5>
                                    <p class="card-text text-muted">By <?= htmlspecialchars($course['teacher_name']) ?></p>
                                    <p class="card-text"><?= nl2br(htmlspecialchars(substr($course['description'], 0, 100))) ?>...</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="view_course.php?course_id=<?= $course['course_id'] ?>" class="btn btn-sm btn-primary">View Details</a>
                                        <small class="text-muted"><?= rand(10, 50) ?> students enrolled</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
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