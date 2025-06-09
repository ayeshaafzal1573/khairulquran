<?php
ob_start();
session_start();

require_once 'includes/header.php';
require_once 'includes/auth.php';
require_once 'includes/config.php';

// Check course ID parameter
if (!isset($_GET['course_id']) || !is_numeric($_GET['course_id'])) {
    $_SESSION['error'] = "Invalid course selection";
    header("Location: courses.php");
    exit();
}

$courseId = (int)$_GET['course_id'];

// Authentication check
if (!isLoggedIn()) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    $_SESSION['error'] = "Please login to enroll in courses";
    header("Location: login.php");
    exit();
}

if (!isStudent()) {
    $_SESSION['error'] = "Only students can enroll in courses";
    header("Location: courses.php");
    exit();
}

// Get course details
$stmt = $db->prepare("SELECT * FROM courses WHERE course_id = ?");
$stmt->execute([$courseId]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    $_SESSION['error'] = "Course not found";
    header("Location: courses.php");
    exit();
}

// Get student details
$userId = $_SESSION['user_id'];
$stmtStudent = $db->prepare("SELECT * FROM students WHERE user_id = ?");
$stmtStudent->execute([$userId]);
$student = $stmtStudent->fetch(PDO::FETCH_ASSOC);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $fullName = trim($_POST['full_name']);
    $contact = trim($_POST['contact']);
$paymentMethod = $_POST['payment_method'] ?? 'N/A';
$transactionId = trim($_POST['transaction_id'] ?? 'Not Available');

    
    // Basic validation
    if (empty($fullName)) {
        $errors[] = "Full name is required";
    }
    
    if (empty($contact)) {
        $errors[] = "Contact number is required";
    }
  
    
    // If no errors, process enrollment
    if (empty($errors)) {
        try {
            $db->beginTransaction();
            
            // Insert enrollment record
        $insertStmt = $db->prepare("
    INSERT INTO enrollments 
    (student_id, course_id, enrollment_date, payment_status, transaction_id)
    VALUES (?, ?, NOW(), 'pending', ?)
");
$success = $insertStmt->execute([
    $student['student_id'],
    $courseId,
    $transactionId 
]);

            
            if ($success) {
                $db->commit();
                $_SESSION['success'] = "Enrollment successful!";
                header("Location: student/dashboard.php");
                exit();
            } else {
                $db->rollBack();
                $errors[] = "Failed to complete enrollment. Please try again.";
            }
        } catch (PDOException $e) {
            $db->rollBack();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!-- HTML Form remains the same as your original -->
<div class="container py-5">
    <!-- Display error messages if any -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <h2 class="mb-4 text-center">Enroll in <?= htmlspecialchars($course['title']) ?></h2>
                    <p class="text-center mb-4">Course Duration: <?= htmlspecialchars($course['duration']) ?></p>
                    
                    <form method="POST" class="enrollment-form">
                        <input type="hidden" name="course_id" value="<?= $courseId ?>">

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control rounded-3" name="full_name" required disabled
                                value="<?= htmlspecialchars($student['full_name'] ?? $_SESSION['user_fullname'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="tel" class="form-control rounded-3" name="contact" required
                                value="<?= htmlspecialchars($student['phone'] ?? '') ?>">
                        </div>

                        <div class="mb-3" hidden>
                            <label class="form-label">Payment Method</label>
                            <input type="text" name="payment_method" value="N/A">
                           
                        </div>

                        <div class="mb-4" hidden>
                            <label class="form-label">Transaction ID</label>
                          <input type="text" class="form-control rounded-3" name="transaction_id" value="Not Available">

                                              </div>

                        <div class="d-grid">
                            <button type="submit" class="enroll-btn">Complete Enrollment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
<?php ob_end_flush(); ?>