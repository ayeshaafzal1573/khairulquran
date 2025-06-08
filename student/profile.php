<?php
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $parent_name = trim($_POST['parent_name'] ?? '');
    $parent_contact = trim($_POST['parent_contact'] ?? '');
    $previous_education = trim($_POST['previous_education'] ?? '');

    // Validate inputs
    if (empty($full_name)) {
        $error = "Full name is required.";
    } else {
        // Image upload handling
        $profile_image_to_save = $student['profile_image']; // Default to existing image
        
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_image']['tmp_name'];
            $fileName = $_FILES['profile_image']['name'];
            $fileSize = $_FILES['profile_image']['size'];
            $fileType = $_FILES['profile_image']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $maxFileSize = 2 * 1024 * 1024; // 2MB

            if (in_array($fileExtension, $allowedfileExtensions)) {
                if ($fileSize <= $maxFileSize) {
                    $uploadFileDir = '../uploads/profiles/';
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }
                    
                    $newFileName = 'profile_' . $student_user_id . '_' . time() . '.' . $fileExtension;
                    $dest_path = $uploadFileDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        // Delete old image if exists
                        if ($student['profile_image'] && file_exists($uploadFileDir . $student['profile_image'])) {
                            unlink($uploadFileDir . $student['profile_image']);
                        }
                        $profile_image_to_save = $newFileName;
                    } else {
                        $error = 'There was an error moving the uploaded file.';
                    }
                } else {
                    $error = 'File size exceeds maximum limit of 2MB.';
                }
            } else {
                $error = 'Upload failed. Allowed file types: ' . implode(', ', $allowedfileExtensions);
            }
        }

        if (!$error) {
            // Update database
            $update_stmt = $db->prepare("UPDATE students SET 
                full_name = ?, 
                address = ?, 
                contact_number = ?, 
                parent_name = ?, 
                parent_contact = ?, 
                previous_education = ?, 
                profile_image = ? 
                WHERE user_id = ?");
            
            $updated = $update_stmt->execute([
                $full_name, 
                $address, 
                $contact_number, 
                $parent_name, 
                $parent_contact, 
                $previous_education, 
                $profile_image_to_save, 
                $student_user_id
            ]);

            if ($updated) {
                $success = "Profile updated successfully.";
                // Refresh student data
                $stmt->execute([$student_user_id]);
                $student = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = "Failed to update profile.";
            }
        }
    }
}

// Count enrolled courses for sidebar
$enrolled_stmt = $db->prepare("SELECT COUNT(*) FROM enrollments WHERE student_id = ?");
$enrolled_stmt->execute([$student['student_id']]);
$enrolled_count = $enrolled_stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
       <?php include './includes/sidebar.php'; ?>
    <div class="main-content">
        
<div class="container-fluid">
    <div class="row">
      
        <!-- Main Content -->
        <main class="col-12 p-3">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                <h1 class="h2">Edit Profile</h1>
                
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="form-section">
                        <form method="POST" enctype="multipart/form-data" id="profileForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="full_name" class="form-control" 
                                               value="<?= htmlspecialchars($student['full_name']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact_number" class="form-label">Contact Number</label>
                                        <input type="tel" name="contact_number" class="form-control" 
                                               value="<?= htmlspecialchars($student['contact_number']); ?>">
                                        <small class="form-text text-muted">Format: 10-15 digits</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="address" class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($student['address']); ?></textarea>
                            </div>

                            <h5 class="mb-3">Parent/Guardian Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="parent_name" class="form-label">Parent/Guardian Name</label>
                                        <input type="text" name="parent_name" class="form-control" 
                                               value="<?= htmlspecialchars($student['parent_name']); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="parent_contact" class="form-label">Parent/Guardian Contact</label>
                                        <input type="tel" name="parent_contact" class="form-control" 
                                               value="<?= htmlspecialchars($student['parent_contact']); ?>">
                                        <small class="form-text text-muted">Format: 10-15 digits</small>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="previous_education" class="form-label">Previous Education</label>
                                <textarea name="previous_education" class="form-control" rows="3"><?= htmlspecialchars($student['previous_education']); ?></textarea>
                            </div>

                            <div class="mb-4">
                                <label for="profile_image" class="form-label">Profile Image</label>
                                <div class="d-flex align-items-center">
                                    <div class="me-4 text-center">
                                        <?php if ($student['profile_image']): ?>
                                            <img src="../uploads/profiles/<?= htmlspecialchars($student['profile_image']) ?>" 
                                                 alt="Current Profile" class="profile-image-preview" id="imagePreview">
                                        <?php else: ?>
                                            <img src="https://cdn-icons-png.flaticon.com/512/9187/9187604.png" 
                                                 alt="Default Profile" class="profile-image-preview" id="imagePreview">
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file" name="profile_image" id="profile_image" 
                                               accept="image/*" class="form-control">
                                        <small class="form-text text-muted">
                                            Max size: 2MB. Allowed types: JPG, JPEG, PNG, GIF.
                                            Leave empty to keep current image.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save"></i> Save Changes
                                </button>
                                <a href="profile.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-x"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
            </div>
        </main>
    </div>
</div>

    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Image preview functionality
    document.getElementById('profile_image').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // // Form validation
    // document.getElementById('profileForm').addEventListener('submit', function(event) {
    //     const contactNumber = document.querySelector('input[name="contact_number"]').value;
    //     const parentContact = document.querySelector('input[name="parent_contact"]').value;
        
    //     if (contactNumber && !/^[0-9]{10,15}$/.test(contactNumber)) {
    //         alert('Contact number must be 10-15 digits.');
    //         event.preventDefault();
    //         return false;
    //     }
        
    //     if (parentContact && !/^[0-9]{10,15}$/.test(parentContact)) {
    //         alert('Parent contact must be 10-15 digits.');
    //         event.preventDefault();
    //         return false;
    //     }
        
    //     return true;
    // });
</script>
</body>
</html>





