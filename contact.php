<?php
ob_start();
session_start();
require_once 'includes/config.php';


// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    // Validate inputs
    $errors = [];
    if (empty($name)) $errors[] = "Name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($subject)) $errors[] = "Subject is required";
    if (empty($message)) $errors[] = "Message is required";

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);

            $_SESSION['success'] = "Your message has been sent successfully!";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error sending message: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = implode("<br>", $errors);
    }
}

include './includes/header.php';
?>

<style>
    :root {
        --primary-color: #473d32;
        --secondary-color: #ff9800;
        --text-dark: #2C3E50;
        --text-light: #F8F9FA;
        --bg-light: #F8F9FA;
    }

    .header {
        background: linear-gradient(135deg, var(--primary-color), #3a3228);
        color: var(--text-light);
        padding: 4rem 2rem;
        text-align: center;
        margin-bottom: 3rem;
        border-radius: 0 0 30px 30px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .header h1 {
        font-size: 2.8rem;
        margin-bottom: 1rem;
        font-weight: 700;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
    }

    .contact-form {
        background: var(--bg-light);
        padding: 2.5rem;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .contact-form::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    }

    .form-control {
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        transition: all 0.3s;
    }

    .form-control:focus {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 3px rgba(255, 152, 0, 0.2);
    }

    .submit-btn {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.8rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 7px 14px rgba(0, 0, 0, 0.15);
    }

    .map-container {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        margin: 3rem 0;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Alert Messages */
    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }
</style>

<div class="container">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success'];
            unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error'];
            unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <header class="header">
        <h1>Contact Us</h1>
        <p>We'd love to hear from you! Reach out with your questions or feedback.</p>
    </header>

    <div class="row g-5">
        <!-- Contact Form -->
        <div class="col-lg-12" data-aos="fade-in">
            <div class="contact-form">
                <form id="contactForm" method="POST">
                    <div class="mb-4">
                        <label for="name" class="form-label">Your Name</label>
                        <input type="text" id="name" name="name" class="form-control" required
                            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" required
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="mb-4">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-control" required
                            value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
                    </div>
                    <div class="mb-4">
                        <label for="message" class="form-label">Your Message</label>
                        <textarea id="message" name="message" rows="5" class="form-control" required><?=
                                                                                                        htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="submit-btn">
                        Send Message <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="map-container my-5" data-aos="fade-out">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14478.204672071332!2d67.16002818203154!3d24.879174563165268!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3eb339f3256a595f%3A0xaccc6a871948b0e5!2sShamsi%20Society%20Shah%20Faisal%20Colony%2C%20Shah%20Faisal%20Town%2C%20Pakistan!5e0!3m2!1sen!2sus!4v1747897352916!5m2!1sen!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</div>

<?php include './includes/footer.php'; ?>



<?php

ob_end_flush();
?>