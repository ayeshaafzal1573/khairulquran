<?php
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$student_id = $_SESSION['user_id'];
$course_id = intval($_GET['course_id'] ?? 0);

// Fetch course details
$stmt = $db->prepare("SELECT *, t.full_name AS teacher_name
                      FROM courses c
                      JOIN teachers t ON c.teacher_id = t.teacher_id
                      WHERE c.course_id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    die("Course not found.");
}

// Fetch lessons for this course
$stmt = $db->prepare("SELECT * FROM course_content WHERE course_id = ? ORDER BY content_id ASC");

$stmt->execute([$course_id]);
$lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($course['title']) ?> - Course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h2><?= htmlspecialchars($course['title']) ?></h2>
<h5 class="text-muted">Instructor: <?= htmlspecialchars($course['teacher_name']) ?></h5>
<p><?= nl2br(htmlspecialchars($course['description'])) ?></p>

<hr>

<h4>Lessons</h4>
<?php if (count($lessons) > 0): ?>
    <ul class="list-group">
        <?php foreach ($lessons as $lesson): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                 <h5>Course Name:</h5>
                <?= htmlspecialchars($lesson['title']) ?>
                  </li>
                   <li class="list-group-item d-flex justify-content-between align-items-center">
                 <h5>Course Description:</h5>
                <?= htmlspecialchars($lesson['description']) ?>
                  </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No lessons added yet.</p>
<?php endif; ?>

<a href="student_dashboard.php" class="btn btn-secondary mt-4">Back to Dashboard</a>

</body>
</html>
