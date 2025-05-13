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
        
        if(count < target) {
          counter.innerText = Math.ceil(count + increment);
          setTimeout(animateCounters, 1);
        } else {
          counter.innerText = target + '+';
        }
      });
    }
  
    // Start counter animation when stats section is in view
    const statsSection = document.querySelector('.stats-section');
    const observer = new IntersectionObserver((entries) => {
      if(entries[0].isIntersecting) {
        animateCounters();
      }
    }, {threshold: 0.5});
  
    if(statsSection) {
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
      responsive: [
        {
          breakpoint: 768,
          settings: {
            dots: false
          }
        }
      ]
    });
  
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        if(targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if(targetElement) {
          window.scrollTo({
            top: targetElement.offsetTop - 80,
            behavior: 'smooth'
          });
        }
      });
    });
  });