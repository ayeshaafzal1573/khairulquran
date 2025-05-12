<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

// Check if content ID is provided and valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_courses.php');
    exit;
}

$contentId = $_GET['id'];

// Get content details
$stmt = $db->prepare("SELECT cc.*, c.course_id, c.title AS course_title 
                     FROM course_content cc
                     JOIN courses c ON cc.course_id = c.course_id
                     WHERE cc.content_id = ?");
$stmt->execute([$contentId]);
$content = $stmt->fetch();

if (!$content) {
    header('Location: manage_courses.php');
    exit;
}

$courseId = $content['course_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $sequence = (int)$_POST['sequence'];
    
    // Handle video update
    $videoPath = $content['video_url'];
    if (!empty($_POST['video_url'])) {
        $videoPath = trim($_POST['video_url']);
    } elseif (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/course_videos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Delete old video if exists
        if ($videoPath && file_exists('../../' . $videoPath)) {
            unlink('../../' . $videoPath);
        }
        
        $extension = pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('video_') . '.' . $extension;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['video_file']['tmp_name'], $destination)) {
            $videoPath = 'uploads/course_videos/' . $filename;
        }
    }
    
    // Handle document update
    $documentPath = $content['document_url'];
    if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/course_documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Delete old document if exists
        if ($documentPath && file_exists('../../' . $documentPath)) {
            unlink('../../' . $documentPath);
        }
        
        $extension = pathinfo($_FILES['document_file']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('doc_') . '.' . $extension;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['document_file']['tmp_name'], $destination)) {
            $documentPath = 'uploads/course_documents/' . $filename;
        }
    } elseif (isset($_POST['remove_document']) && $_POST['remove_document'] == '1') {
        // Remove document if requested
        if ($documentPath && file_exists('../../' . $documentPath)) {
            unlink('../../' . $documentPath);
        }
        $documentPath = null;
    }

    // Update content in database
    $updateStmt = $db->prepare("UPDATE course_content SET 
                               title = ?, 
                               description = ?, 
                               video_url = ?, 
                               document_url = ?, 
                               sequence_number = ?
                               WHERE content_id = ?");
    $updateStmt->execute([$title, $description, $videoPath, $documentPath, $sequence, $contentId]);

    $_SESSION['message'] = "Content updated successfully!";
    header("Location: manage_content.php?course_id=$courseId");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Content - Khairulkhan Academy</title>
   </head>
<body>
     <div class="container-fluid">
  <div class="row">
    
    <nav class="col-md-2 d-none d-md-block p-0 bg-dark sidebar" id="sidebar">
      <?php include '../includes/sidebar.php'; ?>
    </nav>
        <main class="col-md-10 ms-sm-auto p-4">
            <div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Edit Content: <?= htmlspecialchars($content['title']) ?></h5>
                        <p class="mb-0 text-muted">Course: <?= htmlspecialchars($content['course_title']) ?></p>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title*</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?= htmlspecialchars($content['title']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="3"><?= htmlspecialchars($content['description']) ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="sequence" class="form-label">Sequence Number*</label>
                                <input type="number" class="form-control" id="sequence" name="sequence" 
                                       value="<?= $content['sequence_number'] ?>" min="1" required>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header">Video Content</div>
                                <div class="card-body">
                                    <?php if ($content['video_url']): ?>
                                        <?php if (strpos($content['video_url'], 'youtube.com') !== false || strpos($content['video_url'], 'vimeo.com') !== false): ?>
                                            <div class="mb-3">
                                                <label class="form-label">Current Video URL</label>
                                                <p><a href="<?= htmlspecialchars($content['video_url']) ?>" target="_blank"><?= htmlspecialchars($content['video_url']) ?></a></p>
                                            </div>
                                        <?php else: ?>
                                            <div class="mb-3">
                                                <label class="form-label">Current Video File</label>
                                                <p>
                                                    <a href="../../<?= htmlspecialchars($content['video_url']) ?>" target="_blank">View Video</a>
                                                    <span class="text-muted ms-2">(<?= round(filesize('../../' . $content['video_url']) / (1024 * 1024), 2) ?> MB)</span>
                                                </p>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <label for="video_url" class="form-label">YouTube/Vimeo URL</label>
                                        <input type="url" class="form-control" id="video_url" name="video_url" 
                                               placeholder="https://www.youtube.com/watch?v=..."
                                               value="<?= htmlspecialchars($content['video_url']) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="video_file" class="form-label">Or Upload New Video File</label>
                                        <input type="file" class="form-control" id="video_file" name="video_file" accept="video/*">
                                        <small class="text-muted">Max 50MB (MP4 recommended). Will replace existing video.</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header">Document/Notes</div>
                                <div class="card-body">
                                    <?php if ($content['document_url']): ?>
                                        <div class="mb-3">
                                            <label class="form-label">Current Document</label>
                                            <p>
                                                <a href="../../<?= htmlspecialchars($content['document_url']) ?>" target="_blank">Download Document</a>
                                                <span class="text-muted ms-2">(<?= round(filesize('../../' . $content['document_url']) / 1024, 2) ?> KB)</span>
                                            </p>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="remove_document" name="remove_document" value="1">
                                                <label class="form-check-label" for="remove_document">Remove current document</label>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <label for="document_file" class="form-label">Upload New Document</label>
                                        <input type="file" class="form-control" id="document_file" name="document_file" 
                                               accept=".pdf,.doc,.docx,.txt">
                                        <small class="text-muted">PDF, Word, or Text files. Will replace existing document.</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="manage_content.php?course_id=<?= $courseId ?>" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Back to Content
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                             
            </div>
                   </main>
        </div>
    </div>

    <script>
        // Disable video URL field if video file is selected and vice versa
        document.getElementById('video_file').addEventListener('change', function() {
            if (this.files.length > 0) {
                document.getElementById('video_url').value = '';
                document.getElementById('video_url').disabled = true;
            } else {
                document.getElementById('video_url').disabled = false;
            }
        });
        
        document.getElementById('video_url').addEventListener('input', function() {
            if (this.value.trim() !== '') {
                document.getElementById('video_file').value = '';
                document.getElementById('video_file').disabled = true;
            } else {
                document.getElementById('video_file').disabled = false;
            }
        });
        
        // Initialize based on current state
        if (document.getElementById('video_url').value.trim() !== '') {
            document.getElementById('video_file').disabled = true;
        }
    </script>
</body>
</html>