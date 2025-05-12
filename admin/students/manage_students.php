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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Khairulkhan Academy</title>
</head>
<body>
     <div class="container-fluid">
  <div class="row">
    
    <nav class="col-md-2 d-none d-md-block p-0 bg-dark sidebar" id="sidebar">
      <?php include '../includes/sidebar.php'; ?>
    </nav>
        <main class="col-md-10 ms-sm-auto p-4">
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
                                        <th>Status</th>
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
                                        <td>
                                            <span class="badge bg-<?= $student['status'] ? 'success' : 'danger' ?>">
                                                <?= $student['status'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="view_student.php?id=<?= $student['student_id'] ?>" class="btn btn-sm btn-info" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="edit_student.php?id=<?= $student['student_id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="?toggle_status=<?= $student['student_id'] ?>" class="btn btn-sm btn-<?= $student['status'] ? 'warning' : 'success' ?>" title="<?= $student['status'] ? 'Deactivate' : 'Activate' ?>" onclick="return confirm('Are you sure want to change status of student?')">
                                                <i class="bi bi-power"></i>
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

</body>
</html>