<?php
session_start();
require '../includes/config.php'; 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: /khairulquran/login.php');
    exit;
}

$teacher_id = $_SESSION['user_id'];

// Get teacher details
$teacher_sql = "SELECT u.username, u.email, t.full_name, t.profile_image 
               FROM users u
               JOIN teachers t ON u.user_id = t.user_id
               WHERE u.user_id = ?";
$teacher_stmt = $db->prepare($teacher_sql);
$teacher_stmt->execute([$teacher_id]);
$teacher = $teacher_stmt->fetch(PDO::FETCH_ASSOC);

// Get courses with student count
$sql = "SELECT c.course_id, c.title, c.description, 
               COUNT(e.student_id) AS student_count
        FROM courses c
        LEFT JOIN enrollments e ON c.course_id = e.course_id
        WHERE c.teacher_id = ?
        GROUP BY c.course_id";
$stmt = $db->prepare($sql);
$stmt->execute([$teacher_id]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
</head>
<body>
        <?php include './includes/sidebar.php'; ?>

        <div class="main-content">
              <?php include './includes/loader.php'; ?>
<div class="container-fluid">
    <div class="row">
 <div class="col-12 ms-sm-auto ">
    

            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">My Courses</h1>
               
            </div>

            <?php if (empty($courses)): ?>
                <div class="alert alert-info">
                    You haven't created any courses yet.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Course Title</th>
                                <th>Description</th>
                                <th>Students</th>
                                <th>View Students</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($course['title']); ?></strong>
                                </td>
                                <td>
                                    <?= htmlspecialchars($course['description']); ?>
                                </td>
                                <td>
                                    <span class="badge badge-enrolled rounded-pill p-2">
                                        <?= $course['student_count']; ?> enrolled
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="enrolled_students.php?course_id=<?= $course['course_id']; ?>" 
                                           class="btn btn-sm btn-outline-primary"
                                           data-bs-toggle="tooltip" title="View Students">
                                            <i class="fas fa-users"></i>
                                        </a>
                                     
                                     
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
</div>

                            </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Enable tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
</body>
</html>