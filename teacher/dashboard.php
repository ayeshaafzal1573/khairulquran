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
        .welcome-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
                        <a class="nav-link active" href="dashboard.php">
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
                <h1 class="h2">Dashboard Overview</h1>
            </div>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>