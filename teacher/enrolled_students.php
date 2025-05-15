<?php
session_start();
require '../includes/config.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$teacher_id = $_SESSION['user_id'];
$course_id = intval($_GET['course_id'] ?? 0);

// ✅ 1. Verify ownership using PDO correctly
$stmt = $db->prepare("SELECT course_id FROM courses WHERE course_id = ? AND teacher_id = ?");
$stmt->execute([$course_id, $teacher_id]);
if ($stmt->rowCount() === 0) {
    die('Unauthorized access');
}
$teacher_id = $_SESSION['user_id']; // or however you're getting it
$sql = "
SELECT 
    s.full_name AS name,
    e.enrollment_date
FROM enrollments e
JOIN students s ON e.student_id = s.student_id
JOIN courses c ON e.course_id = c.course_id
WHERE e.course_id = :course_id AND c.teacher_id = :teacher_id
";

$stmt = $db->prepare($sql);
$stmt->execute([
    'course_id' => $course_id,
    'teacher_id' => $teacher_id
]);
$students = $stmt->fetchAll();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Enrolled Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h1>Students Enrolled in Course ID: <?= $course_id ?></h1>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Student Name</th>
            <th>Enrolled At</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($students as $student): ?>
        <tr>
            <td><?= htmlspecialchars($student['name']); ?></td>
            <td><?= htmlspecialchars($student['enrollment_date']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="my_courses.php" class="btn btn-secondary mt-3">Back to Courses</a>

</body>
</html>
