<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_courses.php');
    exit;
}

$courseId = $_GET['id'];
$teachers = $db->query("SELECT teacher_id, full_name FROM teachers ORDER BY full_name")->fetchAll();

// Get course data
$stmt = $db->prepare("SELECT * FROM courses WHERE course_id = ?");
$stmt->execute([$courseId]);
$course = $stmt->fetch();

if (!$course) {
    header('Location: manage_courses.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $teacherId = $_POST['teacher_id'] ?: null;
    $duration = trim($_POST['duration']);
    $price = trim($_POST['price']);
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $currentImage = $course['image_url'];

    // Handle file upload if new image is provided
    if (isset($_FILES['course_image']) && $_FILES['course_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/course_images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $extension = pathinfo($_FILES['course_image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('course_') . '.' . $extension;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['course_image']['tmp_name'], $destination)) {
            // Delete old image if it exists
            if ($currentImage && file_exists('../../' . $currentImage)) {
                unlink('../../' . $currentImage);
            }
            $currentImage = 'uploads/course_images/' . $filename;
        }
    }

    // Update course in database
    $stmt = $db->prepare("UPDATE courses SET title = ?, description = ?, image_url = ?, teacher_id = ?, 
                         duration = ?, price = ?, is_featured = ? WHERE course_id = ?");
    $stmt->execute([$title, $description, $currentImage, $teacherId, $duration, $price, $isFeatured, $courseId]);

    $_SESSION['message'] = "Course updated successfully!";
    header('Location: manage_courses.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
       <title>Edit Course - Khair-ul-Quran Academy</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

</head>
<body>


<?php include '../includes/sidebar.php'; ?>
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
                        <h5 class="card-title">Edit Course</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Course Title*</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                               value="<?= htmlspecialchars($course['title']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description*</label>
                                        <textarea class="form-control" id="description" name="description" 
                                                  rows="5" required><?= htmlspecialchars($course['description']) ?></textarea>
                                    </div>
                                      <div class="mb-3">
                                        <label class="form-label">Current Image</label>
                                        <?php if ($course['image_url']): ?>
                                        <img src="../../<?= $course['image_url'] ?>" class="img-thumbnail mb-2" style="max-height: 150px;">
                                        <?php else: ?>
                                        <p class="text-muted">No image uploaded</p>
                                        <?php endif; ?>
                                        <label for="course_image" class="form-label">Change Image</label>
                                        <input type="file" class="form-control" id="course_image" name="course_image" accept="image/*">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                  
                                    <div class="mb-3">
                                        <label for="teacher_id" class="form-label">Instructor</label>
                                        <select class="form-select" id="teacher_id" name="teacher_id">
                                            <option value="">-- Select Teacher --</option>
                                            <?php foreach ($teachers as $teacher): ?>
                                            <option value="<?= $teacher['teacher_id'] ?>" 
                                                <?= $teacher['teacher_id'] == $course['teacher_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($teacher['full_name']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="duration" class="form-label">Duration*</label>
                                        <input type="text" class="form-control" id="duration" name="duration" 
                                               value="<?= htmlspecialchars($course['duration']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price (PKR)*</label>
                                        <input type="number" class="form-control" id="price" name="price" 
                                               value="<?= htmlspecialchars($course['price']) ?>" min="0" step="0.01" required>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" 
                                            <?= $course['is_featured'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_featured">Featured Course</label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="manage_courses.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Course</button>
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
    <script>
        // Disable video URL field if video file is selected and vice versa
        document.getElementById('video_file').addEventListener('change', function() {
            if (this.files.length > 0) {
                document.getElementById('video_url').value = '';
                document.getElementById('video_url').disabled = true;
            } else {
                document.getElementById('video_url').disabled = false;
            }
        });
        
        document.getElementById('video_url').addEventListener('input', function() {
            if (this.value.trim() !== '') {
                document.getElementById('video_file').value = '';
                document.getElementById('video_file').disabled = true;
            } else {
                document.getElementById('video_file').disabled = false;
            }
        });
        
        // Initialize based on current state
        if (document.getElementById('video_url').value.trim() !== '') {
            document.getElementById('video_file').disabled = true;
        }
    </script>
</body>
</html>
