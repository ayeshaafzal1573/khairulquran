<?php
ob_start();
session_start();

require_once 'includes/header.php';
require_once 'includes/auth.php';
require_once 'includes/config.php';

// Check course ID parameter
if (!isset($_GET['course_id'])) {
  header("Location: courses.php");
  exit();
}

// Authentication check
if (!isLoggedIn()) {
  $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
  header("Location: login.php");
  exit();
}

if (!isStudent()) {
  $_SESSION['error'] = "Only students can enroll in courses";
  header("Location: courses.php");
  exit();
}

// Get course details
$courseId = $_GET['course_id'];
$stmt = $db->prepare("SELECT * FROM courses WHERE course_id = ?");
$stmt->execute([$courseId]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
  $_SESSION['error'] = "Course not found";
  header("Location: courses.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fullName = trim($_POST['full_name']);  
  $contact = trim($_POST['contact']);
  $paymentMethod = $_POST['payment_method'];
  $transactionId = trim($_POST['transaction_id']);
  $courseId = $_POST['course_id'];
  $userId = $_SESSION['user_id'];

  $stmtStudent = $db->prepare("SELECT student_id FROM students WHERE user_id = ?");
  $stmtStudent->execute([$userId]);
  $student = $stmtStudent->fetch(PDO::FETCH_ASSOC);

  if (!$student) {
      $_SESSION['error'] = "Student profile nahi mila, admin se rabta karein.";
      header("Location: courses.php");
      exit();
  }

  $studentId = $student['student_id'];

  $insertStmt = $db->prepare("
      INSERT INTO enrollments (student_id, course_id, payment_status, transaction_id)
      VALUES (?, ?, ?, ?)
  ");

  $success = $insertStmt->execute([$studentId, $courseId, 'pending', $transactionId]);

  if ($success) {
      $_SESSION['success'] = "Enrollment successfully ho gaya!";
      header("Location: student/dashboard.php");
      exit();
  } else {
      $_SESSION['error'] = "Enrollment mein masla aya, dobara koshish karein.";
  }
}
?>
<style>
  .card-body h2{
      color: #ff9800;
  }
.btn-lg{
  color:white;
    background-color: #ff9800;
}
</style>
<!-- Stylish Enroll Form UI -->
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-5">
          <h2 class="mb-4 text-center">Enroll in <?= htmlspecialchars($course['title']) ?></h2>
          
          <form method="POST" class="enrollment-form">
            <input type="hidden" name="course_id" value="<?= $courseId ?>">

            <div class="mb-3">
              <label class="form-label">Full Name</label>
              <input type="text" class="form-control rounded-3" name="full_name" required
                value="<?= htmlspecialchars($_SESSION['user_fullname'] ?? '') ?>">
            </div>

            <div class="mb-3">
              <label class="form-label">Contact Number</label>
              <input type="tel" class="form-control rounded-3" name="contact" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Payment Method</label>
              <select name="payment_method" class="form-select rounded-3" required>
                <option value="">Select Method</option>
                <option value="jazzcash">JazzCash</option>
                <option value="easypaisa">EasyPaisa</option>
                <option value="bank_transfer">Bank Transfer</option>
              </select>
            </div>

            <div class="mb-4">
              <label class="form-label">Transaction ID</label>
              <input type="text" class="form-control rounded-3" name="transaction_id" required>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-lg rounded-3 shadow-sm">Complete Enrollment</button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<?php

ob_end_flush();
?>