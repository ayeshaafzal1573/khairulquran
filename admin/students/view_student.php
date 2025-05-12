<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_students.php');
    exit;
}

$studentId = $_GET['id'];

$stmt = $db->prepare("
    SELECT s.*, u.email, u.username, u.status, u.created_at as join_date
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

$stmt = $db->prepare("
    SELECT e.enrollment_id, c.title as course_title, e.enrollment_date, 
           e.completion_status, e.payment_status, t.full_name as teacher_name
    FROM enrollments e
    JOIN courses c ON e.course_id = c.course_id
    LEFT JOIN teachers t ON c.teacher_id = t.teacher_id
    WHERE e.student_id = ?
    ORDER BY e.enrollment_date DESC
");
$stmt->execute([$studentId]);
$enrollments = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student - Khairulkhan Academy</title>
</head>
<body>
    <div class="container-fluid">
  <div class="row">
    
    <nav class="col-md-2 d-none d-md-block p-0 bg-dark sidebar" id="sidebar">
      <?php include '../includes/sidebar.php'; ?>
    </nav>
        <main class="col-md-10 ms-sm-auto p-4">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Student Details</h5>
                        <div>
                            <a href="edit_student.php?id=<?= $studentId ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil me-1"></i> Edit
                            </a>
                            <a href="manage_students.php" class="btn btn-sm btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Account Information</h6>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Student ID</th>
                                        <td><?= $student['student_id'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Username</th>
                                        <td><?= htmlspecialchars($student['username']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><?= htmlspecialchars($student['email']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <span class="badge bg-<?= $student['status'] ? 'success' : 'danger' ?>">
                                                <?= $student['status'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Join Date</th>
                                        <td><?= date('M d, Y', strtotime($student['join_date'])) ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Personal Information</h6>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Full Name</th>
                                        <td><?= htmlspecialchars($student['full_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Contact Number</th>
                                        <td><?= htmlspecialchars($student['contact_number']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><?= htmlspecialchars($student['address']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Parent/Guardian</th>
                                        <td><?= htmlspecialchars($student['parent_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Parent Contact</th>
                                        <td><?= htmlspecialchars($student['parent_contact']) ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Course Enrollments</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($enrollments)): ?>
                        <div class="alert alert-info">No course enrollments found.</div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Enrollment ID</th>
                                        <th>Course</th>
                                        <th>Teacher</th>
                                        <th>Enrollment Date</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($enrollments as $enrollment): ?>
                                    <tr>
                                        <td><?= $enrollment['enrollment_id'] ?></td>
                                        <td><?= htmlspecialchars($enrollment['course_title']) ?></td>
                                        <td><?= htmlspecialchars($enrollment['teacher_name'] ?? 'N/A') ?></td>
                                        <td><?= date('M d, Y', strtotime($enrollment['enrollment_date'])) ?></td>
                                        <td>
                                            <?php 
                                            $statusClass = [
                                                'not_started' => 'secondary',
                                                'in_progress' => 'info',
                                                'completed' => 'success'
                                            ];
                                            ?>
                                            <span class="badge bg-<?= $statusClass[$enrollment['completion_status']] ?>">
                                                <?= ucwords(str_replace('_', ' ', $enrollment['completion_status'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $enrollment['payment_status'] === 'paid' ? 'success' : 'warning' ?>">
                                                <?= ucfirst($enrollment['payment_status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="../enrollments/view_enrollment.php?id=<?= $enrollment['enrollment_id'] ?>" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
                                        </main>
        </div>
    </div>

</body>
</html>