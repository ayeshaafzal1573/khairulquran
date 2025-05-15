<?php
session_start();

require '../includes/config.php'; 
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$teacher_id = $_SESSION['user_id'];
$course_id = intval($_GET['course_id'] ?? 0);
// Verify teacher owns this course
$stmt = $db->prepare("SELECT course_id FROM courses WHERE course_id = ? AND teacher_id = ?");
$stmt->execute([$course_id, $teacher_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$course) {
    die('Unauthorized access');
}

$stmt = $db->prepare("SELECT * FROM course_content WHERE course_id = :course_id");
$stmt->execute(['course_id' => $course_id]);
$lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Content</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h1>Manage Content for Course ID: <?php echo $course_id; ?></h1>
<a href="add_lesson.php?course_id=<?php echo $course_id; ?>" class="btn btn-success mb-3">Add New Lesson</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Lesson Title</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
     <?php foreach ($lessons as $lesson): ?>
<tr>
    <td><?= htmlspecialchars($lesson['title']); ?></td>
    <td><?= htmlspecialchars($lesson['description']); ?></td>
    <td>
        <a href="edit_lesson.php?id=<?= $lesson['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
        <a href="delete_lesson.php?id=<?= $lesson['id']; ?>&course_id=<?= $course_id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
    </td>
</tr>
<?php endforeach; ?>
 
    </tbody>
</table>

<a href="my_courses.php" class="btn btn-secondary mt-3">Back to My Courses</a>

</body>
</html>
