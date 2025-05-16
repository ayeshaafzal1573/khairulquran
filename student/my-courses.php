<?php
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}

$student_id = $_SESSION['user_id'];

// Fetch student details
$student_stmt = $db->prepare("SELECT s.*, u.email FROM students s 
                             JOIN users u ON s.user_id = u.user_id 
                             WHERE s.user_id = ?");
$student_stmt->execute([$student_id]);
$student = $student_stmt->fetch(PDO::FETCH_ASSOC);

// ✅ Use correct student_id for enrollments
$actual_student_id = $student['student_id'];

$courses_stmt = $db->prepare("
    SELECT 
        c.course_id, 
        c.title, 
        c.description, 
        c.image_url, 
        c.duration, 
        t.full_name AS teacher_name,
        COUNT(cc.content_id) AS total_lessons,
        e.enrollment_date, 
        e.completion_status, 
        e.payment_status
    FROM enrollments e
    JOIN courses c ON e.course_id = c.course_id
    JOIN teachers t ON c.teacher_id = t.teacher_id
    LEFT JOIN course_content cc ON cc.course_id = c.course_id
    WHERE e.student_id = ?
    GROUP BY c.course_id, t.full_name, e.enrollment_date, e.completion_status, e.payment_status
");
$courses_stmt->execute([$actual_student_id]);

$enrolled_courses = $courses_stmt->fetchAll(PDO::FETCH_ASSOC);




// Count enrolled courses for sidebar
$enrolled_count = count($enrolled_courses);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - Khair-ul-Quran Academy</title>
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
         .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-paid {
            background-color: #28a745;
            color: white;
        }
        .badge-pending {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-failed {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block sidebar collapse p-0">
         
            <div class="user-profile">
                <?php if ($student['profile_image']): ?>
                    <img src="../uploads/<?= htmlspecialchars($student['profile_image']) ?>" alt="Profile">
                <?php else: ?>
                    <img src="../assets/default-profile.jpg" alt="Profile">
                <?php endif; ?>
                <h5><?= htmlspecialchars($student['full_name']) ?></h5>
                <small><?= htmlspecialchars($student['email']) ?></small>
            </div>
            <div class="sidebar-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="my-courses.php">
                            <i class="bi bi-book"></i> My Courses 
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                            <i class="bi bi-person"></i> Profile
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
                <h1 class="h2">My Courses</h1>
               
            </div>

          

        <?php if (count($enrolled_courses) > 0): ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($enrolled_courses as $course): ?>
            <?php 
                // Payment status ke liye badge color set karna (optional)
                switch ($course['payment_status']) {
                    case 'paid': $status_class = 'badge-paid'; break;
                    case 'pending': $status_class = 'badge-pending'; break;
                    case 'failed': $status_class = 'badge-failed'; break;
                    default: $status_class = 'badge-pending';
                }
            ?>
            <div class="col">
                <div class="card course-card h-100">
                    <div class="position-relative">
                        <?php if ($course['image_url']): ?>
                         <img src="../<?= htmlspecialchars($course['image_url']) ?>" class="card-img-top course-thumbnail" alt="<?= htmlspecialchars($course['title']) ?>">
                          <?php else: ?>
                            <img src="../assets/default-course.jpg" 
                                 class="card-img-top course-thumbnail" alt="Default course image">
                        <?php endif; ?>
                        <span class="status-badge <?= $status_class ?>">
                            <?= ucfirst($course['payment_status']) ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($course['title']) ?></h5>
                        <p class="card-text text-muted">
                            <i class="bi bi-person"></i> <?= htmlspecialchars($course['teacher_name']) ?>
                        </p>
                        <p class="card-text"><?= htmlspecialchars(substr($course['description'], 0, 100)) ?>...</p>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <div class="d-grid gap-2">
                            <a href="view_course.php?course_id=<?= $course['course_id'] ?>" 
                               class="btn btn-success">
                                <i class="bi bi-arrow-right-circle"></i> Continue Learning
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>

                <div class="alert alert-info">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-info-circle-fill fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="alert-heading">No Enrolled Courses</h4>
                            <p>You haven't enrolled in any courses yet. Browse our Quranic courses to begin your learning journey.</p>
                            <hr>
                            <a href="available-courses.php" class="btn btn-primary">
                                <i class="bi bi-book"></i> Browse Available Courses
                            </a>
                        </div>
                    </div>
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