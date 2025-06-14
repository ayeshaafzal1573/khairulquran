<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

// Get all students with their user accounts
$students = $db->query("
    SELECT s.*, u.email, u.username, u.status 
    FROM students s
    JOIN users u ON s.user_id = u.user_id
    ORDER BY s.student_id DESC
")->fetchAll();

// Handle student status change
if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
    $studentId = $_GET['toggle_status'];
    $stmt = $db->prepare("UPDATE users SET status = 1 - status WHERE user_id = (SELECT user_id FROM students WHERE student_id = ?)");
    $stmt->execute([$studentId]);
    $_SESSION['message'] = "Student status updated successfully";
    header('Location: manage_students.php');
    exit;
}

///DELETE
// Handle student delete
if (isset($_GET['delete_student']) && is_numeric($_GET['delete_student'])) {
    $studentId = $_GET['delete_student'];

    // First get the user_id of the student
    $stmt = $db->prepare("SELECT user_id FROM students WHERE student_id = ?");
    $stmt->execute([$studentId]);
    $user = $stmt->fetch();

    if ($user) {
        $userId = $user['user_id'];

        // Delete student record
        $stmt = $db->prepare("DELETE FROM students WHERE student_id = ?");
        $stmt->execute([$studentId]);

        // Optionally delete user account as well (uncomment if needed)
        $stmt = $db->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);

        $_SESSION['message'] = "Student deleted successfully";
    } else {
        $_SESSION['message'] = "Student not found.";
    }

    header('Location: manage_students.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Students - Khair-ul-Quran Academy</title>
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
  <?php include '../includes/loader.php'; ?>
    <?php displayAlert(); ?>
            <div >
                <div >
                <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message']); endif; ?>

                       <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Manage Students</h5>
                        <a href="add_student.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Add New Student
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Contact</th>
                                        <!-- <th>Status</th> -->
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?= $student['student_id'] ?></td>
                                        <td><?= htmlspecialchars($student['full_name']) ?></td>
                                        <td><?= htmlspecialchars($student['username']) ?></td>
                                        <td><?= htmlspecialchars($student['email']) ?></td>
                                        <td><?= htmlspecialchars($student['contact_number']) ?></td>
                                        <!-- <td>
                                            <span class="badge bg-<?= $student['status'] ? 'success' : 'danger' ?>">
                                                <?= $student['status'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td> -->
                                        <td>
                                          
                                            <a href="view_student.php?id=<?= $student['student_id'] ?>" class="btn btn-sm btn-info" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="edit_student.php?id=<?= $student['student_id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <!-- <a href="?toggle_status=<?= $student['student_id'] ?>" class="btn btn-sm btn-<?= $student['status'] ? 'warning' : 'success' ?>" title="<?= $student['status'] ? 'Deactivate' : 'Activate' ?>" onclick="return confirm('Are you sure want to change status of student?')">
                                                <i class="bi bi-power"></i>
                                            </a> -->
                                            <a href="manage_students.php?delete_student=<?= $student['student_id'] ?>" 
   class="btn btn-sm btn-danger" 
   title="Delete" 
   onclick="return confirm('Are you sure you want to delete this student? This action cannot be undone.')">
    <i class="bi bi-trash"></i>
</a>

                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
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


