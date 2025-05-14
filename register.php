<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';


$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $role = $_POST['role'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);
    $fullName = trim($_POST['full_name']);
    $contact = trim($_POST['contact_number']);
    
    // Teacher specific fields
    $specialization = trim($_POST['specialization'] ?? '');
    $qualifications = trim($_POST['qualifications'] ?? '');
    
    // Student specific fields
    $parentName = trim($_POST['parent_name'] ?? '');
    $parentContact = trim($_POST['parent_contact'] ?? '');

    // Common validations
    if (empty($username)) $errors[] = 'Username is required';
    if (empty($email)) $errors[] = 'Email is required';
    if (empty($password)) $errors[] = 'Password is required';
    if ($password !== $confirmPassword) $errors[] = 'Passwords do not match';
    if (empty($fullName)) $errors[] = 'Full name is required';
    if (!in_array($role, ['student', 'teacher'])) $errors[] = 'Invalid role selected';

    // Role-specific validations
    if ($role === 'teacher') {
        if (empty($specialization)) $errors[] = 'Specialization is required';
    } else {
        if (empty($parentName)) $errors[] = 'Parent/Guardian name is required';
    }

    // Check existing user
    $stmt = $db->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) $errors[] = 'Username or email already exists';

    if (empty($errors)) {
        try {
            $db->beginTransaction();

            // Create user account
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $userStmt = $db->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $userStmt->execute([$username, $email, $hashedPassword, $role]);
            $userId = $db->lastInsertId();

            // Create role-specific profile
            if ($role === 'teacher') {
                $profileStmt = $db->prepare("INSERT INTO teachers (user_id, full_name, contact_number, specialization, qualifications) VALUES (?, ?, ?, ?, ?)");
                $profileStmt->execute([$userId, $fullName, $contact, $specialization, $qualifications]);
            } else {
                $profileStmt = $db->prepare("INSERT INTO students (user_id, full_name, contact_number, parent_name, parent_contact) VALUES (?, ?, ?, ?, ?)");
                $profileStmt->execute([$userId, $fullName, $contact, $parentName, $parentContact]);
            }

            $db->commit();
            $_SESSION['success'] = 'Registration successful! Please login.';
            header('Location: login.php');
            exit;
        } catch (PDOException $e) {
            $db->rollBack();
            $errors[] = 'Registration failed: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Khair-ul-Quran Academy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
 body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: url('./assets/images/login.jpg') no-repeat center center fixed;
    background-size: cover;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card {
     background: rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
}

.card h3 {
    font-weight: 600;
    color: #000;
}

.form-label {
    font-weight: 500;
    color: #000;
}

.form-control {
    border-radius: 8px;
}

  .btn-register{
            border-radius: 10px;
            background-color: #473d32;
            font-weight: bold;
            color:white;
        }


.alert-danger {
    border-radius: 8px;
    padding: 10px 15px;
}
a{
    text-decoration:none;
color:#ff9800;

}
</style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h3 class="mt-1">Register Yourself in Our Academy</h3>

                        </div>

                        <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <form method="POST" id="registrationForm">
                            <div class="mb-1">
                                <label class="form-label">I am registering as:</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="role" id="studentRole" 
                                               value="student" <?= ($_POST['role'] ?? '') === 'student' ? 'checked' : '' ?> required>
                                        <label class="form-check-label" for="studentRole">Student</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="role" id="teacherRole" 
                                               value="teacher" <?= ($_POST['role'] ?? '') === 'teacher' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="teacherRole">Teacher</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username:</label>
                                        <input type="text" class="form-control" id="username" name="username" 
                                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email:</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                           <label for="full_name" class="form-label">Full Name:</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
                     
                                                </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                         <label for="password" class="form-label">Password:</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                </div>
                            </div>


                            <div class="mb-3">
                                <label for="contact_number" class="form-label">Contact Number:</label>
                                <input type="tel" class="form-control" id="contact_number" name="contact_number" 
                                       value="<?= htmlspecialchars($_POST['contact_number'] ?? '') ?>" required>
                            </div>

                            <!-- Teacher Specific Fields -->
                            <div id="teacherFields" style="display: none;">
                                <div class="mb-3">
                                    <label for="specialization" class="form-label">Specialization:</label>
                                    <input type="text" class="form-control" id="specialization" name="specialization" 
                                           value="<?= htmlspecialchars($_POST['specialization'] ?? '') ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="qualifications" class="form-label">Qualifications:</label>
                                    <textarea class="form-control" id="qualifications" name="qualifications" rows="2"><?= htmlspecialchars($_POST['qualifications'] ?? '') ?></textarea>
                                </div>
                            </div>

                            <!-- Student Specific Fields -->
                            <div id="studentFields" style="display: none;">
                                <div class="mb-3">
                                    <label for="parent_name" class="form-label">Parent/Guardian Name*</label>
                                    <input type="text" class="form-control" id="parent_name" name="parent_name" 
                                           value="<?= htmlspecialchars($_POST['parent_name'] ?? '') ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="parent_contact" class="form-label">Parent/Guardian Contact*</label>
                                    <input type="tel" class="form-control" id="parent_contact" name="parent_contact" 
                                           value="<?= htmlspecialchars($_POST['parent_contact'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-register">Register</button>
                                <div class="text-center">
                                    Already have an account? <a href="login.php">Login here</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Show/hide role-specific fields
    document.addEventListener('DOMContentLoaded', function() {
        const roleRadios = document.querySelectorAll('input[name="role"]');
        const teacherFields = document.getElementById('teacherFields');
        const studentFields = document.getElementById('studentFields');

        function toggleFields() {
            const role = document.querySelector('input[name="role"]:checked')?.value;
            teacherFields.style.display = role === 'teacher' ? 'block' : 'none';
            studentFields.style.display = role === 'student' ? 'block' : 'none';
            
            // Toggle required attributes
            document.getElementById('specialization').required = role === 'teacher';
            document.getElementById('parent_name').required = role === 'student';
            document.getElementById('parent_contact').required = role === 'student';
        }

        roleRadios.forEach(radio => {
            radio.addEventListener('change', toggleFields);
        });

        // Initial check
        toggleFields();
    });
    </script>
</body>
</html>