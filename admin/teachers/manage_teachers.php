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
// Handle delete teacher
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $teacherId = $_GET['delete'];

    // Get user_id before deleting teacher
    $stmt = $db->prepare("SELECT user_id FROM teachers WHERE teacher_id = ?");
    $stmt->execute([$teacherId]);
    $user = $stmt->fetch();

    if ($user) {
        // Delete teacher
        $db->prepare("DELETE FROM teachers WHERE teacher_id = ?")->execute([$teacherId]);

        // Optionally delete user account also
        $db->prepare("DELETE FROM users WHERE user_id = ?")->execute([$user['user_id']]);

        $_SESSION['message'] = "Teacher deleted successfully";
    } else {
        $_SESSION['message'] = "Teacher not found";
    }

    header('Location: manage_teachers.php');
    exit;
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Teacher - Khair-ul-Quran Academy</title>
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
                                        <!-- <th>Status</th> -->
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
                                        <!-- <td>
                                            <span class="badge bg-<?= $teacher['status'] ? 'success' : 'danger' ?>">
                                                <?= $teacher['status'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td> -->
                                        <td>
                                            <a href="view_teacher.php?id=<?= $teacher['teacher_id'] ?>" class="btn btn-sm btn-info" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="edit_teacher.php?id=<?= $teacher['teacher_id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="?delete=<?= $teacher['teacher_id'] ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this teacher? This will also delete their user account.')">
    <i class="bi bi-trash"></i>
</a>

                                            <!-- <a href="?toggle_status=<?= $teacher['teacher_id'] ?>" class="btn btn-sm btn-<?= $teacher['status'] ? 'warning' : 'success' ?>" title="<?= $teacher['status'] ? 'Deactivate' : 'Activate' ?>" onclick="return confirm('Are you sure?')">
                                                <i class="bi bi-power"></i>
                                            </a> -->
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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


