<?php
session_start();
require '../includes/config.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch student details joined with user email
$student_stmt = $db->prepare("SELECT s.*, u.email FROM students s 
                             JOIN users u ON s.user_id = u.user_id 
                             WHERE s.user_id = ?");
$student_stmt->execute([$user_id]);
$student = $student_stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    // Student not found — redirect or show error
    echo "Student details not found.";
    exit;
}

$student_id = $student['student_id']; // Correct student_id from students table

// Fetch enrolled courses for this student
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
$courses_stmt->execute([$student_id]);

$enrolled_courses = $courses_stmt->fetchAll(PDO::FETCH_ASSOC);

// Count enrolled courses for sidebar or stats
$enrolled_count = count($enrolled_courses);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Courses - Khair-ul-Quran Academy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <style>
        /* Optional: Payment status badges */
        .badge-paid {
            background-color: #198754;
            color: #fff;
        }
        .badge-pending {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-failed {
            background-color: #dc3545;
            color: #fff;
        }
        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 0.3em 0.6em;
            font-size: 0.75rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-transform: capitalize;
            opacity: 0.9;
        }
        .course-thumbnail {
            height: 180px;
            object-fit: cover;
        }
    </style>
</head>
<body>

    <?php include './includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <div class="row">

                <!-- Main Content -->
                <main class="col-12">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">My Courses</h1>
                    </div>

                    <?php if ($enrolled_count > 0): ?>
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                            <?php foreach ($enrolled_courses as $course): ?>
                                <?php 
                                    // Payment status badge color
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
                                                <img src="../assets/default-course.jpg" class="card-img-top course-thumbnail" alt="Default course image">
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
                                                <a href="view_course.php?course_id=<?= $course['course_id'] ?>" class="btn btn-success">
                                                    <i class="bi bi-arrow-right-circle"></i> Continue Learning
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="bi bi-info-circle-fill fs-1 me-3"></i>
                            <div>
                                <h4 class="alert-heading">No Enrolled Courses</h4>
                                <p>You haven't enrolled in any courses yet. Browse our Quranic courses to begin your learning journey.</p>
                                <hr>
                                <a href="available-courses.php" class="btn btn-primary">
                                    <i class="bi bi-book"></i> Browse Available Courses
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </main>

            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Highlight active sidebar link
    document.addEventListener('DOMContentLoaded', function() {
        const currentPage = location.pathname.split('/').pop();
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('href') === currentPage) {
                link.classList.add('active');
            }
        });
    });
</script>
</body>
</html>
