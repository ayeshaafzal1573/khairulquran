<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

// Get filter parameters
$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : null;
$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : null;
$statusFilter = isset($_GET['status']) ? $_GET['status'] : null;
$methodFilter = isset($_GET['method']) ? $_GET['method'] : null;

// Build query with filters for payments
$query = "
    SELECT p.*, 
           e.enrollment_id,
           s.full_name as student_name,
           c.title as course_title
    FROM payments p
    JOIN enrollments e ON p.enrollment_id = e.enrollment_id
    JOIN students s ON e.student_id = s.student_id
    JOIN courses c ON e.course_id = c.course_id
    WHERE 1=1
";

$params = [];

if ($dateFrom) {
    $query .= " AND p.payment_date >= ?";
    $params[] = $dateFrom;
}

if ($dateTo) {
    $query .= " AND p.payment_date <= ?";
    $params[] = $dateTo . ' 23:59:59'; // Ensuring we include the whole day
}

if ($statusFilter && in_array($statusFilter, ['success', 'failed', 'pending'])) {
    $query .= " AND p.status = ?";
    $params[] = $statusFilter;
}

if ($methodFilter) {
    $query .= " AND p.payment_method = ?";
    $params[] = $methodFilter;
}

$query .= " ORDER BY p.payment_date DESC";

// Get filtered payments
$payments = $db->prepare($query);
$payments->execute($params);
$payments = $payments->fetchAll();

// Calculate totals with the same filters
$totalQuery = "
    SELECT 
        COALESCE(SUM(CASE WHEN status = 'success' THEN amount ELSE 0 END), 0) as success_total,
        COALESCE(SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END), 0) as pending_total,
        COALESCE(SUM(CASE WHEN status = 'failed' THEN amount ELSE 0 END), 0) as failed_total,
        COUNT(*) as total_count
    FROM payments
    WHERE 1=1
";

$totalParams = [];

if ($dateFrom) {
    $totalQuery .= " AND payment_date >= ?";
    $totalParams[] = $dateFrom;
}

if ($dateTo) {
    $totalQuery .= " AND payment_date <= ?";
    $totalParams[] = $dateTo . ' 23:59:59';
}

if ($statusFilter && in_array($statusFilter, ['success', 'failed', 'pending'])) {
    $totalQuery .= " AND status = ?";
    $totalParams[] = $statusFilter;
}

if ($methodFilter) {
    $totalQuery .= " AND payment_method = ?";
    $totalParams[] = $methodFilter;
}

// Execute total query
$totalStmt = $db->prepare($totalQuery);
$totalStmt->execute($totalParams);
$totals = $totalStmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments - Khairulkhan Academy</title>
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
                    <div class="card-header">
                        <h5 class="card-title">Manage Payments</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="date_from" class="form-label">From Date</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="date_to" class="form-label">To Date</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to" value="<?= htmlspecialchars($dateTo) ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">All Statuses</option>
                               <option value="success" <?= $statusFilter === 'success' ? 'selected' : '' ?>>Paid</option>
      <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="failed" <?= $statusFilter === 'failed' ? 'selected' : '' ?>>Failed</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="method" class="form-label">Method</label>
                                    <select class="form-select" id="method" name="method">
                                        <option value="">All Methods</option>
                                        <option value="manual" <?= $methodFilter === 'manual' ? 'selected' : '' ?>>Manual</option>
                                        <option value="credit_card" <?= $methodFilter === 'credit_card' ? 'selected' : '' ?>>Credit Card</option>
                                        <option value="jazzcash" <?= $methodFilter === 'jazzcash' ? 'selected' : '' ?>>JazzCash</option>
                                        <option value="easypaisa" <?= $methodFilter === 'easypaisa' ? 'selected' : '' ?>>EasyPaisa</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                                    <a href="manage_payments.php" class="btn btn-secondary">Reset Filters</a>
                                </div>
                            </div>
                        </form>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5>Successful Payments</h5>
                                        <h2>Rs <?= number_format($totals['success_total'] ?? 0, 2) ?></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-warning text-dark">
                                    <div class="card-body">
                                        <h5>Pending Payments</h5>
                                        <h2>Rs <?= number_format($totals['pending_total'] ?? 0, 2) ?></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h5>Failed Payments</h5>
                                        <h2>Rs <?= number_format($totals['failed_total'] ?? 0, 2) ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><?= $payment['payment_id'] ?></td>
                                        <td><?= htmlspecialchars($payment['student_name']) ?></td>
                                        <td><?= htmlspecialchars($payment['course_title']) ?></td>
                                        <td>Rs <?= number_format($payment['amount'], 2) ?></td>
                                        <td><?= date('d M Y', strtotime($payment['payment_date'])) ?></td>
                                        <td><?= ucwords(str_replace('_', ' ', $payment['payment_method'])) ?></td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $payment['status'] === 'success' ? 'success' : 
                                                ($payment['status'] === 'failed' ? 'danger' : 'warning')
                                            ?>">
                                                <?= ucfirst($payment['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="payment_details.php?id=<?= $payment['payment_id'] ?>" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="?delete=<?= $payment['payment_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
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
</body>
</html>
