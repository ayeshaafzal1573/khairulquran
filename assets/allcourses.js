document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navbar = document.querySelector('.navbar');
    
    mobileMenuBtn.addEventListener('click', function() {
        navbar.classList.toggle('active');
        mobileMenuBtn.innerHTML = navbar.classList.contains('active') ? 
            '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
    });
    
    // Close mobile menu when clicking on a link
    const navLinks = document.querySelectorAll('.navbar ul li a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            navbar.classList.remove('active');
            mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
        });
    });
    
    // Header scroll effect
    window.addEventListener('scroll', function() {
        const header = document.querySelector('.header');
        header.classList.toggle('scrolled', window.scrollY > 50);
    });
    
    // Course Filtering
    const filterButtons = document.querySelectorAll('.filter-btn');
    const courseGrid = document.querySelector('.course-grid');
    
    // Sample course data
    const courses = [
        {
            id: 1,
            title: "Quran Reading Basics",
            description: "Learn how to read Quran with proper pronunciation from the beginning.",
            category: "kids",
            price: "$29/month",
            image: "https://muslimequran.com/wp-content/uploads/2023/06/quran-reading.jpg"
        },
        {
            id: 2,
            title: "Tajweed Rules Course",
            description: "Master the rules of Tajweed to recite Quran beautifully.",
            category: "adults",
            price: "$39/month",
            image: "https://muslimequran.com/wp-content/uploads/2023/06/tajweed-course.jpg"
        },
        {
            id: 3,
            title: "Quran Memorization",
            description: "Systematic approach to memorize Quran with understanding.",
            category: "kids",
            price: "$49/month",
            image: "https://muslimequran.com/wp-content/uploads/2023/06/hifz-course.jpg"
        },
        {
            id: 4,
            title: "Arabic for Beginners",
            description: "Learn Arabic language to understand Quran directly.",
            category: "arabic",
            price: "$35/month",
            image: "https://muslimequran.com/wp-content/uploads/2023/06/arabic-course.jpg"
        },
        {
            id: 5,
            title: "Advanced Tajweed",
            description: "Deep dive into advanced Tajweed rules for perfect recitation.",
            category: "tajweed",
            price: "$45/month",
            image: "https://muslimequran.com/wp-content/uploads/2023/06/advanced-tajweed.jpg"
        },
        {
            id: 6,
            title: "Islamic Studies for Kids",
            description: "Fun and engaging Islamic education for children.",
            category: "kids",
            price: "$25/month",
            image: "https://muslimequran.com/wp-content/uploads/2023/06/islamic-studies.jpg"
        },
        {
            id: 7,
            title: "Islamic Studies for Kids",
            description: "Fun and engaging Islamic education for children.",
            category: "tajweed",
            price: "$25/month",
            image: "https://muslimequran.com/wp-content/uploads/2023/06/islamic-studies.jpg"
        },
        {
            id: 8,
            title: "Islamic Studies for Kids",
            description: "Fun and engaging Islamic education for children.",
            category: "adults",
            price: "$25/month",
            image: "https://muslimequran.com/wp-content/uploads/2023/06/islamic-studies.jpg"
        },
        {
            id: 9,
            title: "Islamic Studies for Kids",
            description: "Fun and engaging Islamic education for children.",
            category: "arabic",
            price: "$25/month",
            image: "https://muslimequran.com/wp-content/uploads/2023/06/islamic-studies.jpg"
        },
        {
            id: 10,
            title: "Islamic Studies for Kids",
            description: "Fun and engaging Islamic education for children.",
            category: "adults",
            price: "$25/month",
            image: "https://muslimequran.com/wp-content/uploads/2023/06/islamic-studies.jpg"
        }
    ];
    
    // Display all courses initially
    displayCourses(courses);
    
    // Filter courses
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            
            if (filter === 'all') {
                displayCourses(courses);
            } else {
                const filteredCourses = courses.filter(course => course.category === filter);
                displayCourses(filteredCourses);
            }
        });
    });
    
    // Function to display courses
    function displayCourses(coursesToDisplay) {
        courseGrid.innerHTML = '';
        
        if (coursesToDisplay.length === 0) {
            courseGrid.innerHTML = '<p class="no-courses">No courses found in this category.</p>';
            return;
        }
        
        coursesToDisplay.forEach(course => {
            const courseCard = document.createElement('div');
            courseCard.className = 'course-card';
            courseCard.innerHTML = `
                <div class="course-img">
                    <img src="${course.image}" alt="${course.title}">
                </div>
                <div class="course-info">
                    <span class="course-category">${course.category.charAt(0).toUpperCase() + course.category.slice(1)}</span>
                    <h3>${course.title}</h3>
                    <p>${course.description}</p>
                    <div class="course-meta">
                        <span class="course-price">${course.price}</span>
                        <a href="#">Learn More</a>
                    </div>
                </div>
            `;
            courseGrid.appendChild(courseCard);
        });
    }
    
    // Teacher Slider Data
    const teachers = [
        {
            id: 1,
            name: "Sheikh Ahmed Mohamed",
            role: "Quran & Tajweed Teacher",
            image: "https://muslimequran.com/wp-content/uploads/2023/06/teacher-1.jpg"
        },
        {
            id: 2,
            name: "Sheikh Mahmoud Ali",
            role: "Hifz Specialist",
            image: "https://muslimequran.com/wp-content/uploads/2023/06/teacher-2.jpg"
        },
        {
            id: 3,
            name: "Sheikh Ibrahim Hassan",
            role: "Arabic Language Expert",
            image: "https://muslimequran.com/wp-content/uploads/2023/06/teacher-3.jpg"
        },
        {
            id: 4,
            name: "Ustadha Fatima Ahmed",
            role: "Female Quran Teacher",
            image: "https://muslimequran.com/wp-content/uploads/2023/06/teacher-4.jpg"
        },
        {
            id: 5,
            name: "Sheikh Omar Abdullah",
            role: "Islamic Studies Teacher",
            image: "https://muslimequran.com/wp-content/uploads/2023/06/teacher-5.jpg"
        }
    ];
            // Display Teachers
            const teacherSlider = document.querySelector('.teacher-slider');
        
            teachers.forEach(teacher => {
                const teacherCard = document.createElement('div');
                teacherCard.className = 'teacher-card';
                teacherCard.innerHTML = `
                    <div class="teacher-img">
                        <img src="${teacher.image}" alt="${teacher.name}">
                    </div>
                    <div class="teacher-info">
                        <h3>${teacher.name}</h3>
                        <p>${teacher.role}</p>
                        <div class="teacher-social">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                `;
                teacherSlider.appendChild(teacherCard);
            });
    
            // Testimonial Data
            const testimonials = [
                {
                    id: 1,
                    quote: "My kids have improved their Quran reading significantly since joining Muslim Quran Academy. The teachers are very patient and knowledgeable.",
                    name: "Sarah Johnson",
                    role: "Parent",
                    image: "https://muslimequran.com/wp-content/uploads/2023/06/testimonial-1.jpg"
                },
                {
                    id: 2,
                    quote: "I've been taking Tajweed classes for 6 months and I can see a huge difference in my recitation. Alhamdulillah for this opportunity to learn properly.",
                    name: "Mohammed Ali",
                    role: "Student",
                    image: "https://muslimequran.com/wp-content/uploads/2023/06/testimonial-2.jpg"
                },
                {
                    id: 3,
                    quote: "The flexibility of online classes and the quality of teaching is excellent. My daughter looks forward to her Quran lessons every week.",
                    name: "Amina Khan",
                    role: "Parent",
                    image: "https://muslimequran.com/wp-content/uploads/2023/06/testimonial-3.jpg"
                },
                {
                    id: 4,
                    quote: "As a working professional, the evening classes fit perfectly with my schedule. The teachers are very accommodating and professional.",
                    name: "Omar Farooq",
                    role: "Student",
                    image: "https://muslimequran.com/wp-content/uploads/2023/06/testimonial-4.jpg"
                }
            ];
    
            // Display Testimonials
            const testimonialSlider = document.querySelector('.testimonial-slider');
            
            testimonials.forEach(testimonial => {
                const testimonialCard = document.createElement('div');
                testimonialCard.className = 'testimonial-card';
                testimonialCard.innerHTML = `
                    <div class="testimonial-content">
                        <p>${testimonial.quote}</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="${testimonial.image}" alt="${testimonial.name}">
                        <div class="author-info">
                            <h4>${testimonial.name}</h4>
                            <p>${testimonial.role}</p>
                        </div>
                    </div>
                `;
                testimonialSlider.appendChild(testimonialCard);
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
    
            // View All Courses button functionality
            const viewAllBtn = document.querySelector('.view-all .btn');
            viewAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Set filter to 'all' and trigger click
                const allFilter = document.querySelector('.filter-btn[data-filter="all"]');
                allFilter.click();
                
                // Scroll to courses section
                const coursesSection = document.querySelector('#courses');
                if (coursesSection) {
                    window.scrollTo({
                        top: coursesSection.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
    
            // Free Trial button functionality
            const freeTrialBtn = document.querySelector('.hero-buttons .btn-secondary');
            freeTrialBtn.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Thank you for your interest! Please contact us to schedule your free trial class.');
            });
    
            // Get Started button functionality
            const getStartedBtn = document.querySelector('.cta .btn-primary');
            getStartedBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Scroll to registration section (you might want to add this)
                // For now, just show an alert
                alert('Please register to get started with your Quran learning journey!');
            });
    
            // Registration button functionality
            const registerBtn = document.querySelector('.btn-primary.btn');
            registerBtn.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Registration form will appear here. Please provide your details to get started.');
            });
    
            // Login button functionality
            const loginBtn = document.querySelector('.btn-login');
            loginBtn.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Login form will appear here. Please enter your credentials to access your account.');
            });
        });