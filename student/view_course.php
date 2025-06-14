<?php
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}
$student_user_id = $_SESSION['user_id'];

$student_stmt = $db->prepare("SELECT s.*, u.email FROM students s 
                             JOIN users u ON s.user_id = u.user_id 
                             WHERE s.user_id = ?");
$student_stmt->execute([$student_user_id]);
$student = $student_stmt->fetch(PDO::FETCH_ASSOC);
$student_id = $student['student_id'];

if (!isset($_GET['course_id'])) {
    header("Location: my-courses.php");
    exit;
}

$course_id = $_GET['course_id'];

$enrollment_stmt = $db->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?");
$enrollment_stmt->execute([$student_id, $course_id]);
$enrollment = $enrollment_stmt->fetch(PDO::FETCH_ASSOC);

if (!$enrollment) {
    header("Location: my-courses.php");
    exit;
}

$course_stmt = $db->prepare("
    SELECT c.*, t.full_name AS teacher_name
    FROM courses c
    JOIN teachers t ON c.teacher_id = t.teacher_id
    WHERE c.course_id = ?
");
$course_stmt->execute([$course_id]);
$course = $course_stmt->fetch(PDO::FETCH_ASSOC);

$lessons_stmt = $db->prepare("
    SELECT * FROM course_content
    WHERE course_id = ?
    ORDER BY content_id ASC
");
$lessons_stmt->execute([$course_id]);
$lessons = $lessons_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Course - Khair-ul-Quran Academy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
   
</head>
<body>

    <?php include './includes/sidebar.php'; ?>
    <div class="main-content">

    
    <div class="container-fluid">
        <div class="row">
        

            <main class="col-12 py-4">
       <!-- Enhanced Course Info Card -->
<div class="card border-0 shadow-lg mb-5 rounded-4 overflow-hidden">
    <div class="position-relative">
        <?php if ($course['image_url']): ?>
            <img src="../<?= htmlspecialchars($course['image_url']) ?>" class="w-100" style="height: 300px; object-fit: cover;" alt="Course Image">
        <?php else: ?>
            <img src="../assets/default-course.jpg" class="w-100" style="height: 300px; object-fit: cover;" alt="Default Course">
        <?php endif; ?>
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-25"></div>
    </div>
    <div class="card-body p-4">
        <h2 class="card-title fw-bold mb-3"><?= htmlspecialchars($course['title']) ?></h2>

        <div class="d-flex flex-wrap gap-3 mb-3">
            <span class="badge bg-primary p-2 px-3 rounded-pill">
                <i class="bi bi-person-fill me-1"></i>
                <?= htmlspecialchars($course['teacher_name']) ?>
            </span>
            <span class="badge bg-secondary p-2 px-3 rounded-pill">
                <i class="bi bi-clock me-1"></i>
                <?= htmlspecialchars($course['duration']) ?>
            </span>
        </div>

        <hr>
        <p class="text-muted" style="line-height: 1.7;"><?= nl2br(htmlspecialchars($course['description'])) ?></p>
    </div>
</div>


                <!-- Lessons Section -->
                <h4 class="mb-3"><i class="bi bi-list-ul"></i> Lessons</h4>
                <?php if (count($lessons) > 0): ?>
                    <div class="list-group mb-5">
                        <?php foreach ($lessons as $lesson): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1"><i class="bi bi-play-circle text-success me-2"></i> <?= htmlspecialchars($lesson['title']) ?></h6>
                                    <small><?= htmlspecialchars($lesson['description']) ?></small>
                                </div>
                                <?php if ($lesson['video_url']): ?>
                                    <a href="<?= htmlspecialchars($lesson['video_url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-play-btn-fill"></i> Watch
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-info-circle"></i> No lessons have been added to this course yet.
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
