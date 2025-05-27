
<?php
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_stmt = $db->prepare("
    SELECT s.full_name, u.email 
    FROM students s
    JOIN users u ON s.student_id = u.user_id
    WHERE s.student_id = ?
");
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // fallback values
    $user = [
        'full_name' => 'Unknown',
        'email' => 'Not available'
    ];
}
$student_user_id = $_SESSION['user_id'];

$error = '';
$success = '';

// Fetch current student data with user information
$stmt = $db->prepare("SELECT s.student_id, s.full_name, s.address, s.contact_number, s.parent_name, 
                      s.parent_contact, s.previous_education, s.profile_image, u.email
                      FROM students s
                      JOIN users u ON s.user_id = u.user_id
                      WHERE s.user_id = ?");
$stmt->execute([$student_user_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student profile not found.");
}



// Fetch enrolled courses count
$enrolled_stmt = $db->prepare("SELECT COUNT(*) FROM enrollments WHERE student_id = ?");
$enrolled_stmt->execute([$user_id]);
$enrolled_count = $enrolled_stmt->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #4e73df 0%, #224abe 100%);
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 5px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .user-profile {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .user-profile img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.2);
        }
        .card {
            transition: transform 0.3s;
            height: 100%;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .card-img-top {
            height: 160px;
            object-fit: cover;
        }
        .stats-card {
            border-left: 4px solid #4e73df;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
     <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block sidebar collapse p-0">
            <div class="user-profile">
                <?php if ($student['profile_image']): ?>
                  <img src="../uploads/profiles/<?= htmlspecialchars($student['profile_image']) ?>" alt="Profile">
   <?php else: ?>
                    <img src="../assets/default-profile.jpg" alt="Profile">
                <?php endif; ?>
                <h5><?= htmlspecialchars($student['full_name']) ?></h5>
                <small><?= htmlspecialchars($student['email']) ?></small>
            </div>
            <div class="sidebar-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my-courses.php">
                            <i class="bi bi-book"></i> My Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="profile.php">
                            <i class="bi bi-person"></i> Profile
                        </a>
                    </li>
                    
                    <li class="nav-item mt-3">
                        <a class="nav-link text-danger" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
      
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-12 mb-3">
                    <div class="card stats-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted">Enrolled Courses</h6>
                                    <h3><?= $enrolled_count ?></h3>
                                </div>
                                <div class="icon-circle text-primary">
                                    <i class="bi bi-book"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <div class="card stats-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted">Courses in Progress</h6>
                                    <h3>3</h3>
                                </div>
                                <div class="icon-circle text-warning">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <div class="card stats-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted">Completed Courses</h6>
                                    <h3>5</h3>
                                </div>
                                <div class="icon-circle text-success">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

           
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Simple script to highlight active sidebar item
    document.addEventListener('DOMContentLoaded', function() {
        const currentPage = location.pathname.split('/').pop();
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentPage) {
                link.classList.add('active');
            }
        });
    });
</script>
</body>
</html>