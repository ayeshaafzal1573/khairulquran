<?php 
ob_start();
session_start();
include './includes/header.php'; 

?>
<?php
require_once './includes/config.php';
require_once './includes/auth.php';
?>
<style>
    :root {
        --primary-color: #ff9800;
        --secondary-color: #473d32;
        --accent-color: #ff9800;
        --text-color: #333;
        --light-text: #666;
        --light-bg: #f9f9f9;
        --white: #fff;
        --border-radius: 8px;
        --box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s ease;
    }

    .hero {
        padding: 40px 0 60px;
        position: relative;
        overflow: hidden;
    }

    .hero .container {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .hero-content {
        flex: 1;
        max-width: 600px;
    }

    .hero-content h1 {
        font-size: 48px;
        margin-bottom: 20px;
        color: var(--secondary-color);
        line-height: 1.3;
    }

    .hero-content p {
        font-size: 18px;
        color: var(--light-text);
        margin-bottom: 30px;
    }

    .hero-buttons {
        display: flex;
        gap: 15px;
    }

    .hero-image {
        flex: 1;
        text-align: center;
    }

    .hero-image img {
        max-width: 90%;
        height: auto;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-20px);
        }

        100% {
            transform: translateY(0px);
        }
    }

    /* Features Section */
    .features {
        background: var(--white);
    }

    .features .container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
    }

    .feature-card {
        background: var(--white);
        padding: 30px;
        border-radius: var(--border-radius);
        text-align: center;
        box-shadow: var(--box-shadow);
        transition: var(--transition);
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .feature-card i {
        font-size: 50px;
        color: var(--primary-color);
        margin-bottom: 20px;
    }

    .feature-card h3 {
        font-size: 22px;
        margin-bottom: 15px;
    }

    .feature-card p {
        color: var(--light-text);
    }

    /* Courses Section */
    .courses {
        background-color: #f9f9f9;
        padding: 5rem 0;
        position: relative;
        overflow: hidden;
    }

    .courses::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('https://example.com/quran-pattern.png') center/cover no-repeat;
        opacity: 0.03;
        z-index: 0;
    }

    .section-header h2.text-gradient {
        background: #ff9800;
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        font-weight: 700;
    }

    .divider {
        width: 80px;
        height: 4px;
        background:#ff9800;
        margin: 1rem auto;
        border-radius: 2px;
    }

    /* Course Card Styling */
    .card {
        border-radius: 12px;
        transition: all 0.3s ease;
        background: white;
        position: relative;
        z-index: 1;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
    }

    .card-image-container {
        position: relative;
        overflow: hidden;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .card-img-top {
        height: 200px;
        width: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .card:hover .card-img-top {
        transform: scale(1.05);
    }

    .course-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #473d32;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .teacher-badge {
        background: #f0f4ff;
        color:brown;
        padding: 3px 8px;
        border-radius: 20px;
        font-size: 0.75rem;
        white-space: nowrap;
        margin-left: 10px;
    }

    /* Enroll Button Styling */
    .enroll-btn {
        display: inline-block;
        background: #ff9800;
        color: white !important;
        padding: 8px 20px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none !important;
        transition: all 0.3s ease;
        border: none;
    }

    .enroll-btn:hover {
        transform: translateY(-2px);
        background:#ff9800;
    }

    /* Filter Buttons */
    .btn-group-pill .btn {
        border-radius: 50px !important;
        margin: 0 5px;
    }

    .filter-btn {
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .filter-btn.active {
        background: #473d32 !important;
        border-color: transparent !important;
    }

    /* Empty State */
    .empty-state {
        padding: 2rem;
        background: rgba(255, 255, 255, 0.7);
        border-radius: 12px;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .course-filters .btn-group {
            flex-wrap: wrap;
            justify-content: center;
        }

        .filter-btn {
            margin: 5px;
        }

        .card {
            margin-bottom: 20px;
        }
    }

    /* Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .course-card {
        animation: fadeIn 0.5s ease forwards;
        opacity: 0;
    }

    .course-card:nth-child(1) {
        animation-delay: 0.1s;
    }

    .course-card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .course-card:nth-child(3) {
        animation-delay: 0.3s;
    }

    .course-card:nth-child(4) {
        animation-delay: 0.4s;
    }

    .course-card:nth-child(5) {
        animation-delay: 0.5s;
    }

    .course-card:nth-child(6) {
        animation-delay: 0.6s;
    }

    .course-card:hover{
        background-color: transparent !important;
    }


    /* About Section */
    .about .container {
        display: flex;
        align-items: center;
        gap: 50px;
    }

    .about-image {
        flex: 1;
    }

    .about-image img {
        width: 100%;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
    }

    .about-content {
        flex: 1;
    }

    .about-content h2 {
        font-size: 36px;
        color: var(--secondary-color);
        margin-bottom: 20px;
    }

    .about-content p {
        margin-bottom: 20px;
        color: var(--light-text);
    }

    .about-list {
        list-style: none;
        margin: 25px 0;
    }

    .about-list li {
        margin-bottom: 10px;
        display: flex;
        align-items: center;
    }

    .about-list i {
        color: var(--primary-color);
        margin-right: 10px;
        font-size: 18px;
    }

    /* Teachers Section */
    .teachers {
        background: var(--white);
    }

    .teacher-slider {
        display: flex;
        gap: 30px;
        overflow-x: auto;
        padding-bottom: 20px;
        scrollbar-width: thin;
        scrollbar-color: var(--primary-color) #f1f1f1;
    }

    .teacher-slider::-webkit-scrollbar {
        height: 8px;
    }

    .teacher-slider::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .teacher-slider::-webkit-scrollbar-thumb {
        background: var(--primary-color);
        border-radius: 10px;
    }

    .teacher-card {
        min-width: 280px;
        background: var(--white);
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--box-shadow);
        text-align: center;
        transition: var(--transition);
    }

    .teacher-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .teacher-img {
        height: 250px;
        overflow: hidden;
    }

    .teacher-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: var(--transition);
    }

    .teacher-card:hover .teacher-img img {
        transform: scale(1.1);
    }

    .teacher-info {
        padding: 20px;
    }

    .teacher-info h3 {
        font-size: 20px;
        margin-bottom: 5px;
    }

    .teacher-info p {
        color: var(--light-text);
        font-size: 14px;
        margin-bottom: 15px;
    }

    .teacher-social {
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    .teacher-social a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: #f5f5f5;
        color: var(--primary-color);
        transition: var(--transition);
    }

    .teacher-social a:hover {
        background: var(--primary-color);
        color: var(--white);
    }

    /* Testimonials Section */
    .testimonials {
        background: var(--light-bg);
    }

    .testimonial-slider {
        display: flex;
        gap: 30px;
        overflow-x: auto;
        padding-bottom: 20px;
    }

    .testimonial-card {
        min-width: 350px;
        background: var(--white);
        padding: 30px;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
    }

    .testimonial-content {
        margin-bottom: 20px;
        position: relative;
    }

    .testimonial-content p {
        font-style: italic;
        color: var(--light-text);
        margin-bottom: 20px;
    }

    .testimonial-content::before {
        content: '"';
        position: absolute;
        top: -20px;
        left: -10px;
        font-size: 60px;
        color: rgba(30, 136, 229, 0.1);
        font-family: serif;
        line-height: 1;
    }

    .testimonial-author {
        display: flex;
        align-items: center;
    }

    .testimonial-author img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 15px;
    }

    .author-info h4 {
        font-size: 18px;
        margin-bottom: 5px;
    }

    .author-info p {
        color: var(--light-text);
        font-size: 14px;
    }

    /* CTA Section */
    .cta {
        background: url("assets/images/q4.jpg");
        color: black;
        text-align: center;
        background-repeat: no-repeat;
        padding: 100px 0;
        width: 100%;
        height: 350px;
        background-size: cover;
        justify-content: center;
        display: flex;

    }

    .cta h2 {
        font-size: 36px;
        margin-bottom: 20px;
    }

    .cta p {
        font-size: 18px;
        max-width: 700px;
        margin: 0 auto 30px;

        color: black;
    }

    .cta .btn {
        background-color: black;
        color: white;
        text-decoration: none;
    }
</style>
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Learn Quran Online with Expert Teachers</h1>
            <p>Join thousands of students worldwide learning Quran, Tajweed, Arabic and Islamic studies with our qualified teachers.</p>

        </div>
        <div class="hero-image">
            <img src="./assets/images/course-banner.jpg" alt="Quran Teacher">
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="container">
        <div class="feature-card">
            <i class="fas fa-user-graduate"></i>
            <h3>Certified Teachers</h3>
            <p>Learn from Al-Azhar and Islamic university graduates with Ijazah certification.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-laptop-house"></i>
            <h3>Online Classes</h3>
            <p>Attend classes from anywhere in the world with flexible scheduling.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-male"></i>
            <h3>One-on-One</h3>
            <p>Personalized attention with dedicated teacher for each student.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-quran"></i>
            <h3>Tajweed Rules</h3>
            <p>Master the proper pronunciation and recitation of the Holy Quran.</p>
        </div>
    </div>
</section>

<!-- Courses Section -->
<section class="courses" id="courses">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="display-4 text-gradient">Our Quran Courses</h2>
            <p class="lead text-muted">Embark on your spiritual journey with our comprehensive Quranic programs</p>
            <div class="divider"></div>
        </div>

        <!-- Duration Filters -->
        <div class="course-filters text-center mb-5">
            <div class="btn-group btn-group-pill" role="group">
                <button class="filter-btn btn  active" data-filter="all">All Courses</button>
                <button class="filter-btn btn" data-filter="1month">1 Month</button>
                <button class="filter-btn btn" data-filter="3months">3 Months</button>
                <button class="filter-btn btn " data-filter="6months">6 Months</button>
            </div>
        </div>

        <!-- Courses Grid -->
        <div class="row" id="courseGrid">
            <?php
            $sql = "SELECT * FROM courses c LEFT JOIN teachers t ON c.teacher_id = t.teacher_id";
            $stmt = $db->query($sql);
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($courses) > 0):
                foreach ($courses as $course):
                    $title = htmlspecialchars($course['title']);
                    $image = htmlspecialchars($course['image_url']);
                    $desc  = htmlspecialchars($course['description']);
                    $teacher = htmlspecialchars($course['teacher_id']);
                    $duration = strtolower(str_replace(' ', '', $course['duration'])); // eg: 1month, 3months
            ?>
                    <div class="col-lg-4 col-md-6 mb-4 course-card" data-duration="<?= $duration ?>">
                        <div class="card h-100 shadow-hover border-0 overflow-hidden">
                            <div class="card-image-container">
                                <img src="<?= $image ?>" class="card-img-top" alt="<?= $title ?>">
                                <div class="course-badge"><?= $course['duration'] ?></div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title mb-0"><?= $title ?></h5>
                                    <span class="teacher-badge">
                                        <i class="fas fa-user-tie"></i> <?= $teacher ?>
                                    </span>
                                </div>
                                <p class="card-text text-muted"><?= substr($desc, 0, 100) ?>...</p>

                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div class="course-rating">
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                        <span class="small text-muted ms-1">(42)</span>
                                    </div>

                                    <div class="enroll-section">
                                            <a id="enrollBtn" href="/khairulquran/register.php" class="enroll-btn">
                                                Enroll Now <i class="fas fa-arrow-right ms-2"></i>
                                            </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php endforeach;
            else:
                echo '<div class="col-12 text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                            <h4>No courses available at the moment</h4>
                            <p class="text-muted">We are preparing new courses. Please check back soon.</p>
                        </div>
                      </div>';
            endif;
            ?>
        </div>
    </div>
</section>

<!-- Filter Script -->
<script>
    const filterBtns = document.querySelectorAll('.filter-btn');
    const courseCards = document.querySelectorAll('.course-card');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const filter = btn.getAttribute('data-filter');

            // Set active class
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Show/hide cards
            courseCards.forEach(card => {
                const duration = card.getAttribute('data-duration');
                if (filter === 'all' || filter === duration) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
</script>




<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Start Your Quran Learning Journey Today</h2>
            <p>Join thousands of students learning Quran online with our expert teachers</p>
            <a href="#" class="btn">Get Started</a>
        </div>
    </div>
</section>

<?php include './includes/footer.php'; ?>


<?php

ob_end_flush();
?>