<?php include './includes/header.php'; ?>

<style>
    :root {
        --primary-color:#473d32;
        --secondary-color:#ff9800;
        --text-dark: #2C3E50;
        --text-light: #F8F9FA;
        --bg-light: #F8F9FA;
    }

    .header {
        background: var(--primary-color);
        color: var(--text-light);
        padding: 4rem 2rem;
        text-align: center;
        margin-bottom: 3rem;
        border-radius: 0 0 30px 30px;
    }

    .header h1 {
        font-size: 2.8rem;
        margin-bottom: 1rem;
        font-weight: 700;
    }

    .contact-form {
        background: var(--bg-light);
        padding: 2.5rem;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        position: relative;
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

    .submit-btn {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 1rem 2.5rem;
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
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(42,93,103,0.2);
    }

    .info-card {
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        border-left: 4px solid var(--primary-color);
        background: white;
    }

    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .icon-circle {
        width: 60px;
        height: 60px;
        background: rgba(42,93,103,0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        color: var(--primary-color);
        font-size: 1.5rem;
    }

    .map-container iframe {
        width: 100%;
        height: 400px;
        border: none;
        border-radius: 15px;
    }
</style>

<div class="container">
    <header class="header">
        <h1>Contact Us</h1>
        <p>We'd love to hear from you! Reach out with your questions or feedback.</p>
    </header>

    <div class="row g-5">
        <!-- Contact Form -->
        <div class="col-lg-12" data-aos="fade-in">
            <div class="contact-form">
                <form id="contactForm">
                    <div class="mb-4">
                        <label for="name" class="form-label">Your Name</label>
                        <input type="text" id="name" name="name" class="form-control" required />
                    </div>
                    <div class="mb-4">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" required />
                    </div>
                    <div class="mb-4">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-control" required />
                    </div>
                    <div class="mb-4">
                        <label for="message" class="form-label">Your Message</label>
                        <textarea id="message" name="message" rows="5" class="form-control" required></textarea>
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
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.215209179535!2d-73.98784492401796!3d40.74844097138989!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c259a9b3117469%3A0xd134e199a405a163!2sEmpire%20State%20Building!5e0!3m2!1sen!2sus!4v1629999999999!5m2!1sen!2sus"
            loading="lazy"
            allowfullscreen>
        </iframe>
    </div>
</div>

<?php include './includes/footer.php'; ?>
