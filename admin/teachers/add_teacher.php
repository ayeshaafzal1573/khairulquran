<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and process form data
    $fullName = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $specialization = trim($_POST['specialization']);
    $qualifications = trim($_POST['qualifications']);
    $bio = trim($_POST['bio']);
    $contact = trim($_POST['contact_number']);



    // Validate inputs
    $errors = [];
    if (empty($fullName)) $errors[] = 'Full name is required';
    if (empty($username)) $errors[] = 'Username is required';
    if (empty($email)) $errors[] = 'Email is required';
    if (empty($password)) $errors[] = 'Password is required';
    if (empty($specialization)) $errors[] = 'Specialization is required';

    $imageFileName = null;

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
                $imageFileName = $newFileName;
            } else {
                $errors[] = 'Image upload failed.';
            }
        } else {
            $errors[] = 'Invalid image format. Only JPG, PNG, GIF allowed.';
        }
    }


    if (empty($errors)) {
        try {
            $db->beginTransaction();

            // Create user account
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $userStmt = $db->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'teacher')");
            $userStmt->execute([$username, $email, $hashedPassword]);
            $userId = $db->lastInsertId();

            // Create teacher profile
            $teacherStmt = $db->prepare("INSERT INTO teachers (user_id, full_name, specialization, qualifications, bio, contact_number,profile_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $teacherStmt->execute([$userId, $fullName, $specialization, $qualifications, $bio, $contact, $imageFileName]);

            $db->commit();
            $_SESSION['message'] = "Teacher added successfully!";
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Teacher - Khair-ul-Quran Academy</title>
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
                            <h5 class="card-title">Add New Teacher</h5>
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
                                            <input type="text" class="form-control" id="username" name="username" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email*</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password*</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-3">Professional Information</h6>
                                        <div class="mb-3">
                                            <label for="full_name" class="form-label">Full Name*</label>
                                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="specialization" class="form-label">Specialization*</label>
                                            <input type="text" class="form-control" id="specialization" name="specialization" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="contact_number" class="form-label">Contact Number</label>
                                            <input type="text" class="form-control" id="contact_number" name="contact_number">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="teacher_image" class="form-label">Teacher Image</label>
                                    <input type="file" class="form-control" id="teacher_image" name="teacher_image" accept="image/*">
                                    <small class="text-muted">Recommended size: 800x450px</small>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="qualifications" class="form-label">Qualifications</label>
                                            <textarea class="form-control" id="qualifications" name="qualifications" rows="2"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="bio" class="form-label">Bio/Introduction</label>
                                            <textarea class="form-control" id="bio" name="bio" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <a href="manage_teachers.php" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Add Teacher</button>
                                </div>


                            </form>
                        </div>
                    </div>
                </div>
            </main>
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
</body>

</html>