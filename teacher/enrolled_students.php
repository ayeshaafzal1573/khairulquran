<?php
session_start();
require '../includes/config.php';

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id'])) {
    header('Location: /khairulquran/login.php');
    exit;
}

if ($_SESSION['role'] !== 'teacher') {
    header('Location: /khairulquran/unauthorized.php');
    exit;
}

$teacher_id = $_SESSION['user_id'];
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

if ($course_id <= 0) {
    $_SESSION['error'] = "Invalid course ID";
    header('Location: my_courses.php');
    exit;
}

try {
    // Verify course ownership with prepared statement
    $stmt = $db->prepare("SELECT c.course_id, c.title 
                         FROM courses c
                         WHERE c.course_id = ? AND c.teacher_id = ?");
    if (!$stmt->execute([$course_id, $teacher_id])) {
        throw new Exception("Failed to verify course ownership");
    }
    
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$course) {
        $_SESSION['error'] = "Course not found or you don't have permission to view it";
        header('Location: my_courses.php');
        exit;
    }

    // Get teacher details
    $teacher_sql = "SELECT u.username, u.email, t.full_name, t.profile_image 
                   FROM users u
                   JOIN teachers t ON u.user_id = t.user_id
                   WHERE u.user_id = ?";
    $teacher_stmt = $db->prepare($teacher_sql);
    if (!$teacher_stmt->execute([$teacher_id])) {
        throw new Exception("Failed to fetch teacher details");
    }
    $teacher = $teacher_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$teacher) {
        throw new Exception("Teacher profile not found");
    }

    // Get enrolled students with additional details
    $sql = "SELECT 
               s.student_id,
               s.full_name AS name,
               s.contact_number,
               s.profile_image,
               e.enrollment_date,
               e.completion_status
            FROM enrollments e
            JOIN students s ON e.student_id = s.student_id
            WHERE e.course_id = ?
            ORDER BY e.enrollment_date DESC";
    $stmt = $db->prepare($sql);
    if (!$stmt->execute([$course_id])) {
        throw new Exception("Failed to fetch enrolled students");
    }
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['error'] = "A database error occurred. Please try again later.";
    header('Location: my_courses.php');
    exit;
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = $e->getMessage();
    header('Location: my_courses.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrolled Students - <?= htmlspecialchars($course['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .profile-img-sm {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .default-profile-sm {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .student-link {
            text-decoration: none;
            color: inherit;
            transition: color 0.2s;
        }
        .student-link:hover {
            color: #0d6efd;
        }
        .badge-status {
            min-width: 100px;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include './includes/sidebar.php'; ?>

    <main class="main-content">
          <?php include '../includes/loader.php'; ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 ms-sm-auto px-md-4">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                            <?= htmlspecialchars($_SESSION['error']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">
                            Students Enrolled in: <span class="text-primary"><?= htmlspecialchars($course['title']) ?></span>
                        </h1>
                        <div>
                            <span class="badge bg-secondary me-2">
                                <?= count($students) ?> student<?= count($students) !== 1 ? 's' : '' ?>
                            </span>
                            <a href="my_courses.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Courses
                            </a>
                        </div>
                    </div>

                    <?php if (empty($students)): ?>
                        <div class="alert alert-info">
                            No students are currently enrolled in this course.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student</th>
                                        <th>Contact</th>
                                        <th>Enrolled On</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($student['profile_image'])): ?>
                                                    <img src="<?= htmlspecialchars($student['profile_image']) ?>" 
                                                         class="profile-img-sm me-2" 
                                                         alt="<?= htmlspecialchars($student['name']) ?>"
                                                         onerror="this.onerror=null;this.src='../assets/default-profile.png';">
                                                <?php else: ?>
                                                    <div class="default-profile-sm me-2">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <a href="student_details.php?student_id=<?= $student['student_id'] ?>" 
                                                   class="student-link">
                                                    <?= htmlspecialchars($student['name']) ?>
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <?= $student['contact_number'] ? htmlspecialchars($student['contact_number']) : 'N/A' ?>
                                        </td>
                                        <td>
                                            <?= date('M j, Y', strtotime($student['enrollment_date'])) ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $statusClass = [
                                                'not_started' => 'bg-secondary',
                                                'in_progress' => 'bg-warning text-dark',
                                                'completed' => 'bg-success'
                                            ][$student['completion_status'] ?? 'not_started'];
                                            ?>
                                            <span class="badge rounded-pill <?= $statusClass ?> badge-status">
                                                <?= ucfirst(str_replace('_', ' ', $student['completion_status'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="message_student.php?student_id=<?= $student['student_id'] ?>" 
                                               class="btn btn-sm btn-outline-primary"
                                               data-bs-toggle="tooltip" 
                                               title="Message Student">
                                                <i class="fas fa-envelope"></i>
                                            </a>
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
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enable tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Error handling for images
            document.querySelectorAll('img').forEach(img => {
                img.onerror = function() {
                    this.src = '../assets/default-profile.png';
                };
            });
        });
    </script>
</body>
</html>