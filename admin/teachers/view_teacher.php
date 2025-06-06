<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_teachers.php');
    exit;
}

$teacherId = $_GET['id'];

// Get teacher data with user account info
$stmt = $db->prepare("
    SELECT t.*, u.email, u.username, u.status, u.created_at as join_date
    FROM teachers t
    JOIN users u ON t.user_id = u.user_id
    WHERE t.teacher_id = ?
");
$stmt->execute([$teacherId]);
$teacher = $stmt->fetch();


if (!$teacher) {
    header('Location: manage_teachers.php');
    exit;
}

// Get courses taught by this teacher
$stmt = $db->prepare("
    SELECT c.course_id, c.title, c.duration, c.price, 
           COUNT(e.enrollment_id) as total_students
    FROM courses c
    LEFT JOIN enrollments e ON c.course_id = e.course_id
    WHERE c.teacher_id = ?
    GROUP BY c.course_id
    ORDER BY c.title
");
$stmt->execute([$teacherId]);
$courses = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>View Teacher - Khair-ul-Quran Academy</title>
   
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <style>
    .sidebar {
      min-height: 100vh;
  background:  #473d32;
      color: white;
      position: fixed;
      width: 230px;
      transition: all 0.3s;
      z-index: 1000;
      left: 0;
    }
    
    .sidebar .nav-link {
      color: rgba(255, 255, 255, 0.8);
      margin-bottom: 5px;
      border-radius: 5px;
      padding: 10px 15px;
      white-space: nowrap;
    }
    
    .sidebar .nav-link:hover, 
    .sidebar .nav-link.active {
      color: white;
      background-color: rgba(255, 255, 255, 0.1);
    }
    
    .sidebar .nav-link i {
      margin-right: 10px;
      width: 20px;
      text-align: center;
    }
    
    .btn {
      background-color: #f59e0b;
      border: none;
    }
    
    .sidebar-toggle-btn {
      background-color: #f59e0b;
      position: fixed;
      left: 10px;
      top: 10px;
      z-index: 1100;
      display: none;
    }
    
    .main-content {
      margin-left: 220px;
      padding: 10px;
            padding-bottom:60px;
      transition: all 0.3s;
    }
    
    /* Mobile navbar styles */
    .mobile-navbar {
      display: none;
       background:  #473d32;
      padding: 5px;
      position: fixed;
      bottom: 0;
      width: 100%;
      z-index: 1000;
    }
    
    .mobile-navbar .nav-link {
      color: rgba(255, 255, 255, 0.8);
      text-align: center;
      font-size: 12px;
    }
    
    .mobile-navbar .nav-link i {
      display: block;
      font-size: 20px;
      margin-bottom: 5px;
    }
    
    /* Responsive styles */
    @media (max-width: 768px) {
      .sidebar {
        left: -250px;
      }
      
      .sidebar.active {
        left: 0;
      }
      
      .main-content {
        margin-left: 0;
      }
      
      .sidebar-toggle-btn {
        display: none;
      }
      
      .mobile-navbar {
        display: flex;
        justify-content: space-around;
      }
    }
    
    @media (min-width: 769px) and (max-width: 992px) {
      .sidebar {
        width: 220px;
      }
      
      .main-content {
        margin-left: 220px;
      }
    }
  </style>
</head>
<body>

<!-- Sidebar Toggle Button -->
<button class="btn sidebar-toggle-btn" id="sidebarToggle">
  <i class="bi bi-list"></i>
</button>

<!-- Sidebar -->
<div id="sidebar" class="d-flex flex-column p-3 text-white sidebar">
  <!-- Sidebar Navigation -->
  <ul class="nav flex-column">  
    <li class="nav-item">
      <a class="nav-link" href="/khairulquran/admin/dashboard.php">
        <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/khairulquran/admin/courses/manage_courses.php">
        <i class="bi bi-book"></i> <span>Courses</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/khairulquran/admin/students/manage_students.php">
        <i class="bi bi-people"></i> <span>Students</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/khairulquran/admin/teachers/manage_teachers.php">
        <i class="bi bi-person-badge"></i> <span>Teachers</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/khairulquran/admin/enrollments/manage_enrollments.php">
        <i class="bi bi-clipboard-check"></i> <span>Enrollments</span>
      </a>
    </li>
  </ul>

  <!-- Logout Button -->
  <div class="mt-auto">
    <form action="/khairulquran/admin/logout.php" method="POST">
      <button type="submit" class="btn w-100 mt-3">
        <i class="fas fa-power-off"></i> <span>Logout</span>
      </button>
    </form>
  </div>
</div>

<!-- Mobile Navbar (shown on small screens) -->
<div class="mobile-navbar d-lg-none">
  <a class="nav-link" href="/khairulquran/admin/dashboard.php">
    <i class="bi bi-speedometer2"></i>
    <span>Dashboard</span>
  </a>
  <a class="nav-link" href="/khairulquran/admin/courses/manage_courses.php">
    <i class="bi bi-book"></i>
    <span>Courses</span>
  </a>
  <a class="nav-link" href="/khairulquran/admin/students/manage_students.php">
    <i class="bi bi-people"></i>
    <span>Students</span>
  </a>
  <a class="nav-link" href="/khairulquran/admin/teachers/manage_teachers.php">
    <i class="bi bi-person-badge"></i>
    <span>Teachers</span>
  </a>
</div>

<!-- Main Content -->
<main class="main-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
         <div class="row">
       
            <main>

    <?php displayAlert(); ?>
             <?php include '../includes/loader.php'; ?>
                <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message']); endif; ?>
       <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Teacher Details</h5>
                        <div>
                            <a href="edit_teacher.php?id=<?= $teacherId ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil me-1"></i> Edit
                            </a>
                            <a href="manage_teachers.php" class="btn btn-sm btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Account Information</h6>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Teacher ID</th>
                                        <td><?= $teacher['teacher_id'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Username</th>
                                        <td><?= htmlspecialchars($teacher['username']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><?= htmlspecialchars($teacher['email']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <span class="badge bg-<?= $teacher['status'] ? 'success' : 'danger' ?>">
                                                <?= $teacher['status'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Join Date</th>
                                        <td><?= date('M d, Y', strtotime($teacher['join_date'])) ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Professional Information</h6>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Full Name</th>
                                        <td><?= htmlspecialchars($teacher['full_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Specialization</th>
                                        <td><?= htmlspecialchars($teacher['specialization']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Contact Number</th>
                                        <td><?= htmlspecialchars($teacher['contact_number']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Qualifications</th>
                                        <td><?= htmlspecialchars($teacher['qualifications']) ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h6>Bio/Introduction</h6>
                            <div class="border p-3 rounded bg-light">
                                <?= nl2br(htmlspecialchars($teacher['bio'])) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Courses Taught</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($courses)): ?>
                        <div class="alert alert-info">No courses assigned yet.</div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Course ID</th>
                                        <th>Course Title</th>
                                        <th>Duration</th>
                                        <th>Price</th>
                                        <th>Students</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><?= $course['course_id'] ?></td>
                                        <td><?= htmlspecialchars($course['title']) ?></td>
                                        <td><?= htmlspecialchars($course['duration']) ?></td>
                                        <td><?= number_format($course['price'], 2) ?></td>
                                        <td><?= $course['total_students'] ?></td>
                                        <td>
                                            <a href="../courses/manage_content.php?course_id=<?= $course['course_id'] ?>" class="btn btn-sm btn-info">
                                                <i class="bi bi-collection"></i>
                                            </a>
                                            <a href="../courses/edit_course.php?id=<?= $course['course_id'] ?>" class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil"></i>
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
</main>
      </div>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
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
