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

// Fetch current student data
$stmt = $db->prepare("SELECT student_id, full_name, address, contact_number, parent_name, parent_contact, previous_education, profile_image FROM students WHERE user_id = ?");
$stmt->execute([$student_user_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student profile not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $address = $_POST['address'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $parent_name = $_POST['parent_name'] ?? '';
    $parent_contact = $_POST['parent_contact'] ?? '';
    $previous_education = $_POST['previous_education'] ?? '';

    // Image upload handling
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_image']['tmp_name'];
        $fileName = $_FILES['profile_image']['name'];
        $fileSize = $_FILES['profile_image']['size'];
        $fileType = $_FILES['profile_image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Directory where images will be saved (make sure it's writable)
            $uploadFileDir = '../uploads/';
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                // Delete old image if exists
                if ($student['profile_image'] && file_exists($uploadFileDir . $student['profile_image'])) {
                    unlink($uploadFileDir . $student['profile_image']);
                }
                $profile_image_to_save = $newFileName;
            } else {
                $error = 'There was an error moving the uploaded file.';
            }
        } else {
            $error = 'Upload failed. Allowed file types: ' . implode(', ', $allowedfileExtensions);
        }
    } else {
        // If no new image uploaded, keep old image filename
        $profile_image_to_save = $student['profile_image'];
    }

    if (!$error) {
        // Update database
        $update_stmt = $db->prepare("UPDATE students SET full_name = ?, address = ?, contact_number = ?, parent_name = ?, parent_contact = ?, previous_education = ?, profile_image = ? WHERE user_id = ?");
        $updated = $update_stmt->execute([$full_name, $address, $contact_number, $parent_name, $parent_contact, $previous_education, $profile_image_to_save, $student_user_id]);

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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h1>Edit My Profile</h1>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success; ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= $error; ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="full_name" class="form-label">Full Name</label>
        <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($student['full_name']); ?>" required>
    </div>

    <div class="mb-3">
        <label for="address" class="form-label">Address</label>
        <textarea name="address" class="form-control"><?= htmlspecialchars($student['address']); ?></textarea>
    </div>

    <div class="mb-3">
        <label for="contact_number" class="form-label">Contact Number</label>
        <input type="text" name="contact_number" class="form-control" value="<?= htmlspecialchars($student['contact_number']); ?>">
    </div>

    <div class="mb-3">
        <label for="parent_name" class="form-label">Parent Name</label>
        <input type="text" name="parent_name" class="form-control" value="<?= htmlspecialchars($student['parent_name']); ?>">
    </div>

    <div class="mb-3">
        <label for="parent_contact" class="form-label">Parent Contact</label>
        <input type="text" name="parent_contact" class="form-control" value="<?= htmlspecialchars($student['parent_contact']); ?>">
    </div>

    <div class="mb-3">
        <label for="previous_education" class="form-label">Previous Education</label>
        <textarea name="previous_education" class="form-control"><?= htmlspecialchars($student['previous_education']); ?></textarea>
    </div>

    <div class="mb-3">
        <label for="profile_image" class="form-label">Profile Image</label><br>
        <?php if ($student['profile_image']): ?>
            <img src="../uploads/<?= htmlspecialchars($student['profile_image']); ?>" alt="Profile Image" class="img-thumbnail mb-2" style="max-width:150px;"><br>
        <?php endif; ?>
        <input type="file" name="profile_image" accept="image/*" class="form-control">
        <small class="form-text text-muted">Leave empty if you do not want to change the image.</small>
    </div>

    <button type="submit" class="btn btn-primary">Update Profile</button>
</form>

<a href="student_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>

</body>
</html>
