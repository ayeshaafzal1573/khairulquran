<?php 
ob_start();
session_start();
include './includes/header.php';

?>
<?php include './includes/config.php'; ?>
<style>
  body {
    overflow-x: hidden;
  }

  .about-hero h1 {
    font-size: 3.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 20px;
    line-height: 1.2;
  }

  .about-hero p {
    font-size: 1.2rem;
    color: #7f8c8d;
    margin-bottom: 30px;
  }

  .hero-image {
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  }

  .hero-image img {
    width: 100%;
    height: auto;
    transition: transform 0.5s ease;
  }

  .hero-image:hover img {
    transform: scale(1.03);
  }

  .experience-badge {
    position: absolute;
    bottom: 20px;
    right: 20px;
    background: rgba(44, 62, 80, 0.9);
    color: white;
    padding: 15px 20px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  }

  .experience-badge span {
    font-size: 2.5rem;
    font-weight: 700;
    display: block;
    line-height: 1;
  }

  .experience-badge p {
    margin: 0;
    font-size: 0.9rem;
    opacity: 0.8;
  }

  /* Section Header */
  .section-header {
    text-align: center;
    margin-bottom: 60px;
  }

  .section-header span {
    color: #ff9800;
    font-size: 1.1rem;
    font-weight: 600;
    letter-spacing: 1px;
    display: block;
    margin-bottom: 10px;
  }

  .section-header h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 15px;
  }

  .section-header p {
    color: #7f8c8d;
    max-width: 700px;
    margin: 0 auto;
  }

  /* Mission Section */
  .mission-section {
    padding: 100px 0;
    background-color: #fff;
  }

  .mission-card {
    background: #fff;
    border-radius: 10px;
    padding: 40px 30px;
    height: 100%;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-top: 4px solid #ff9800;
  }

  .mission-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
  }

  .icon-box {
    width: 70px;
    height: 70px;
    background: #f8f9fa;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 25px;
    color: #ff9800;
    font-size: 1.8rem;
  }

  .mission-card h3 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 15px;
    color: #2c3e50;
  }

  .mission-card p {
    color: #7f8c8d;
    line-height: 1.6;
  }

  .timeline {
    position: relative;
    max-width: 1200px;
    margin: 0 auto;
  }

  .timeline::after {
    content: '';
    position: absolute;
    width: 6px;
    background-color: #ff9800;
    top: 0;
    bottom: 0;
    left: 50%;
    margin-left: -3px;
    border-radius: 10px;
  }

  .timeline-item {
    padding: 10px 40px;
    position: relative;
    width: 50%;
    box-sizing: border-box;
  }

  .timeline-item::after {
    content: '';
    position: absolute;
    width: 25px;
    height: 25px;
    right: -12px;
    background-color: white;
    border: 4px solid #ff9800;
    top: 15px;
    border-radius: 50%;
    z-index: 1;
  }

  .timeline-item:nth-child(odd) {
    left: 0;
  }

  .timeline-item:nth-child(even) {
    left: 50%;
  }

  .timeline-item:nth-child(even)::after {
    left: -12px;
  }

  .timeline-content {
    padding: 20px 30px;
    background-color: white;
    position: relative;
    border-radius: 10px;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.05);
  }

  .timeline-year {
    position: absolute;
    top: -20px;
    background: #ff9800;
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-weight: 600;
    box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
  }

  .timeline-item:nth-child(odd) .timeline-year {
    right: 20px;
  }

  .timeline-item:nth-child(even) .timeline-year {
    left: 20px;
  }

  .timeline-content h3 {
    margin-top: 20px;
    color: #2c3e50;
    font-weight: 600;
  }

  .timeline-content p {
    color: #7f8c8d;
    line-height: 1.6;
  }

  /* Teachers Section */
  .teachers-section {
    padding: 100px 0;
    background-color: #f9f9f9;
  }

  .teacher-card {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 30px;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
  }

  .teacher-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
  }

  .teacher-img {
    position: relative;
    overflow: hidden;
    height: 250px;
    width: 100%;
    object-fit: contain!important ;
  }

  .teacher-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
  }

  .teacher-card:hover .teacher-img img {
    transform: scale(1.1);
  }

  .teacher-info {
    padding: 20px;
    text-align: center;
  }

  .teacher-info h3 {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 5px;
    color: #2c3e50;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .teacher-info span {
    display: block;
    color: #ff9800;
    font-weight: 500;
    margin-bottom: 10px;
    font-size: 0.9rem;
  }

  .teacher-info p {
    color: #7f8c8d;
    font-size: 0.9rem;
    margin-bottom: 0;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 60px;
  }

  .testimonials-slider {
    max-width: 1000px;
    margin: 0 auto;
  }

  .testimonial-item {
    padding: 0 15px;
  }

  .testimonial-content {
    background: white;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    position: relative;
  }

  .quote-icon {
    position: absolute;
    top: 20px;
    right: 30px;
    color: #ff9800;
    opacity: 0.2;
    font-size: 4rem;
  }

  .testimonial-content p {
    font-size: 1.1rem;
    color: #2c3e50;
    line-height: 1.8;
    margin-bottom: 30px;
    position: relative;
  }

  .student-info {
    display: flex;
    align-items: center;
  }

  .student-info img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 15px;
    border: 3px solid #ff9800;
  }

  .student-info h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 5px;
  }

  .student-info span {
    color: #7f8c8d;
    font-size: 0.9rem;
  }

  /* Stats Section */
  .stats-section {
    padding: 80px 0;
    background-color: #473d32;
    color: white;
  }

  .stat-item {
    text-align: center;
    padding: 30px 15px;
  }

  .stat-item i {
    font-size: 2.5rem;
    color: #ff9800;
    margin-bottom: 20px;
  }

  .stat-item h3 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 10px;
  }

  .stat-item p {
    font-size: 1.1rem;
    opacity: 0.8;
    margin-bottom: 0;
  }

  .explore {
    background-color: #473d32;
    color: white;
    padding: 12px 25px;
    border-radius: 30px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: 2px solid #473d32;
  }

  .explore:hover {
    background-color: transparent;
    color: #473d32;
  }

  @media (max-width: 768px) {
    .about-hero h1 {
      font-size: 2.5rem;
    }
    
    .about-hero p {
      font-size: 1rem;
    }
    
    .teacher-card {
      max-width: 300px;
      margin-left: auto;
      margin-right: auto;
    }
    
    .timeline-item {
      width: 100%;
      padding-left: 70px;
      padding-right: 25px;
    }
    
    .timeline-item::after {
      left: 18px;
    }
    
    .timeline-item:nth-child(even) {
      left: 0;
    }
    
    .timeline::after {
      left: 31px;
    }
    
    .timeline-item:nth-child(odd) .timeline-year,
    .timeline-item:nth-child(even) .timeline-year {
      left: 0;
      right: auto;
    }
  }
</style>

<section class="about-hero">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6" data-aos="fade-right">
        <h1>Our Journey in Quranic Education</h1>
        <p>Guiding hearts towards the light of the Quran since 2010</p>
        <a href="#mission" class="btn explore">Explore Our Mission</a>
      </div>
      <div class="col-lg-6" data-aos="fade-left">
        <div class="hero-image">
          <img src="assets/images/q1.jpg" alt="Quran Study" class="img-fluid">
          <div class="experience-badge">
            <span>13+</span>
            <p>Years of Excellence</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Mission Section -->
<section class="mission-section" id="mission" data-aos="fade-up">
  <div class="container">
    <div class="section-header">
      <span>Our Purpose</span>
      <h2>Divine Inspiration, Modern Education</h2>
    </div>
    <div class="row">
      <div class="col-md-6 mb-4">
        <div class="mission-card">
          <div class="icon-box">
            <i class="fas fa-book-quran"></i>
          </div>
          <h3>Our Mission</h3>
          <p>To provide authentic Quranic education through innovative online platforms, making quality Islamic learning accessible to students worldwide while preserving traditional teaching methods.</p>
        </div>
      </div>
      <div class="col-md-6 mb-4">
        <div class="mission-card">
          <div class="icon-box">
            <i class="fas fa-eye"></i>
          </div>
          <h3>Our Vision</h3>
          <p>To nurture a generation of Muslims who are deeply connected to the Quran, embody its teachings, and become beacons of light in their communities through knowledge and practice.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Timeline Section -->
<section class="timeline-section bg-light py-5">
  <div class="container">
    <div class="section-header">
      <span>Our Journey</span>
      <h2>Milestones in Quranic Education</h2>
    </div>
    <div class="timeline">
      <div class="timeline-item" data-aos="fade-right">
        <div class="timeline-content">
          <div class="timeline-year">2010</div>
          <h3>Foundation Laid</h3>
          <p>Established with just 3 teachers and 15 students in a small mosque classroom</p>
        </div>
      </div>
      <div class="timeline-item" data-aos="fade-left">
        <div class="timeline-content">
          <div class="timeline-year">2014</div>
          <h3>First Online Class</h3>
          <p>Pioneered online Quran teaching in our region with innovative video call technology</p>
        </div>
      </div>
      <div class="timeline-item" data-aos="fade-right">
        <div class="timeline-content">
          <div class="timeline-year">2018</div>
          <h3>International Recognition</h3>
          <p>Awarded "Best Online Quran Academy" by Islamic Education Foundation</p>
        </div>
      </div>
      <div class="timeline-item" data-aos="fade-left">
        <div class="timeline-content">
          <div class="timeline-year">2023</div>
          <h3>Global Reach</h3>
          <p>Now serving over 2,000 students across 25 countries with 50 certified teachers</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Teachers Section -->
<section class="teachers-section">
  <div class="container">
    <div class="section-header">
      <span>Meet Our</span>
      <h2>Certified Quran Instructors</h2>
      <p>Our teachers are carefully selected for their knowledge, teaching ability, and character</p>
    </div>
    <div class="row">

      <?php
      $Sel = "SELECT * FROM `teachers` LIMIT 8";
      $stmt = $db->query($Sel);
      $teachers = $stmt->fetchAll();
      ?>

      <?php foreach ($teachers as $teacher): ?>
        <div class="col-lg-3 col-md-6 mb-4" data-aos="zoom-in">
          <div class="teacher-card">
            <div class="teacher-img">
              <img src="uploads/teachers/<?= $teacher['profile_image']; ?>" alt="<?= htmlspecialchars($teacher['full_name']) ?>" class="img-fluid">
            </div>
            <div class="teacher-info">
              <h3 title="<?= htmlspecialchars($teacher['full_name']) ?>"><?= htmlspecialchars($teacher['full_name']) ?></h3>
              <span><?= htmlspecialchars($teacher['specialization']) ?></span>
              <p><?= htmlspecialchars($teacher['qualifications']) ?></p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>

    </div>
  </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section bg-light py-5">
  <div class="container">
    <div class="section-header">
      <span>Voices of</span>
      <h2>Our Beloved Students</h2>
    </div>
    <div class="testimonials-slider">
      <div class="testimonial-item">
        <div class="testimonial-content">
          <div class="quote-icon">
            <i class="fas fa-quote-left"></i>
          </div>
          <p>My children have flourished under the guidance of Khairul Quran Academy. The teachers are patient, knowledgeable, and truly care about their students' progress in both Quran recitation and Islamic values.</p>
          <div class="student-info">
            <img src="assets/images/child 3.jpg" alt="Parent">
            <div>
              <h4>Sarah Johnson</h4>
              <span>Parent, USA</span>
            </div>
          </div>
        </div>
      </div>
      <div class="testimonial-item">
        <div class="testimonial-content">
          <div class="quote-icon">
            <i class="fas fa-quote-left"></i>
          </div>
          <p>As a working professional, the flexible schedule has allowed me to continue my Quran memorization. The teaching methods are excellent, and I've progressed more in 6 months here than in 2 years elsewhere.</p>
          <div class="student-info">
            <img src="assets/images/child1.jpg" alt="Student">
            <div>
              <h4>Abdullah Mohammed</h4>
              <span>Hifz Student, UK</span>
            </div>
          </div>
        </div>
      </div>
      <div class="testimonial-item">
        <div class="testimonial-content">
          <div class="quote-icon">
            <i class="fas fa-quote-left"></i>
          </div>
          <p>The Tajweed course transformed my Quran recitation. The teachers have exceptional knowledge and explain complex rules in simple ways. I'm now leading prayers in my local masjid with confidence.</p>
          <div class="student-info">
            <img src="assets/images/child4.jpg" alt="Student">
            <div>
              <h4>Meez khan</h4>
              <span>Tajweed Student, Canada</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
  <div class="container">
    <div class="row">
      <div class="col-md-3 col-sm-6" data-aos="fade-up">
        <div class="stat-item">
          <i class="fas fa-users"></i>
          <h3><span class="counter" data-target="2000">0</span>+</h3>
          <p>Students Enrolled</p>
        </div>
      </div>
      <div class="col-md-3 col-sm-6" data-aos="fade-up" data-aos-delay="100">
        <div class="stat-item">
          <i class="fas fa-chalkboard-teacher"></i>
          <h3><span class="counter" data-target="50">0</span>+</h3>
          <p>Certified Teachers</p>
        </div>
      </div>
      <div class="col-md-3 col-sm-6" data-aos="fade-up" data-aos-delay="200">
        <div class="stat-item">
          <i class="fas fa-globe"></i>
          <h3><span class="counter" data-target="25">0</span>+</h3>
          <p>Countries Reached</p>
        </div>
      </div>
      <div class="col-md-3 col-sm-6" data-aos="fade-up" data-aos-delay="300">
        <div class="stat-item">
          <i class="fas fa-book-quran"></i>
          <h3><span class="counter" data-target="150">0</span>+</h3>
          <p>Huffaz Graduated</p>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS animation library
    AOS.init({
      duration: 800,
      easing: 'ease-in-out',
      once: true
    });

    // Counter animation for stats section
    const counters = document.querySelectorAll('.counter');
    const speed = 200;

    function animateCounters() {
      counters.forEach(counter => {
        const target = +counter.getAttribute('data-target');
        const count = +counter.innerText;
        const increment = target / speed;

        if (count < target) {
          counter.innerText = Math.ceil(count + increment);
          setTimeout(animateCounters, 1);
        }
      });
    }

    // Start counter animation when stats section is in view
    const statsSection = document.querySelector('.stats-section');
    const observer = new IntersectionObserver((entries) => {
      if (entries[0].isIntersecting) {
        animateCounters();
      }
    }, {
      threshold: 0.5
    });

    if (statsSection) {
      observer.observe(statsSection);
    }

    // Testimonials slider
    $('.testimonials-slider').slick({
      dots: true,
      infinite: true,
      speed: 300,
      slidesToShow: 1,
      adaptiveHeight: true,
      autoplay: true,
      autoplaySpeed: 5000,
      arrows: false,
      responsive: [{
        breakpoint: 768,
        settings: {
          dots: false
        }
      }]
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();

        const targetId = this.getAttribute('href');
        if (targetId === '#') return;

        const targetElement = document.querySelector(targetId);
        if (targetElement) {
          window.scrollTo({
            top: targetElement.offsetTop - 80,
            behavior: 'smooth'
          });
        }
      });
    });
  });
</script>

<?php include './includes/footer.php'; ?>


<?php

ob_end_flush();
?>