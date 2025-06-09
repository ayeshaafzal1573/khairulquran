<?php

ob_start();
session_start();
require_once './includes/config.php';
require_once './includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll_now'])) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = 'Please login first to enroll.';
        header('Location: login.php');
        exit;
    }
    
    // Check if user has student role
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
        $_SESSION['error'] = 'Only students can enroll in courses.';
        header('Location: dashboard.php');
        exit;
    }
    
    // Check if course_id is provided
    if (!isset($_POST['course_id']) || empty($_POST['course_id'])) {
        $_SESSION['error'] = 'No course selected for enrollment.';
        header('Location: courses.php');
        exit;
    }
    
    // Redirect to enroll.php with course_id
    header("Location: enroll.php?course_id=" . $_POST['course_id']);
    exit;
}
    
?>
<?php include './includes/header.php'; ?>
<style>
  .modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.6);
  }

  .modal-content {
    background-color: white;
    margin: 10% auto;
    padding: 20px;
    width: 70%;
    max-width: 600px;
    border-radius: 10px;
    position: relative;
  }

  .arbaz-button {
    display: flex;
    justify-content: flex-end;
    font-size: 28px;
    cursor: pointer;
  }
</style>
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
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
      <div class="img-slide">
        <div
          class="bg"
          style="background-image: url('./assets/images/banner\ \(2\).jpg')"></div>
        <div
          class="bg"
          style="background-image: url(./assets/images/banner3.jpg)"></div>
        <div
          class="bg"
          style="background-image: url(./assets/images/banner4.jpg)"></div>
        <div
          class="bg"
          style="background-image: url(./assets/images/banner5.jpg)"></div>
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
          <form method="POST">
            <input type="hidden" name="enroll_now" value="1">
            <button type="submit">ENROLL NOW</button>
          </form>
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
        <img src="assets/images/child.jpg" alt="Child Learning Quran" class="img-fluid" />
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
            <!-- <form method="POST">
  <input type="hidden" name="enroll_now" value="1">
  <button type="submit">ENROLL NOW</button>
</form> -->
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Courses Section -->
<section class="courses" id="courses" data-aos="flip-left">
  <div class="container">
    <h3 class="text-center mb-4">Our Courses</h3>
    <div class="row">
      <?php
      $sql = "SELECT 
          c.course_id, 
          c.title, 
          c.image_url, 
          c.description, 
          t.full_name AS teacher_name
        FROM 
          courses c
        LEFT JOIN 
          teachers t ON c.teacher_id = t.teacher_id
        LIMIT 6";

      $stmt = $db->query($sql);
      $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if (count($courses) > 0) {
        foreach ($courses as $course) {
          $courseId = htmlspecialchars($course['course_id']);
          $title = htmlspecialchars($course['title']);
          $image = htmlspecialchars($course['image_url']);
          $desc  = htmlspecialchars($course['description']);
          $teacherName = htmlspecialchars($course['teacher_name'] ?? 'N/A');
      ?>
          <div class="col-md-4 mb-1">
            <div class="card course-card h-100"
              data-course-id="<?= $courseId ?>"
              data-title="<?= $title ?>"
              data-image="<?= $image ?>"
              data-description="<?= $desc ?>"
              data-teachername="<?= $teacherName ?>">
              <div class="card-body text-center">
                <h5 class=""><?= $title ?></h5>
              </div>
            </div>
          </div>
      <?php }
      } else {
        echo "<p>No courses available.</p>";
      } ?>
    </div>
  </div>
</section>

<script>
  const upBtn = document.querySelector('.up');
  const downBtn = document.querySelector('.down');
  const textSlides = document.querySelectorAll('.left-slider .slide');
  const imgSlides = document.querySelectorAll('.img-slide .bg');
  let currentSlide = 0;

  function showSlide(index) {
    textSlides.forEach((slide, i) => {
      slide.classList.toggle('active', i === index);
    });
    imgSlides.forEach((img, i) => {
      img.classList.toggle('active', i === index);
    });
  }

  upBtn.addEventListener('click', () => {
    currentSlide = (currentSlide + 1) % textSlides.length;
    showSlide(currentSlide);
  });

  downBtn.addEventListener('click', () => {
    currentSlide = (currentSlide - 1 + textSlides.length) % textSlides.length;
    showSlide(currentSlide);
  });
</script>


<!-- Course Details Modal -->
<div id="courseModal" class="modal">
  <div class="modal-content">
    <span class="arbaz-button">&times;</span>
    <div class="modal-body">
      <img id="courseImage" src="" alt="Course Image" />
      <div class="course-info">
        <h4 id="courseTitle"></h4>
        <p id="courseDescription" style="color: black;"></p>
        <!-- <h6>Teachers: <span id="teacherName"></span></h6> -->
        <div class="flex-row mt-3">

    <div class="enroll-section">
    <?php if (isLoggedIn() && isStudent()): ?>
        <form method="POST" action="">
            <input type="hidden" name="enroll_now" value="1">
            <input type="hidden" name="course_id" id="modalCourseId" value="">
            <button type="submit" class="enroll-btn">Enroll Now</button>
        </form>
    <?php elseif (!isLoggedIn()): ?>
        <a href="/khairulquran/register.php" class="enroll-btn">Login to Enroll</a>
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
 document.addEventListener("DOMContentLoaded", () => {
    const courseCards = document.querySelectorAll(".course-card");
    const modal = document.getElementById("courseModal");
    const closeButton = modal.querySelector(".arbaz-button");
    const courseIdInput = document.getElementById("modalCourseId");

    courseCards.forEach((card) => {
        card.addEventListener("click", () => {
            const courseId = card.getAttribute("data-course-id");
            const title = card.getAttribute("data-title");
            const image = card.getAttribute("data-image");
            const description = card.getAttribute("data-description");

            // Set modal content
            document.getElementById("courseTitle").innerText = title;
            document.getElementById("courseImage").src = image;
            document.getElementById("courseDescription").innerText = description;
            courseIdInput.value = courseId; // Set the course ID in the form

            // Show modal
            modal.style.display = "block";
        });
    });

    // Close modal
    closeButton.addEventListener("click", () => {
        modal.style.display = "none";
    });

    // Close on outside click
    window.addEventListener("click", (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
});

    
</script>

<script>
  const slideLeft = document.querySelector('.left-slider');
  const slideRight = document.querySelector('.img-slide');
  const upButton = document.querySelector('.up');
  const downButton = document.querySelector('.down');
  const slideLength = slideRight.querySelectorAll('.bg').length;

  let activeSlideIndex = 0;

  // Set initial position
  slideLeft.style.top = `-${(slideLength - 1) * 100}vh`;

  const changeSlide = (direction) => {
    if (direction === 'up') {
      activeSlideIndex++;
      if (activeSlideIndex > slideLength - 1) {
        activeSlideIndex = 0;
      }
    } else if (direction === 'down') {
      activeSlideIndex--;
      if (activeSlideIndex < 0) {
        activeSlideIndex = slideLength - 1;
      }
    }

    slideRight.style.transform = `translateY(-${activeSlideIndex * 100}vh)`;
    slideLeft.style.transform = `translateY(${activeSlideIndex * 100}vh)`;
  };

  upButton.addEventListener('click', () => changeSlide('up'));
  downButton.addEventListener('click', () => changeSlide('down'));
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

<?php

ob_end_flush();
?>