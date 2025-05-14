<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

// Get all teachers with their user accounts
$teachers = $db->query("
    SELECT t.*, u.email, u.username, u.status 
    FROM teachers t
    JOIN users u ON t.user_id = u.user_id
    ORDER BY t.full_name
")->fetchAll();

// Handle teacher status change
if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
    $teacherId = $_GET['toggle_status'];
    $stmt = $db->prepare("UPDATE users SET status = 1 - status WHERE user_id = (SELECT user_id FROM teachers WHERE teacher_id = ?)");
    $stmt->execute([$teacherId]);
    $_SESSION['message'] = "Teacher status updated successfully";
    header('Location: manage_teachers.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers - Khair-ul-Quran Academy</title>
    <?php include '../../includes/header.php'; ?>
</head>
<body>
   <div class="container-fluid">
  <div class="row">
    
    <nav class="col-md-2 d-none d-md-block p-0 bg-dark sidebar" id="sidebar">
      <?php include '../includes/sidebar.php'; ?>
    </nav>
        <main class="col-md-10 ms-sm-auto p-4">

            <div>
                <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message']); endif; ?>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Manage Teachers</h5>
                        <a href="add_teacher.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Add New Teacher
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Specialization</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($teachers as $teacher): ?>
                                    <tr>
                                        <td><?= $teacher['teacher_id'] ?></td>
                                        <td><?= htmlspecialchars($teacher['full_name']) ?></td>
                                        <td><?= htmlspecialchars($teacher['specialization']) ?></td>
                                        <td><?= htmlspecialchars($teacher['email']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $teacher['status'] ? 'success' : 'danger' ?>">
                                                <?= $teacher['status'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="view_teacher.php?id=<?= $teacher['teacher_id'] ?>" class="btn btn-sm btn-info" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="edit_teacher.php?id=<?= $teacher['teacher_id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="?toggle_status=<?= $teacher['teacher_id'] ?>" class="btn btn-sm btn-<?= $teacher['status'] ? 'warning' : 'success' ?>" title="<?= $teacher['status'] ? 'Deactivate' : 'Activate' ?>" onclick="return confirm('Are you sure?')">
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