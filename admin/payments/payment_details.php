<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_payments.php');
    exit;
}

$paymentId = $_GET['id'];

$statement = $db->prepare("
    SELECT p.*, 
           e.enrollment_id,
           s.full_name as student_name, s.contact_number as student_contact,
           c.title as course_title, c.price as course_price,
           t.full_name as teacher_name
    FROM payments p
    JOIN enrollments e ON p.enrollment_id = e.enrollment_id
    JOIN students s ON e.student_id = s.student_id
    JOIN courses c ON e.course_id = c.course_id
    LEFT JOIN teachers t ON c.teacher_id = t.teacher_id
    WHERE p.payment_id = ?
");

$statement->execute([$paymentId]);
$payment = $statement->fetch();


if (!$payment) {
    header('Location: manage_payments.php');
    exit;
}

// Handle status update
if (isset($_GET['update_status']) && in_array($_GET['update_status'], ['paid', 'pending', 'failed'])) {
    $newStatus = $_GET['update_status'];
    $db->prepare("UPDATE payments SET status = ? WHERE payment_id = ?")
       ->execute([$newStatus, $paymentId]);
    $_SESSION['message'] = "Payment status updated successfully";
    header("Location: payment_details.php?id=$paymentId");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details - Khair-ul-Quran Academy</title>
</head>
<body>
 <div class="container-fluid">
  <div class="row">
    
    <nav class="col-md-2 d-none d-md-block p-0 bg-dark sidebar" id="sidebar">
      <?php include '../includes/sidebar.php'; ?>
    </nav>
        <main class="col-md-10 ms-sm-auto p-4">
            <div class="container-fluid py-4">
                <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message']); endif; ?>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Payment Details</h5>
                        <div>
                            <a href="manage_payments.php" class="btn btn-sm btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Payment Information</h6>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Payment ID</th>
                                        <td><?= $payment['payment_id'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Amount</th>
                                        <td><?= number_format($payment['amount'], 2) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Payment Date</th>
                                        <td><?= date('M d, Y H:i', strtotime($payment['payment_date'])) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Payment Method</th>
                                        <td><?= ucwords(str_replace('_', ' ', $payment['payment_method'])) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $payment['status'] === 'paid' ? 'success' : 
                                                ($payment['status'] === 'failed' ? 'danger' : 'warning')
                                            ?>">
                                                <?= ucfirst($payment['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Transaction Details</th>
                                        <td><?= htmlspecialchars($payment['transaction_details']) ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Related Information</h6>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Enrollment ID</th>
                                        <td><?= $payment['enrollment_id'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Student Name</th>
                                        <td><?= htmlspecialchars($payment['student_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Student Contact</th>
                                        <td><?= htmlspecialchars($payment['student_contact']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Course</th>
                                        <td><?= htmlspecialchars($payment['course_title']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Teacher</th>
                                        <td><?= htmlspecialchars($payment['teacher_name'] ?? 'N/A') ?></td>
                                    </tr>
                                    <tr>
                                        <th>Course Price</th>
                                        <td><?= number_format($payment['course_price'], 2) ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h6>Update Payment Status</h6>
                            <div class="d-flex gap-2">
                                <a href="payment_details.php?id=<?= $paymentId ?>&update_status=success" 
                                   class="btn btn-success <?= $payment['status'] === 'paid' ? 'disabled' : '' ?>">
                                    <i class="bi bi-check-circle me-1"></i> Mark as Success
                                </a>
                                <a href="payment_details.php?id=<?= $paymentId ?>&update_status=pending" 
                                   class="btn btn-warning text-white <?= $payment['status'] === 'pending' ? 'disabled' : '' ?>">
                                    <i class="bi bi-hourglass me-1"></i> Mark as Pending
                                </a>
                                <a href="payment_details.php?id=<?= $paymentId ?>&update_status=failed" 
                                   class="btn btn-danger <?= $payment['status'] === 'failed' ? 'disabled' : '' ?>">
                                    <i class="bi bi-x-circle me-1"></i> Mark as Failed
                                </a>
                            </div>
                        </div>
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