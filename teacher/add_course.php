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
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate input
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $duration = intval($_POST['duration'] ?? 0);
        $price = floatval($_POST['price'] ?? 0);
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;

        if (empty($title)) {
            throw new Exception("Course title is required");
        }

        if (strlen($title) > 100) {
            throw new Exception("Title must be less than 100 characters");
        }

        if (empty($description)) {
            throw new Exception("Description is required");
        }

        if ($duration <= 0) {
            throw new Exception("Duration must be a positive number");
        }

        if ($price < 0) {
            throw new Exception("Price cannot be negative");
        }

        // Handle file upload
        $thumbnail_path = null;
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = mime_content_type($_FILES['thumbnail']['tmp_name']);
            
            if (!in_array($file_type, $allowed_types)) {
                throw new Exception("Only JPG, PNG, and GIF images are allowed");
            }

            if ($_FILES['thumbnail']['size'] > 2 * 1024 * 1024) {
                throw new Exception("Image size must be less than 2MB");
            }

            $upload_dir = 'uploads/course_images/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file_ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
            $filename = 'course_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $file_ext;
            $thumbnail_path = $upload_dir . $filename;

            if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnail_path)) {
                throw new Exception("Failed to upload thumbnail");
            }
        }

        // Insert into database
        $stmt = $db->prepare("INSERT INTO courses 
                            (teacher_id, title, description, duration, price, image_url, is_featured, created_at)
                            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");

        $stmt->execute([
            $teacher_id,
            $title,
            $description,
            $duration,
            $price,
            $thumbnail_path,
            $is_featured
        ]);

        $course_id = $db->lastInsertId();
        $success = "Course created successfully!";
        
        // Redirect to course management page
        header("Location: my_courses.php");
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get teacher details for header
try {
    $stmt = $db->prepare("SELECT full_name FROM teachers WHERE user_id = ?");
    $stmt->execute([$teacher_id]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching teacher details";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .thumbnail-preview {
            max-width: 200px;
            max-height: 200px;
            display: none;
            margin-top: 10px;
        }
       
       
    </style>
</head>
<body>
    <?php include './includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="container-fluid p-3">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h3 mb-0">Add New Course</h1>
                        <a href="my_courses.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Courses
                        </a>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" class="form-section">
                        <div class="mb-3">
                            <label for="title" class="form-label">Course Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required 
                                   value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="5" required><?= 
                                htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="duration" class="form-label">Duration (weeks) *</label>
                                <input type="number" class="form-control" id="duration" name="duration" min="1" required 
                                       value="<?= htmlspecialchars($_POST['duration'] ?? '4') ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Price (USD)</label>
                                <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" 
                                       value="<?= htmlspecialchars($_POST['price'] ?? '0') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="thumbnail" class="form-label">Course Thumbnail</label>
                            <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*">
                            <small class="text-muted">Max size: 2MB (JPEG, PNG, GIF)</small>
                            <img id="thumbnailPreview" src="#" alt="Thumbnail Preview" class="thumbnail-preview img-thumbnail">
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" 
                                   <?= isset($_POST['is_featured']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_featured">Feature this course</label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Create Course
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Thumbnail preview
        document.getElementById('thumbnail').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('thumbnailPreview');
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const description = document.getElementById('description').value.trim();
            const duration = document.getElementById('duration').value;
            
            if (!title) {
                alert('Course title is required');
                e.preventDefault();
                return;
            }
            
            if (!description) {
                alert('Description is required');
                e.preventDefault();
                return;
            }
            
            if (duration <= 0) {
                alert('Duration must be a positive number');
                e.preventDefault();
                return;
            }
        });
    </script>
</body>
</html>