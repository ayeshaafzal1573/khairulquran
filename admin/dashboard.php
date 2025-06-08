<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';


requireAdmin();
$totalStudents = $db->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalTeachers = $db->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
$totalCourses = $db->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$recentEnrollments = $db->query("SELECT e.enrollment_id, s.full_name, c.title 
                                FROM enrollments e
                                JOIN students s ON e.student_id = s.student_id
                                JOIN courses c ON e.course_id = c.course_id
                                ORDER BY e.enrollment_date DESC LIMIT 5")->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Sidebar</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

</head>
<body>
<?php include './includes/sidebar.php'; ?>

<main class="main-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <h2 class="mt-2">Welcome Admin</h2>
        <br>
        <?php displayAlert(); ?>
          <?php include './includes/loader.php'; ?>  
         <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Students</h6>
                                    <h2 class="mb-0"><?= $totalStudents ?></h2>
                                </div>
                                <i class="bi bi-people-fill fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Teachers</h6>
                                    <h2 class="mb-0"><?= $totalTeachers ?></h2>
                                </div>
                                <i class="bi bi-person-badge-fill fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Courses</h6>
                                    <h2 class="mb-0"><?= $totalCourses ?></h2>
                                </div>
                                <i class="bi bi-book-half fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
  <!-- Recent Enrollments Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Enrollments</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Student Name</th>
                                    <th>Course</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentEnrollments as $enrollment): ?>
                                    <tr>
                                        <td><?= $enrollment['enrollment_id'] ?></td>
                                        <td><?= htmlspecialchars($enrollment['full_name']) ?></td>
                                        <td><?= htmlspecialchars($enrollment['title']) ?></td>
                                        <td>
                                            <a href="enrollments/view_enrollment.php?id=<?= $enrollment['enrollment_id'] ?>" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
      </div>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
  // Toggle sidebar on button click
  document.getElementById('sidebarToggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('active');
  });
  
  // Close sidebar when clicking outside on mobile
  document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    
    if (window.innerWidth <= 768 && 
        !sidebar.contains(event.target) && 
        event.target !== sidebarToggle && 
        !sidebarToggle.contains(event.target)) {
      sidebar.classList.remove('active');
    }
  });
  window.onload = function () {
    fetch('/khairulquran/check_session.php')
        .then(response => response.json())
        .then(data => {
            if (!data.loggedIn || data.role !== 'admin') { 
                window.location.href = '/khairulquran/login.php';
            }
        });
};
</script>

</body>
</html>