<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

if (!isset($_GET['course_id']) || !is_numeric($_GET['course_id'])) {
    header('Location: manage_courses.php');
    exit;
}

$courseId = $_GET['course_id'];

// Verify course exists
$courseStmt = $db->prepare("SELECT * FROM courses WHERE course_id = ?");
$courseStmt->execute([$courseId]);
$course = $courseStmt->fetch();

if (!$course) {
    header('Location: manage_courses.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $sequence = (int)$_POST['sequence'];
    
    // Handle video upload
    $videoPath = null;
    if (!empty($_POST['video_url'])) {
        $videoPath = trim($_POST['video_url']);
    } elseif (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/course_videos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $extension = pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('video_') . '.' . $extension;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['video_file']['tmp_name'], $destination)) {
            $videoPath = 'uploads/course_videos/' . $filename;
        }
    }
    
    // Handle document upload
    $documentPath = null;
    if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/course_documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $extension = pathinfo($_FILES['document_file']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('doc_') . '.' . $extension;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['document_file']['tmp_name'], $destination)) {
            $documentPath = 'uploads/course_documents/' . $filename;
        }
    }

    // Insert content into database
    $stmt = $db->prepare("INSERT INTO course_content (course_id, title, description, video_url, document_url, sequence_number) 
                         VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$courseId, $title, $description, $videoPath, $documentPath, $sequence]);

    $_SESSION['message'] = "Content added successfully!";
    header("Location: manage_content.php?course_id=$courseId");
    exit;
}

// Get next sequence number
$sequenceStmt = $db->prepare("SELECT MAX(sequence_number) FROM course_content WHERE course_id = ?");
$sequenceStmt->execute([$courseId]);
$nextSequence = $sequenceStmt->fetchColumn() + 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
 <title>Add Course Content - Khair-ul-Quran Academy</title>
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
            <div >
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Add Content to: <?= htmlspecialchars($course['title']) ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title*</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="sequence" class="form-label">Sequence Number*</label>
                                <input type="number" class="form-control" id="sequence" name="sequence" 
                                       value="<?= $nextSequence ?>" min="1" required>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header">Video Content</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="video_url" class="form-label">YouTube/Vimeo URL</label>
                                        <input type="url" class="form-control" id="video_url" name="video_url" 
                                               placeholder="https://www.youtube.com/watch?v=...">
                                    </div>
                                    <div class="mb-3">
                                        <label for="video_file" class="form-label">Or Upload Video File</label>
                                        <input type="file" class="form-control" id="video_file" name="video_file" accept="video/*">
                                        <small class="text-muted">Max 50MB (MP4 recommended)</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header">Document/Notes</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="document_file" class="form-label">Upload Document</label>
                                        <input type="file" class="form-control" id="document_file" name="document_file" 
                                               accept=".pdf,.doc,.docx,.txt">
                                        <small class="text-muted">PDF, Word, or Text files</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="manage_content.php?course_id=<?= $courseId ?>" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Add Content</button>
                            </div>
                        </form>
                    </div>
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



