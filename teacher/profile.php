<?php
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$teacher_id = $_SESSION['user_id'];
$upload_dir = '../uploads/';  // Make sure this folder exists and is writable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['name'] ?? '';
    $contact_number = $_POST['contact'] ?? '';
    $bio = $_POST['bio'] ?? '';

    // Handle file upload
    $profile_image = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['profile_image']['tmp_name'];
        $file_name = basename($_FILES['profile_image']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_ext, $allowed_ext)) {
            // Rename file to avoid conflicts (optional)
            $new_file_name = 'teacher_' . $teacher_id . '_' . time() . '.' . $file_ext;
            $destination = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $destination)) {
                $profile_image = $new_file_name;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
    }

    if (!$full_name) {
        $error = "Name is required.";
    }

    if (empty($error)) {
        if ($profile_image) {
            // Update with new profile image
            $stmt = $db->prepare("UPDATE teachers SET full_name = ?, contact_number = ?, bio = ?, profile_image = ? WHERE user_id = ?");
            $stmt->execute([$full_name, $contact_number, $bio, $profile_image, $teacher_id]);
        } else {
            // Update without changing profile image
            $stmt = $db->prepare("UPDATE teachers SET full_name = ?, contact_number = ?, bio = ? WHERE user_id = ?");
            $stmt->execute([$full_name, $contact_number, $bio, $teacher_id]);
        }
        $success = "Profile updated successfully.";
    }
}

// Fetch current data
$stmt = $db->prepare("SELECT full_name, contact_number, bio, profile_image FROM teachers WHERE user_id = ?");
$stmt->execute([$teacher_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Teacher Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h1>Profile Settings</h1>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="name" class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($result['full_name']) ?>" required>
    </div>
    <div class="mb-3">
        <label for="contact" class="form-label">Contact Number</label>
        <input type="text" name="contact" class="form-control" value="<?= htmlspecialchars($result['contact_number']) ?>">
    </div>
    <div class="mb-3">
        <label for="bio" class="form-label">Bio</label>
        <textarea name="bio" class="form-control" rows="4"><?= htmlspecialchars($result['bio']) ?></textarea>
    </div>
    <div class="mb-3">
        <label for="profile_image" class="form-label">Profile Image</label><br>
        <?php if (!empty($result['profile_image'])): ?>
            <img src="../uploads/<?= htmlspecialchars($result['profile_image']) ?>" alt="Profile Image" style="max-width:150px; max-height:150px; display:block; margin-bottom:10px;">
        <?php else: ?>
            <p>No profile image uploaded.</p>
        <?php endif; ?>
        <input type="file" name="profile_image" accept="image/*" class="form-control">
        <small class="form-text text-muted">Allowed types: jpg, jpeg, png, gif</small>
    </div>

    <button type="submit" class="btn btn-primary">Update Profile</button>
</form>

<a href="teacher_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>

</body>
</html>
