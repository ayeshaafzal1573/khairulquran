<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

// Get all teachers for dropdown
$teachers = $db->query("SELECT teacher_id, full_name FROM teachers ORDER BY full_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $teacherId = $_POST['teacher_id'] ?: null;
    $duration = trim($_POST['duration']);
    $price = trim($_POST['price']);
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

    // Handle file upload
    $imagePath = null;
    if (isset($_FILES['course_image']) && $_FILES['course_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/course_images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $extension = pathinfo($_FILES['course_image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('course_') . '.' . $extension;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['course_image']['tmp_name'], $destination)) {
            $imagePath = 'uploads/course_images/' . $filename;
        }
    }

    // Insert course into database
    $stmt = $db->prepare("INSERT INTO courses (title, description, image_url, teacher_id, duration, price, is_featured) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $imagePath, $teacherId, $duration, $price, $isFeatured]);

    $_SESSION['message'] = "Course added successfully!";
    header('Location: manage_courses.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Course - Khair-ul-Quran Academy</title>

</head>
<body>
   <div class="container-fluid">
  <div class="row">
    
    <nav class="col-md-2 d-none d-md-block p-0 bg-dark sidebar" id="sidebar">
      <?php include '../includes/sidebar.php'; ?>
    </nav>
        <main class="col-md-10 ms-sm-auto p-4">

    <?php displayAlert(); ?>
            <div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Add New Course</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Course Title*</label>
                                        <input type="text" class="form-control" id="title" name="title" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description*</label>
                                        <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                                    </div>
                                      <div class="mb-3">
                                        <label for="course_image" class="form-label">Course Image</label>
                                        <input type="file" class="form-control" id="course_image" name="course_image" accept="image/*">
                                        <small class="text-muted">Recommended size: 800x450px</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                  
                                    <div class="mb-3">
                                        <label for="teacher_id" class="form-label">Instructor</label>
                                        <select class="form-select" id="teacher_id" name="teacher_id">
                                            <option value="">-- Select Teacher --</option>
                                            <?php foreach ($teachers as $teacher): ?>
                                            <option value="<?= $teacher['teacher_id'] ?>"><?= htmlspecialchars($teacher['full_name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="duration" class="form-label">Duration*</label>
                                        <input type="text" class="form-control" id="duration" name="duration" placeholder="e.g. 3 months" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price (PKR)*</label>
                                        <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured">
                                        <label class="form-check-label" for="is_featured">Featured Course</label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="manage_courses.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Add Course</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
                                            </main>
    </div>
    </div>
    </div>
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
    <script>
        // Simple form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const description = document.getElementById('description').value.trim();
            const duration = document.getElementById('duration').value.trim();
            const price = document.getElementById('price').value.trim();
            
            if (!title || !description || !duration || !price) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });
    </script>
</body>
</html>