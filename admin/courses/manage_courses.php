<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

if (!isAdmin()) {
    header('Location: ../../login.php');
    exit;
}

// Handle course deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $courseId = $_GET['delete'];
    $stmt = $db->prepare("DELETE FROM courses WHERE course_id = ?");
    $stmt->execute([$courseId]);
    $_SESSION['message'] = "Course deleted successfully";
    header('Location: manage_courses.php');
    exit;
}

// Get all courses with teacher information
$courses = $db->query("SELECT c.*, t.full_name AS teacher_name 
                      FROM courses c 
                      LEFT JOIN teachers t ON c.teacher_id = t.teacher_id
                      ORDER BY c.created_at DESC")->fetchAll();
?>


    <div class="container-fluid">
  <div class="row">
    
    <nav class="col-md-2 d-none d-md-block p-0 bg-dark sidebar" id="sidebar">
      <?php include '../includes/sidebar.php'; ?>
    </nav>
        <main class="col-md-10 ms-sm-auto p-4">


                <!-- Success Message -->
                <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message']); endif; ?>

                <div class="card w-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Manage Courses</h5>
                        <a href="add_course.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Add New Course
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Course Title</th>
                                        <th>Teacher</th>
                                        <th>Duration</th>
                                        <th>Price</th>
                                        <th>Featured</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><?= $course['course_id'] ?></td>
                                        <td><?= htmlspecialchars($course['title']) ?></td>
                                        <td><?= $course['teacher_name'] ?? 'Not Assigned' ?></td>
                                        <td><?= $course['duration'] ?></td>
                                        <td><?= number_format($course['price'], 2) ?></td>
                                        <td>
                                            <?php if ($course['is_featured']): ?>
                                                <span class="badge bg-success">Yes</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
    <a href="manage_content.php?course_id=<?= $course['course_id'] ?>" class="btn btn-sm btn-info">
        <i class="bi bi-collection"></i>
    </a>
                                            <a href="edit_course.php?id=<?= $course['course_id'] ?>" class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="?delete=<?= $course['course_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this course?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                            <a href="../enrollments/manage_enrollments.php?course=<?= $course['course_id'] ?>" class="btn btn-sm btn-info">
                                                <i class="bi bi-people"></i>
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
            

  