<?php
require '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: /khairulquran/login.php');
    exit;
}

$teacher_id = $_SESSION['user_id'];

// Get teacher details from both users and teachers tables
$sql = "SELECT u.username, u.email, t.full_name, t.profile_image 
        FROM users u
        JOIN teachers t ON u.user_id = t.user_id
        WHERE u.user_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$teacher_id]);
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);

// Get total courses
$sql = "SELECT COUNT(*) AS total_courses FROM courses WHERE teacher_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$teacher_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$total_courses = $result['total_courses'];

// Get total students
$sql = "SELECT COUNT(DISTINCT e.student_id) AS total_students 
        FROM enrollments e
        JOIN courses c ON e.course_id = c.course_id
        WHERE c.teacher_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$teacher_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$total_students = $result['total_students'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include './includes/sidebar.php'; ?>
<div class="main-content">
      <?php include './includes/loader.php'; ?>
<div class="container-fluid">
    <div class="row">
     
        <main class="col-12 py-2">
        

            <div class="welcome-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3>Welcome back, <?php echo htmlspecialchars($teacher['full_name'] ?? $teacher['username']); ?>!</h3>
                        <p class="text-muted">Here's what's happening with your courses today.</p>
                    </div>
                    <div class="text-end">
                        <p class="mb-1"><small class="text-muted">Last login: <?php echo date("F j, Y, g:i a"); ?></small></p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card text-white bg-primary mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Total Courses</h5>
                                    <p class="card-text fs-3"><?php echo $total_courses; ?></p>
                                </div>
                                <i class="fas fa-book-open fa-3x opacity-50"></i>
                            </div>
                            <a href="my_courses.php" class="text-white stretched-link"></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-white bg-success mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Total Students</h5>
                                    <p class="card-text fs-3"><?php echo $total_students; ?></p>
                                </div>
                                <i class="fas fa-users fa-3x opacity-50"></i>
                            </div>
                            <a href="students.php" class="text-white stretched-link"></a>
                        </div>
                    </div>
                </div>
            </div>

       
        </main>
    </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>