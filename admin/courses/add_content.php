<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

if (!isset($_GET['course_id']) || !is_numeric($_GET['course_id'])) {
    header('Location: manage_courses.php');
    exit;
}

$courseId = $_GET['course_id'];

// Verify course exists
$courseStmt = $db->prepare("SELECT * FROM courses WHERE course_id = ?");
$courseStmt->execute([$courseId]);
$course = $courseStmt->fetch();

if (!$course) {
    header('Location: manage_courses.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $sequence = (int)$_POST['sequence'];
    
    // Handle video upload
    $videoPath = null;
    if (!empty($_POST['video_url'])) {
        $videoPath = trim($_POST['video_url']);
    } elseif (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/course_videos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $extension = pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('video_') . '.' . $extension;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['video_file']['tmp_name'], $destination)) {
            $videoPath = 'uploads/course_videos/' . $filename;
        }
    }
    
    // Handle document upload
    $documentPath = null;
    if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/course_documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $extension = pathinfo($_FILES['document_file']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('doc_') . '.' . $extension;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['document_file']['tmp_name'], $destination)) {
            $documentPath = 'uploads/course_documents/' . $filename;
        }
    }

    // Insert content into database
    $stmt = $db->prepare("INSERT INTO course_content (course_id, title, description, video_url, document_url, sequence_number) 
                         VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$courseId, $title, $description, $videoPath, $documentPath, $sequence]);

    $_SESSION['message'] = "Content added successfully!";
    header("Location: manage_content.php?course_id=$courseId");
    exit;
}

// Get next sequence number
$sequenceStmt = $db->prepare("SELECT MAX(sequence_number) FROM course_content WHERE course_id = ?");
$sequenceStmt->execute([$courseId]);
$nextSequence = $sequenceStmt->fetchColumn() + 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course Content - Khairulkhan Academy</title>
  </head>
<body>
    <div class="container-fluid">
  <div class="row">
    
    <nav class="col-md-2 d-none d-md-block p-0 bg-dark sidebar" id="sidebar">
      <?php include '../includes/sidebar.php'; ?>
    </nav>
        <main class="col-md-10 ms-sm-auto p-4">

            <div >
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Add Content to: <?= htmlspecialchars($course['title']) ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title*</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="sequence" class="form-label">Sequence Number*</label>
                                <input type="number" class="form-control" id="sequence" name="sequence" 
                                       value="<?= $nextSequence ?>" min="1" required>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header">Video Content</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="video_url" class="form-label">YouTube/Vimeo URL</label>
                                        <input type="url" class="form-control" id="video_url" name="video_url" 
                                               placeholder="https://www.youtube.com/watch?v=...">
                                    </div>
                                    <div class="mb-3">
                                        <label for="video_file" class="form-label">Or Upload Video File</label>
                                        <input type="file" class="form-control" id="video_file" name="video_file" accept="video/*">
                                        <small class="text-muted">Max 50MB (MP4 recommended)</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header">Document/Notes</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="document_file" class="form-label">Upload Document</label>
                                        <input type="file" class="form-control" id="document_file" name="document_file" 
                                               accept=".pdf,.doc,.docx,.txt">
                                        <small class="text-muted">PDF, Word, or Text files</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="manage_content.php?course_id=<?= $courseId ?>" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Add Content</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
</main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>