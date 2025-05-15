<?php
require '../includes/config.php'; // This should set $db (or $conn?)

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: /khairulquran/login.php');
    exit;
}

$teacher_id = $_SESSION['user_id'];
$teacher_name = $_SESSION['username'];  

$sql = "SELECT COUNT(*) AS total_courses FROM courses WHERE teacher_id = ?";
$stmt = $db->prepare($sql);  // use $db, not $conn
$stmt->execute([$teacher_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$total_courses = $result['total_courses'];


$sql = "SELECT COUNT(DISTINCT e.student_id) AS total_students 
FROM enrollments e
JOIN courses c ON e.course_id = c.course_id
WHERE c.teacher_id = ?
";
$stmt = $db->prepare($sql);  // use $db here as well
$stmt->execute([$teacher_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$total_students = $result['total_students'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Teacher Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h1>Welcome, <?php echo htmlspecialchars($teacher_name); ?></h1>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Courses</h5>
                <p class="card-text fs-3"><?php echo $total_courses; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Students</h5>
                <p class="card-text fs-3"><?php echo $total_students; ?></p>
            </div>
        </div>
    </div>
</div>

<a href="my_courses.php" class="btn btn-primary mt-3">My Courses</a>
<a href="profile.php" class="btn btn-secondary mt-3">Profile Settings</a>

</body>
</html>
