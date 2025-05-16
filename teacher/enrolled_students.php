<?php
session_start();
require '../includes/config.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: /khairulquran/login.php');
    exit;
}

$teacher_id = $_SESSION['user_id'];
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// Verify course ownership
$stmt = $db->prepare("SELECT c.course_id, c.title 
                     FROM courses c
                     WHERE c.course_id = ? AND c.teacher_id = ?");
$stmt->execute([$course_id, $teacher_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    header('Location: my_courses.php');
    exit;
}

// Get teacher details
$teacher_sql = "SELECT u.username, u.email, t.full_name, t.profile_image 
               FROM users u
               JOIN teachers t ON u.user_id = t.user_id
               WHERE u.user_id = ?";
$teacher_stmt = $db->prepare($teacher_sql);
$teacher_stmt->execute([$teacher_id]);
$teacher = $teacher_stmt->fetch(PDO::FETCH_ASSOC);

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
$stmt->execute([$course_id]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #3a7bd5, #00d2ff);
            color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 5px;
            border-radius: 5px;
            padding: 10px 15px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .main-content {
            padding: 20px;
        }
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
            color: #777;
        }
        .badge-status {
            font-size: 0.8rem;
            padding: 5px 8px;
        }
        .table th {
            background-color: #f8f9fa;
        }
        .student-link {
            color: inherit;
            text-decoration: none;
        }
        .student-link:hover {
            text-decoration: underline;
        }
             .profile-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .default-profile {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            color: #777;
            font-size: 24px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
            <div class="position-sticky pt-3">
                <div class="text-center mb-4">
                    <?php if (!empty($teacher['profile_image'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($teacher['profile_image']) ?>" alt="Profile" class="profile-img mb-2">
                    <?php else: ?>
                        <div class="default-profile">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                    <h5><?= htmlspecialchars($teacher['full_name'] ?? $teacher['username']) ?></h5>
                    <p class="text-white-50">Teacher</p>
                </div>
                
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_courses.php">
                            <i class="fas fa-book-open"></i> My Courses
                        </a>
                    </li>
                 
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                            <i class="fas fa-user-cog"></i> Profile Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../includes/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
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
                                                 alt="<?= htmlspecialchars($student['name']) ?>">
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
                              
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
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