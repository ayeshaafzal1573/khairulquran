<?php
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$teacher_id = $_SESSION['user_id'];
$upload_dir = '../uploads/';  // Make sure this folder exists and is writable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['name'] ?? '';
    $contact_number = $_POST['contact'] ?? '';
    $bio = $_POST['bio'] ?? '';

    // Handle file upload
    $profile_image = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['profile_image']['tmp_name'];
        $file_name = basename($_FILES['profile_image']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_ext, $allowed_ext)) {
            // Rename file to avoid conflicts (optional)
            $new_file_name = 'teacher_' . $teacher_id . '_' . time() . '.' . $file_ext;
            $destination = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $destination)) {
                $profile_image = $new_file_name;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
    }

    if (!$full_name) {
        $error = "Name is required.";
    }

    if (empty($error)) {
        if ($profile_image) {
            // Update with new profile image
            $stmt = $db->prepare("UPDATE teachers SET full_name = ?, contact_number = ?, bio = ?, profile_image = ? WHERE user_id = ?");
            $stmt->execute([$full_name, $contact_number, $bio, $profile_image, $teacher_id]);
        } else {
            // Update without changing profile image
            $stmt = $db->prepare("UPDATE teachers SET full_name = ?, contact_number = ?, bio = ? WHERE user_id = ?");
            $stmt->execute([$full_name, $contact_number, $bio, $teacher_id]);
        }
        $success = "Profile updated successfully.";
    }
}

// Fetch current data
$stmt = $db->prepare("SELECT full_name, contact_number, bio, profile_image FROM teachers WHERE user_id = ?");
$stmt->execute([$teacher_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: /khairulquran/login.php');
    exit;
}

$teacher_id = $_SESSION['user_id'];

// Get teacher details from both users and teachers tables
$sql = "SELECT u.username, u.email, t.full_name, t.profile_image 
        FROM users u
        JOIN teachers t ON u.user_id = t.user_id
        WHERE u.user_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$teacher_id]);
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #3a7bd5, #00d2ff);
            color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 5px;
            border-radius: 5px;
            padding: 10px 15px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .main-content {
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .welcome-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .profile-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .default-profile {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            color: #777;
            font-size: 24px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
            <div class="position-sticky pt-3">
                <div class="text-center mb-4">
                    <?php if (!empty($teacher['profile_image'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($teacher['profile_image']) ?>" alt="Profile" class="profile-img mb-2">
                    <?php else: ?>
                        <div class="default-profile">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                    <h5><?php echo htmlspecialchars($teacher['full_name'] ?? $teacher['username']); ?></h5>
                    <p class="text-white-50">Teacher</p>
                </div>
                
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_courses.php">
                            <i class="fas fa-book-open"></i> My Courses
                        </a>
                    </li>
                  
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                            <i class="fas fa-user-cog"></i> Profile Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../includes/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
                 <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
     <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Profile Settings</h1>
            </div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="name" class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($result['full_name']) ?>" required>
    </div>
    <div class="mb-3">
        <label for="contact" class="form-label">Contact Number</label>
        <input type="text" name="contact" class="form-control" value="<?= htmlspecialchars($result['contact_number']) ?>">
    </div>
    <div class="mb-3">
        <label for="bio" class="form-label">Bio</label>
        <textarea name="bio" class="form-control" rows="4"><?= htmlspecialchars($result['bio']) ?></textarea>
    </div>
    <div class="mb-3">
        <label for="profile_image" class="form-label">Profile Image</label><br>
        <?php if (!empty($result['profile_image'])): ?>
            <img src="../uploads/<?= htmlspecialchars($result['profile_image']) ?>" alt="Profile Image" style="max-width:150px; max-height:150px; display:block; margin-bottom:10px;">
        <?php else: ?>
            <p>No profile image uploaded.</p>
        <?php endif; ?>
        <input type="file" name="profile_image" accept="image/*" class="form-control">
        <small class="form-text text-muted">Allowed types: jpg, jpeg, png, gif</small>
    </div>

    <button type="submit" class="btn btn-primary">Update Profile</button>
</form>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


