<?php 
require_once './includes/config.php'; 
require_once './includes/auth.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll_now'])) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
        $_SESSION['error'] = 'Please login as a student to enroll.';
        header('Location: login.php');
        exit;
    } else {
        header("Location: courses.php");
        exit;
    }
}
?>
<?php include './includes/header.php'; ?>
    <div class="container-fluid slide-con">
      <div class="row">
        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
          <div class="left-slider">
            <div class="f1">
              <h1>Khairul Quran Academy</h1>
              <p>
                experience Personalized Quran Learning with Certified Teachers, Flexible Schedules, and a Spiritual Environment Tailored for Every Student."
              </p>
            </div>
            <div class="s1">
              <h1>Khairul Quran Academy</h1>
              <p>
                "Trusted by Families Worldwide for Quality Quran Education with Personalized Attention and Islamic Values."
              </p>
            </div>
            <div class="t1">
              <h1>Khairul Quran Academy</h1>
              <p>
                We combine authentic Quranic education with modern online tools, offering certified tutors, customized learning plans, and 24/7 flexible class options — all in a spiritually uplifting environment.
              </p>
            </div>
            <div class="for1">
              <h1>Khairul Quran Academy</h1>
              <p>
                Learn from trusted Quran teachers, enjoy one-on-one online sessions, and grow spiritually — with free trials, easy enrollment, and worldwide access.
              </p>
            </div>
          </div>
        </div>
        <!-- BANNER IMAGES -->
        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6" >
          <div class="img-slide">
            <div
              class="bg"
              style="background-image: url('./assets/images/banner\ \(2\).jpg')"
            ></div>
            <div
              class="bg"
              style="background-image: url(./assets/images/banner3.jpg)"
            ></div>
            <div
              class="bg"
              style="background-image: url(/assets/images/banner4.jpg)"
            ></div>
            <div
              class="bg"
              style="background-image: url(/assets/images/banner5.jpg)"
            ></div>
          </div>
        </div>
        <div class="buttons">
          <button class="down">
            <i class="fas fa-arrow-down"></i>
          </button>
          <button class="up">
            <i class="fas fa-arrow-up"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- endslider -->
    <section class="features" data-aos="fade-out">
      <h2>Why <span>Choose</span> Us?</h2>
      <div class="feature-grid">
        <div class="feature-item">
          <i class="fas fa-chalkboard-teacher"></i>
          <p>One-on-One Live Classes</p>
        </div>
        <div class="feature-item">
          <i class="fas fa-book-open"></i>
          <p>Structured Curriculum</p>
        </div>
        <div class="feature-item">
          <i class="fas fa-clock"></i>
          <p>Flexible Timings</p>
        </div>
        <div class="feature-item">
          <i class="fas fa-mosque"></i>
          <p>Islamic Environment</p>
        </div>
      </div>
    </section>
    <!-- ABOUT SECTION -->
    <div class="container-fluid">
      <div class="row">
        <div class="col-8" data-aos="fade-left">
          <div class="intro-section">
            <div class="intro-text">
              <h1>
                <span>With Every Lesson</span> Your Child Grows in Faith and Knowledge
              </h1>
              <p>
                We understand that every child learns differently. That’s why our qualified tutors
                use personalized teaching methods, engaging resources, and a compassionate approach
                to help your child grow in Quranic knowledge and confidence.
                <br /><br />
                Through <strong>Khairul Quran Academy</strong> live sessions, your child will explore the
                teachings of the Quran and embrace Islamic values in a meaningful way.
              </p>
              <button>ENROLL NOW</button>
            </div>
          </div>
        </div>
        <div class="col-4" data-aos="fade-right">
          <div class="intro-img">
            <img
              src="assets/images/child.jpg"
           
              alt="Child Learning Quran"
           class="img-fluid" />
          </div>
        </div>
      </div>
    </div>
    <!-- ABOUT SECTION END -->
    <!-- ABOUT SECTION -->
    <div class="container-fluid">
      <div class="row">
        <div class="col-5 mt-5" data-aos="fade-left">
          <div class="intro-img">
            <img src="assets/images/child2.jpg" alt="Child Learning Quran" class="img-fluid" />
          </div>
        </div>
        <div class="col-7" data-aos="fade-right">
          <div class="intro-section">
            <div class="intro-text">
              <h1>
                <h1>
                  <span>Every Class</span> Brings Your Child Closer to the Quran
                </h1>
                <p>
                  Every child is unique and deserves a learning experience that fits their pace and style.
                  Our live tutors use interactive tools and modern techniques to make Quran learning
                  effective, engaging, and spiritually uplifting.
                  <br /><br />
                  With <strong>Khairul Quran Academy</strong> live classes, your child will build a strong foundation
                  in Quran, Tajweed, and Islamic values — from the comfort of home.
                </p>
           <form method="POST">
  <input type="hidden" name="enroll_now" value="1">
  <button type="submit">ENROLL NOW</button>
</form>
            </div>
          </div>
        </div>
      </div>
    </div>
 <!-- Courses Section -->
<section class="courses" id="courses" data-aos="flip-left">
  <h3>Our Courses</h3>
  <div class="course-grid">
  <?php
  $sql = "SELECT * FROM courses c LEFT JOIN teachers t ON c.teacher_id = t.teacher_id LIMIT 6";
  $stmt = $db->query($sql);
  $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if (count($courses) > 0) {
    foreach ($courses as $course) {
      $courseId = htmlspecialchars($course['course_id']);
      $title = htmlspecialchars($course['title']);
      $image = htmlspecialchars($course['image_url']);
      $desc  = htmlspecialchars($course['description']);
      $teacherName = htmlspecialchars($course['full_name'] ?? 'N/A');
  ?>
    <div class="course-card" 
         data-course-id="<?= $courseId ?>"
         data-title="<?= $title ?>"
         data-image="<?= $image ?>"
         data-description="<?= $desc ?>"
         data-teachername="<?= $teacherName ?>">
      <?= $title ?>
    </div>
  <?php }
  } else {
    echo "<p>No courses available.</p>";
  } ?>
  </div>
</section>


<!-- Course Details Modal -->
<div id="courseModal" class="modal">
  <div class="modal-content">
    <span class="close-button">&times;</span>
    <div class="modal-body">
      <img id="courseImage" src="" alt="Course Image" />
      <div class="course-info">
        <h4 id="courseTitle"></h4>
        <p id="courseDescription" style="color: black;"></p>
            <h6>Teacher: <span id="teacherName"></span></h6>
        <div class="flex-row mt-3">
      
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
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById("courseModal");
  const courseCards = document.querySelectorAll(".course-card");
  const closeButton = document.querySelector(".close-button");

  courseCards.forEach(card => {
    card.addEventListener("click", () => {
      const courseId = card.dataset.courseId;
      const enrollBtn = document.getElementById('enrollBtn');
      const loginBtn = document.getElementById('loginEnrollBtn');

      // Update modal content
      document.getElementById('courseImage').src = card.dataset.image;
      document.getElementById('courseTitle').textContent = card.dataset.title;
      document.getElementById('courseDescription').textContent = card.dataset.description;
      document.getElementById('teacherName').textContent = card.dataset.teachername;

    if (enrollBtn) {
  enrollBtn.href = `handle_enroll.php?course_id=${courseId}`;
}

      if(loginBtn) {
        loginBtn.href = `login.php?redirect=${encodeURIComponent(`enroll.php?course_id=${courseId}`)}`;
      }

      modal.style.display = "flex";
    });
  });

  closeButton.addEventListener("click", () => modal.style.display = "none");
  window.addEventListener("click", (e) => e.target === modal && (modal.style.display = "none"));
});
</script>

   <!-- End Banner -->
<div class="abbg">
  <div class="container">
    <div class="row">
      <div class="col-md-12 abo text-center">
        <h1>“Verily, in the remembrance of Allah do hearts find rest.” (Quran 13:28)</h1>
        <h5>
          The Quran is not just a book — it's a light that guides, a friend that comforts, and a cure for the hearts. 
          In a world filled with noise and distractions, turning to the words of Allah brings peace and clarity. 
          Whether you're seeking answers, strength, or simply a moment of tranquility, the Quran is always there — timeless, powerful, and close to the soul. 
          Let it be your companion on the journey of life.
        </h5>
      </div>
    </div>
  </div>
</div>

      <?php include './includes/footer.php'; ?>