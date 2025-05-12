<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

// Get filter parameters
$courseFilter = isset($_GET['course']) ? (int)$_GET['course'] : null;
$studentFilter = isset($_GET['student']) ? (int)$_GET['student'] : null;
$statusFilter = isset($_GET['status']) ? $_GET['status'] : null;

// Build query with filters
$query = "
    SELECT e.*, 
           s.full_name as student_name,
           c.title as course_title,
           t.full_name as teacher_name
    FROM enrollments e
    JOIN students s ON e.student_id = s.student_id
    JOIN courses c ON e.course_id = c.course_id
    LEFT JOIN teachers t ON c.teacher_id = t.teacher_id
    WHERE 1=1
";

$params = [];

if ($courseFilter) {
    $query .= " AND e.course_id = ?";
    $params[] = $courseFilter;
}

if ($studentFilter) {
    $query .= " AND e.student_id = ?";
    $params[] = $studentFilter;
}

if ($statusFilter && in_array($statusFilter, ['pending', 'paid', 'failed'])) {
    $query .= " AND e.payment_status = ?";
    $params[] = $statusFilter;
}

$query .= " ORDER BY e.enrollment_date DESC";

// Get enrollments
$stmt = $db->prepare($query);
$stmt->execute($params);
$enrollments = $stmt->fetchAll();

// Get courses and students for filters
$courses = $db->query("SELECT course_id, title FROM courses ORDER BY title")->fetchAll();
$students = $db->query("SELECT student_id, full_name FROM students ORDER BY full_name")->fetchAll();



// Handle status changes
if (isset($_GET['update_status']) && isset($_GET['new_status'])) {
    $enrollmentId = (int)$_GET['update_status'];
    $newStatus = $_GET['new_status'];
    
    if (in_array($newStatus, ['pending', 'paid', 'failed'])) {
        $db->prepare("UPDATE enrollments SET payment_status = ? WHERE enrollment_id = ?")
           ->execute([$newStatus, $enrollmentId]);
        $_SESSION['message'] = "Enrollment status updated successfully";
        header('Location: manage_enrollments.php?' . $_SERVER['QUERY_STRING']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Enrollments - Khairulkhan Academy</title>
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
                    <div class="card-header">
                        <h5 class="card-title">Manage Enrollments</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="course" class="form-label">Filter by Course</label>
                                    <select class="form-select" id="course" name="course">
                                        <option value="">All Courses</option>
                                        <?php foreach ($courses as $course): ?>
                                        <option value="<?= $course['course_id'] ?>" <?= $courseFilter == $course['course_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($course['title']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="student" class="form-label">Filter by Student</label>
                                    <select class="form-select" id="student" name="student">
                                        <option value="">All Students</option>
                                        <?php foreach ($students as $student): ?>
                                        <option value="<?= $student['student_id'] ?>" <?= $studentFilter == $student['student_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($student['full_name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="status" class="form-label">Filter by Payment Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">All Statuses</option>
                                        <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="paid" <?= $statusFilter === 'paid' ? 'selected' : '' ?>>Paid</option>
                                        <option value="failed" <?= $statusFilter === 'failed' ? 'selected' : '' ?>>Failed</option>
                                    </select>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Teacher</th>
                                        <th>Enrollment Date</th>
                                        <th>Payment Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($enrollments as $enrollment): ?>
                                    <tr>
                                        <td><?= $enrollment['enrollment_id'] ?></td>
                                        <td><?= htmlspecialchars($enrollment['student_name']) ?></td>
                                        <td><?= htmlspecialchars($enrollment['course_title']) ?></td>
                                        <td><?= htmlspecialchars($enrollment['teacher_name'] ?? 'N/A') ?></td>
                                        <td><?= date('M d, Y', strtotime($enrollment['enrollment_date'])) ?></td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $enrollment['payment_status'] === 'paid' ? 'success' : 
                                                ($enrollment['payment_status'] === 'failed' ? 'danger' : 'warning') 
                                            ?>">
                                                <?= ucfirst($enrollment['payment_status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="view_enrollment.php?id=<?= $enrollment['enrollment_id'] ?>">
                                                            <i class="bi bi-eye me-2"></i> View Details
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="?update_status=<?= $enrollment['enrollment_id'] ?>&new_status=paid&<?= $_SERVER['QUERY_STRING'] ?>" onclick="return confirm('Mark as paid?')">
                                                            <i class="bi bi-check-circle me-2"></i> Mark as Paid
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="?update_status=<?= $enrollment['enrollment_id'] ?>&new_status=pending&<?= $_SERVER['QUERY_STRING'] ?>" onclick="return confirm('Mark as pending?')">
                                                            <i class="bi bi-hourglass me-2"></i> Mark as Pending
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="?update_status=<?= $enrollment['enrollment_id'] ?>&new_status=failed&<?= $_SERVER['QUERY_STRING'] ?>" onclick="return confirm('Mark as failed?')">
                                                            <i class="bi bi-x-circle me-2"></i> Mark as Failed
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
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