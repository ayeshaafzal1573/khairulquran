<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_enrollments.php');
    exit;
}

$enrollmentId = $_GET['id'];

if (isset($_GET['mark']) && in_array($_GET['mark'], ['paid', 'pending', 'failed'])) {
    $markStatus = $_GET['mark'];

    // Update the payment status in the database
    $updateStmt = $db->prepare("
        UPDATE enrollments 
        SET payment_status = ? 
        WHERE enrollment_id = ?
    ");
    $updateStmt->execute([$markStatus, $enrollmentId]);

    // If marked as paid, insert a manual payment record
    if ($markStatus === 'paid') {
        // First, get the enrollment details to fetch course price
        $getCourse = $db->prepare("SELECT c.price AS course_price FROM enrollments e JOIN courses c ON e.course_id = c.course_id WHERE e.enrollment_id = ?");
        $getCourse->execute([$enrollmentId]);
        $course = $getCourse->fetch();

        if ($course) {
            $insertPayment = $db->prepare("
                INSERT INTO payments (enrollment_id, amount, payment_date, payment_method, status, transaction_details)
                VALUES (?, ?, NOW(), ?, ?, ?)
            ");
            $insertPayment->execute([
                $enrollmentId,
                $course['course_price'],
                'manual',
                'success',
                'Marked as paid manually by admin'
            ]);
        }
    }

    // Redirect after processing
    header("Location: view_enrollment.php?id=$enrollmentId");
    exit;
}

// Get enrollment details
$stmt = $db->prepare("
    SELECT e.*, 
           s.full_name as student_name, s.contact_number as student_contact, s.address as student_address,
           c.title as course_title, c.price as course_price, c.duration as course_duration,
           t.full_name as teacher_name
    FROM enrollments e
    JOIN students s ON e.student_id = s.student_id
    JOIN courses c ON e.course_id = c.course_id
    LEFT JOIN teachers t ON c.teacher_id = t.teacher_id
    WHERE e.enrollment_id = ?
");
$stmt->execute([$enrollmentId]);
$enrollment = $stmt->fetch();

// Get payment history
$stmt2 = $db->prepare("
    SELECT * FROM payments
    WHERE enrollment_id = ?
    ORDER BY payment_date DESC
");
$stmt2->execute([$enrollmentId]);
$payments = $stmt2->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Enrollment - Khairulkhan Academy</title>
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
                        <h5 class="mb-0">Enrollment Details</h5>
                        <div>
                            <a href="manage_enrollments.php" class="btn btn-sm btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Student Information</h6>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Student Name</th>
                                        <td><?= htmlspecialchars($enrollment['student_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Contact Number</th>
                                        <td><?= htmlspecialchars($enrollment['student_contact']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><?= htmlspecialchars($enrollment['student_address']) ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Course Information</h6>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Course Title</th>
                                        <td><?= htmlspecialchars($enrollment['course_title']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Teacher</th>
                                        <td><?= htmlspecialchars($enrollment['teacher_name'] ?? 'N/A') ?></td>
                                    </tr>
                                    <tr>
                                        <th>Duration</th>
                                        <td><?= htmlspecialchars($enrollment['course_duration']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Course Fee</th>
                                        <td><?= number_format($enrollment['course_price'], 2) ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h6>Enrollment Details</h6>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Enrollment ID</th>
                                        <td><?= $enrollment['enrollment_id'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Enrollment Date</th>
                                        <td><?= date('M d, Y', strtotime($enrollment['enrollment_date'])) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Completion Status</th>
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
                                    </tr>
                                    <tr>
                                        <th>Payment Status</th>
                                        <td>
                                            <span class="badge bg-<?= $enrollment['payment_status'] === 'paid' ? 'success' : ($enrollment['payment_status'] === 'failed' ? 'danger' : 'warning') ?>">
                                                <?= ucfirst($enrollment['payment_status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Payment Actions</h6>
                                <div class="d-grid gap-2">
                                    <a href="?id=<?= $enrollmentId ?>&mark=paid" class="btn btn-success" onclick="return confirm('Mark as paid?')">
                                        <i class="bi bi-check-circle me-1"></i> Mark as Paid
                                    </a>
                                    <a href="?id=<?= $enrollmentId ?>&mark=pending" class="btn btn-warning text-white" onclick="return confirm('Mark as pending?')">
                                        <i class="bi bi-hourglass me-1"></i> Mark as Pending
                                    </a>
                                    <a href="?id=<?= $enrollmentId ?>&mark=failed" class="btn btn-danger" onclick="return confirm('Mark as failed?')">
                                        <i class="bi bi-x-circle me-1"></i> Mark as Failed
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Payment History</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($payments)): ?>
                        <div class="alert alert-info">No payment records found.</div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Amount</th>
                                        <th>Payment Date</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Transaction Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><?= $payment['payment_id'] ?></td>
                                        <td><?= number_format($payment['amount'], 2) ?></td>
                                        <td><?= date('M d, Y', strtotime($payment['payment_date'])) ?></td>
                                        <td><?= ucfirst($payment['payment_method']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $payment['status'] === 'paid' ? 'success' : ($payment['status'] === 'failed' ? 'danger' : 'warning') ?>">
                                                <?= ucfirst($payment['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($payment['transaction_details']) ?></td>
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