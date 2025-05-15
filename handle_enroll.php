<?php
require_once './includes/config.php';
require_once './includes/auth.php';

if (!isLoggedIn()) {
    $_SESSION['error'] = "Please login to enroll.";
    header("Location: login.php");
    exit;
}

if (!isStudent()) {
    $_SESSION['error'] = "Only students can enroll in courses.";
    header("Location: index.php");
    exit;
}

if (isset($_GET['course_id'])) {
    $courseId = intval($_GET['course_id']);
    header("Location: enroll.php?course_id=$courseId");
    exit;
} else {
    $_SESSION['error'] = "Invalid course ID.";
    header("Location: index.php");
    exit;
}
?>
