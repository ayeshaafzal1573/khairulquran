<?php include './includes/header.php'; ?>
<?php 
require_once './includes/config.php'; 
require_once './includes/auth.php';
?>
<style>
    :root {
    --primary-color:#ff9800;
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
    0% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
    100% { transform: translateY(0px); }
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
    background: var(--light-bg);
}

.course-filters {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 30px;
}

.filter-btn {
    padding: 8px 20px;
    background: var(--white);
    border: 1px solid #ddd;
    border-radius: 30px;
    cursor: pointer;
    transition: var(--transition);
}

.filter-btn:hover, .filter-btn.active {
    background: var(--primary-color);
    color: var(--white);
    border-color: var(--primary-color);
}

.course-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
}

.course-card {
    background: var(--white);
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--box-shadow);
    transition: var(--transition);
}

.course-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.course-img {
    height: 200px;
    overflow: hidden;
}

.course-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition);
}

.course-card:hover .course-img img {
    transform: scale(1.1);
}

.course-info {
    padding: 20px;
}

.course-category {
    display: inline-block;
    padding: 5px 15px;
    background: rgba(30, 136, 229, 0.1);
    color: var(--primary-color);
    border-radius: 30px;
    font-size: 12px;
    font-weight: 500;
    margin-bottom: 10px;
}

.course-info h3 {
    font-size: 20px;
    margin-bottom: 10px;
}

.course-info p {
    color: var(--light-text);
    margin-bottom: 15px;
    font-size: 14px;
}

.course-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

.course-price {
    font-weight: 600;
    color: var(--primary-color);
    font-size: 18px;
}

.course-meta a {
    color: var(--primary-color);
    font-weight: 500;
    text-decoration: none;
    transition: var(--transition);
}

.course-meta a:hover {
    color: var(--secondary-color);
    text-decoration: underline;
}

.view-all {
    text-align: center;
    margin-top: 50px;
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
.cta .btn{
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
    <div class="section-header text-center">
      <h2>Our Quran Courses</h2>
      <p>Choose from our comprehensive Quran learning programs</p>
    </div>

    <!-- Duration Filters -->
    <div class="course-filters text-center mb-4">
      <button class="filter-btn btn btn-outline-primary active" data-filter="all">All</button>
      <button class="filter-btn btn btn-outline-primary" data-filter="1month">1 Month</button>
      <button class="filter-btn btn btn-outline-primary" data-filter="3months">3 Months</button>
      <button class="filter-btn btn btn-outline-primary" data-filter="6months">6 Months</button>
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
        <div class="col-md-4 mb-4 course-card" data-duration="<?= $duration ?>">
          <div class="card h-100 shadow-sm border-0">
            <img src="<?= $image ?>" class="card-img-top" alt="<?= $title ?>" style="height: 200px; object-fit: cover;">
            <div class="card-body">
              <h5 class="card-title"><?= $title ?></h5>
              <p class="card-text text-muted"><strong>Teacher:</strong> <?= $teacher ?></p>
              <p class="card-text text-muted"><strong>Duration:</strong> <?= $course['duration'] ?></p>
          
         <div class="enroll-section">
        <?php if (isLoggedIn() && isStudent()): ?>
<a id="enrollBtn" href="#" class="enroll-btn text-decoration-none">Enroll Now</a>

<?php elseif (!isLoggedIn()): ?>
  <a id="loginEnrollBtn" href="#" class="enroll-btn text-decoration-none">Login to Enroll</a>
<?php else: ?>
  <p class="text-danger">Only students can enroll</p>
<?php endif; ?>

 
          </div>
            </div>
         
        </div>
        </div>
      <?php endforeach;
      else:
        echo "<p class='text-center'>No courses available.</p>";
      endif;
      ?>
 
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

