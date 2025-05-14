<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

if (!isset($_GET['course_id']) || !is_numeric($_GET['course_id'])) {
    header('Location: manage_courses.php');
    exit;
}

$courseId = $_GET['course_id'];

// Get course details
$courseStmt = $db->prepare("SELECT * FROM courses WHERE course_id = ?");
$courseStmt->execute([$courseId]);
$course = $courseStmt->fetch();

if (!$course) {
    header('Location: manage_courses.php');
    exit;
}

// Get course content
$contentStmt = $db->prepare("SELECT * FROM course_content WHERE course_id = ? ORDER BY sequence_number");
$contentStmt->execute([$courseId]);
$contents = $contentStmt->fetchAll();

// Handle content deletion
if (isset($_GET['delete_content']) && is_numeric($_GET['delete_content'])) {
    $contentId = $_GET['delete_content'];
    $deleteStmt = $db->prepare("DELETE FROM course_content WHERE content_id = ?");
    $deleteStmt->execute([$contentId]);
    $_SESSION['message'] = "Content deleted successfully";
    header("Location: manage_content.php?course_id=$courseId");
    exit;
}

// Handle content reordering
if (isset($_POST['reorder'])) {
    foreach ($_POST['sequence'] as $contentId => $sequence) {
        $stmt = $db->prepare("UPDATE course_content SET sequence_number = ? WHERE content_id = ?");
        $stmt->execute([$sequence, $contentId]);
    }
    $_SESSION['message'] = "Content order updated successfully";
    header("Location: manage_content.php?course_id=$courseId");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Course Content - Khair-ul-Quran Academy</title>
  </head>
<body>
    <div class="container-fluid">
  <div class="row">
    
    <nav class="col-md-2 d-none d-md-block p-0 bg-dark sidebar" id="sidebar">
      <?php include '../includes/sidebar.php'; ?>
    </nav>
        <main class="col-md-10 ms-sm-auto p-4">

            <div >
                <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message']); endif; ?>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Manage Content: <?= htmlspecialchars($course['title']) ?></h5>
                        <div>
                            <a href="add_content.php?course_id=<?= $courseId ?>" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Add Content
                            </a>
                            <a href="manage_courses.php" class="btn btn-secondary btn-sm">
                                <i class="bi bi-arrow-left me-1"></i> Back to Courses
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($contents)): ?>
                        <div class="alert alert-info">No content added yet for this course.</div>
                        <?php else: ?>
                        <form method="POST">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="50">#</th>
                                            <th>Title</th>
                                            <th>Type</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="sortable">
                                        <?php foreach ($contents as $content): ?>
                                        <tr>
                                            <td>
                                                <input type="hidden" name="sequence[<?= $content['content_id'] ?>]" value="<?= $content['sequence_number'] ?>">
                                                <?= $content['sequence_number'] ?>
                                            </td>
                                            <td><?= htmlspecialchars($content['title']) ?></td>
                                            <td>
                                                <?php if ($content['video_url']): ?>
                                                <span class="badge bg-danger">Video</span>
                                                <?php endif; ?>
                                                <?php if ($content['document_url']): ?>
                                                <span class="badge bg-primary">Document</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="edit_content.php?id=<?= $content['content_id'] ?>" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="?course_id=<?= $courseId ?>&delete_content=<?= $content['content_id'] ?>" 
                                                   class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <button type="submit" name="reorder" class="btn btn-success">
                                    <i class="bi bi-check-circle me-1"></i> Save Order
                                </button>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
                                                </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
    <script>
        $(function() {
            $(".sortable").sortable({
                update: function(event, ui) {
                    $(this).find('tr').each(function(index) {
                        $(this).find('input[name^="sequence"]').val(index + 1);
                    });
                }
            });
            $(".sortable").disableSelection();
        });
    </script>
</body>
</html>