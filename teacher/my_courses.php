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
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
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
        .badge-enrolled {
            background-color: #28a745;
            font-size: 0.9rem;
        }
        .table th {
            background-color: #f8f9fa;
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
                    <h5><?php echo htmlspecialchars($teacher['full_name'] ?? $teacher['username']); ?></h5>
                    <p class="text-white-50">Teacher</p>
                </div>
                
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="my_courses.php">
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
                <h1 class="h2">My Courses</h1>
               
            </div>

            <?php if (empty($courses)): ?>
                <div class="alert alert-info">
                    You haven't created any courses yet. <a href="create_course.php" class="alert-link">Create your first course</a>.
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