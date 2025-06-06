<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_teachers.php');
    exit;
}

$teacherId = $_GET['id'];

// Get teacher data
$stmt = $db->prepare("
    SELECT t.*, u.email, u.username, u.status 
    FROM teachers t
    JOIN users u ON t.user_id = u.user_id
    WHERE t.teacher_id = ?
");
$stmt->execute([$teacherId]);
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$teacher) {
    header('Location: manage_teachers.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $specialization = trim($_POST['specialization']);
    $qualifications = trim($_POST['qualifications']);
    $bio = trim($_POST['bio']);
    $contact = trim($_POST['contact_number']);
    $status = isset($_POST['status']) ? 1 : 0;
    $currentImage = $teacher['profile_image']; // Get current image path

    // Handle file upload if new image is provided
    if (isset($_FILES['teacher_image']) && $_FILES['teacher_image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['teacher_image']['tmp_name'];
        $imageName = basename($_FILES['teacher_image']['name']);
        $imageExt = pathinfo($imageName, PATHINFO_EXTENSION);
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array(strtolower($imageExt), $allowedExts)) {
            $newFileName = uniqid('teacher_') . '.' . $imageExt;
            $uploadDir = '../../uploads/teachers/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $destPath = $uploadDir . $newFileName;
            if (move_uploaded_file($imageTmpPath, $destPath)) {
                // Delete old image if it exists
                if ($currentImage && file_exists('../../uploads/teachers/' . $currentImage)) {
                    unlink('../../uploads/teachers/' . $currentImage);
                }
                $currentImage = $newFileName;
            } else {
                $errors[] = 'Image upload failed.';
            }
        } else {
            $errors[] = 'Invalid image format. Only JPG, PNG, GIF allowed.';
        }
    }

    // Validate inputs
    $errors = [];
    if (empty($fullName)) $errors[] = 'Full name is required';
    if (empty($username)) $errors[] = 'Username is required';
    if (empty($email)) $errors[] = 'Email is required';
    if (empty($specialization)) $errors[] = 'Specialization is required';

    if (empty($errors)) {
        try {
            $db->beginTransaction();

            // Update user account
            $userStmt = $db->prepare("UPDATE users SET username = ?, email = ?, status = ? WHERE user_id = ?");
            $userStmt->execute([$username, $email, $status, $teacher['user_id']]);

            // Update password if provided
            if (!empty($_POST['password'])) {
                $hashedPassword = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
                $db->prepare("UPDATE users SET password = ? WHERE user_id = ?")->execute([$hashedPassword, $teacher['user_id']]);
            }

            // Update teacher profile with image
            $teacherStmt = $db->prepare("
                UPDATE teachers SET 
                full_name = ?, 
                specialization = ?, 
                qualifications = ?, 
                bio = ?, 
                contact_number = ?,
                profile_image = ?
                WHERE teacher_id = ?
            ");
            $teacherStmt->execute([
                $fullName, 
                $specialization, 
                $qualifications, 
                $bio, 
                $contact,
                $currentImage,
                $teacherId
            ]);

            $db->commit();
            $_SESSION['message'] = "Teacher updated successfully!";
            header('Location: manage_teachers.php');
            exit;
        } catch (PDOException $e) {
            $db->rollBack();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Teacher - Khair-ul-Quran Academy</title>
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

            <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Edit Teacher: <?= htmlspecialchars($teacher['full_name']) ?></h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-3">Account Information</h6>
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username*</label>
                                        <input type="text" class="form-control" id="username" name="username" 
                                               value="<?= htmlspecialchars($teacher['username']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email*</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?= htmlspecialchars($teacher['email']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="password" name="password">
                                        <small class="text-muted">Leave blank to keep current password</small>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="status" name="status" 
                                            <?= $teacher['status'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="status">Active Account</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h6 class="mb-3">Professional Information</h6>
                                    <div class="mb-3">
                                        <label for="full_name" class="form-label">Full Name*</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" 
                                               value="<?= htmlspecialchars($teacher['full_name']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="specialization" class="form-label">Specialization*</label>
                                        <input type="text" class="form-control" id="specialization" name="specialization" 
                                               value="<?= htmlspecialchars($teacher['specialization']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="contact_number" class="form-label">Contact Number</label>
                                        <input type="text" class="form-control" id="contact_number" name="contact_number" 
                                               value="<?= htmlspecialchars($teacher['contact_number']) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <?php if ($teacher['profile_image']): ?>
                                        <div class="mb-2">
                                            <img src="../../uploads/teachers/<?= htmlspecialchars($teacher['profile_image']) ?>" 
                                                 alt="Current Teacher Image" style="max-height: 100px;">
                                            <p class="text-muted">Current Image</p>
                                        </div>
                                        <?php endif; ?>
                                        <label for="teacher_image" class="form-label">Change Image</label>
                                        <input type="file" class="form-control" id="teacher_image" name="teacher_image" accept="image/*">
                                        <small class="text-muted">Recommended size: 800x450px</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="qualifications" class="form-label">Qualifications</label>
                                        <textarea class="form-control" id="qualifications" name="qualifications" rows="2"><?= htmlspecialchars($teacher['qualifications']) ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="bio" class="form-label">Bio/Introduction</label>
                                        <textarea class="form-control" id="bio" name="bio" rows="3"><?= htmlspecialchars($teacher['bio']) ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="manage_teachers.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Teacher</button>
                            </div>
                        </form>
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



