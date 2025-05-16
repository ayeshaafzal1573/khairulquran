  <?php
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

  // In your form, you can still keep full_name if you want to collect it, but do NOT insert it to DB if column missing

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name']);  
    $contact = trim($_POST['contact']);
    $paymentMethod = $_POST['payment_method'];
    $transactionId = trim($_POST['transaction_id']);
    $courseId = $_POST['course_id'];
    $userId = $_SESSION['user_id'];

    // Pehle students table se student_id nikalain jiska user_id $userId ho
    $stmtStudent = $db->prepare("SELECT student_id FROM students WHERE user_id = ?");
    $stmtStudent->execute([$userId]);
    $student = $stmtStudent->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        $_SESSION['error'] = "Student profile nahi mila, admin se rabta karein.";
        header("Location: courses.php");
        exit();
    }

    $studentId = $student['student_id'];

    // Ab enrollments table mein sahi student_id insert karain
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

  <div class="container">
    <h1>Enroll in <?= htmlspecialchars($course['title']) ?></h1>
    
    <form method="POST" class="enrollment-form">
      <input type="hidden" name="course_id" value="<?= $courseId ?>">
      
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="full_name" required 
              value="<?= htmlspecialchars($_SESSION['user_fullname'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Contact Number</label>
        <input type="tel" name="contact" required>
      </div>

      <div class="form-group">
        <label>Payment Method</label>
        <select name="payment_method" required>
          <option value="jazzcash">JazzCash</option>
          <option value="easypaisa">EasyPaisa</option>
          <option value="bank_transfer">Bank Transfer</option>
        </select>
      </div>

      <div class="form-group">
        <label>Transaction ID</label>
        <input type="text" name="transaction_id" required>
      </div>

      <button type="submit" class="btn btn-primary">Complete Enrollment</button>
    </form>
  </div>

  <?php require_once 'includes/footer.php'; ?>