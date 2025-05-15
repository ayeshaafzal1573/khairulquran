<?php
session_start();
require '../includes/config.php'; 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: /khairulquran/login.php');
    exit;
}

$teacher_id = $_SESSION['user_id'];
$teacher_name = $_SESSION['username'];

// Prepare statement
$sql = "SELECT course_id, title, description FROM courses WHERE teacher_id = ?";
$stmt = $db->prepare($sql);
if (!$stmt) {
    die("Prepare failed");
}

// Execute with parameter in array
$stmt->execute([$teacher_id]);

// Fetch all courses as associative array
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h1>My Courses</h1>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Course Title</th>
            <th>Description</th>
            <th>Manage Content</th>
            <th>Enrolled Students</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($courses as $course): ?>
        <tr>
            <td><?= htmlspecialchars($course['title']); ?></td>
            <td><?= htmlspecialchars($course['description']); ?></td>
            <td><a href="manage_content.php?course_id=<?= $course['course_id']; ?>" class="btn btn-sm btn-info">Manage Content</a></td>
            <td><a href="enrolled_students.php?course_id=<?= $course['course_id']; ?>" class="btn btn-sm btn-warning">View Students</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="teacher_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>

</body>
</html>
