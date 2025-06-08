<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_students.php');
    exit;
}

$studentId = $_GET['id'];
//get student data
$stmt = $db->prepare("
    SELECT s.*, u.email, u.username, u.status 
    FROM students s
    JOIN users u ON s.user_id = u.user_id
    WHERE s.student_id = ?
");
$stmt->execute([$studentId]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: manage_students.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact_number']);
    $address = trim($_POST['address']);
    $parentName = trim($_POST['parent_name']);
    $parentContact = trim($_POST['parent_contact']);
    $status = isset($_POST['status']) ? 1 : 0;

    // Validate inputs
    $errors = [];
    if (empty($fullName)) $errors[] = 'Full name is required';
    if (empty($username)) $errors[] = 'Username is required';
    if (empty($email)) $errors[] = 'Email is required';

    if (empty($errors)) {
        try {
            $db->beginTransaction();

            // Update user account
            $userStmt = $db->prepare("UPDATE users SET username = ?, email = ?, status = ? WHERE user_id = ?");
            $userStmt->execute([$username, $email, $status, $student['user_id']]);

            // Update password if provided
            if (!empty($_POST['password'])) {
                $hashedPassword = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
                $db->prepare("UPDATE users SET password = ? WHERE user_id = ?")->execute([$hashedPassword, $student['user_id']]);
            }

            // Update student profile
            $studentStmt = $db->prepare("UPDATE students SET full_name = ?, contact_number = ?, address = ?, parent_name = ?, parent_contact = ? WHERE student_id = ?");
            $studentStmt->execute([$fullName, $contact, $address, $parentName, $parentContact, $studentId]);

            $db->commit();
            $_SESSION['message'] = "Student updated successfully!";
            header('Location: manage_students.php');
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
  <title>Edit Student - Khair-ul-Quran Academy</title>
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
                <div >
                <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message']); endif; ?>

                  <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Edit Student: <?= htmlspecialchars($student['full_name']) ?></h5>
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

                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-3">Account Information</h6>
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username*</label>
                                        <input type="text" class="form-control" id="username" name="username" 
                                               value="<?= htmlspecialchars($student['username']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email*</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?= htmlspecialchars($student['email']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="password" name="password">
                                        <small class="text-muted">Leave blank to keep current password</small>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="status" name="status" 
                                            <?= $student['status'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="status">Active Account</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-3">Personal Information</h6>
                                    <div class="mb-3">
                                        <label for="full_name" class="form-label">Full Name*</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" 
                                               value="<?= htmlspecialchars($student['full_name']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="contact_number" class="form-label">Contact Number</label>
                                        <input type="text" class="form-control" id="contact_number" name="contact_number" 
                                               value="<?= htmlspecialchars($student['contact_number']) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="2"><?= htmlspecialchars($student['address']) ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-3">Parent/Guardian Information</h6>
                                    <div class="mb-3">
                                        <label for="parent_name" class="form-label">Parent/Guardian Name</label>
                                        <input type="text" class="form-control" id="parent_name" name="parent_name" 
                                               value="<?= htmlspecialchars($student['parent_name']) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="parent_contact" class="form-label">Parent/Guardian Contact</label>
                                        <input type="text" class="form-control" id="parent_contact" name="parent_contact" 
                                               value="<?= htmlspecialchars($student['parent_contact']) ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="manage_students.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Student</button>
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


