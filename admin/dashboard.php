<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Check admin authentication
if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$totalStudents = $db->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalTeachers = $db->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
$totalCourses = $db->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$recentEnrollments = $db->query("SELECT e.enrollment_id, s.full_name, c.title 
                                FROM enrollments e
                                JOIN students s ON e.student_id = s.student_id
                                JOIN courses c ON e.course_id = c.course_id
                                ORDER BY e.enrollment_date DESC LIMIT 5")->fetchAll();
?>

   <div class="container-fluid">
  <div class="row">
    
    <nav class="col-md-2 d-none d-md-block p-0 bg-dark sidebar" id="sidebar">
      <?php include 'includes/sidebar.php'; ?>
    </nav>
        <main class="col-md-10 col-12 ms-sm-auto px-4">

                <div class="row py-4">
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
       