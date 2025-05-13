document.addEventListener('DOMContentLoaded', function() {
    // Form submission handling
    const contactForm = document.getElementById('contactForm');
    const successModal = document.getElementById('successModal');
    const closeModal = document.querySelector('.close-modal');
    const modalBtn = document.querySelector('.modal-btn');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Here you would typically send the form data to a server
            // For demonstration, we'll just show the success modal
            successModal.style.display = 'block';
            
            // Reset the form
            contactForm.reset();
        });
    }
    
    // Close modal when clicking X
    if (closeModal) {
        closeModal.addEventListener('click', function() {
            successModal.style.display = 'none';
        });
    }
    
    // Close modal when clicking OK button
    if (modalBtn) {
        modalBtn.addEventListener('click', function() {
            successModal.style.display = 'none';
        });
    }
    
    // Close modal when clicking outside of it
    window.addEventListener('click', function(e) {
        if (e.target === successModal) {
            successModal.style.display = 'none';
        }
    });
    
    // Add animation to form elements when they come into view
    const formGroups = document.querySelectorAll('.form-group');
    
    const animateOnScroll = function() {
        formGroups.forEach((group, index) => {
            const groupPosition = group.getBoundingClientRect().top;
            const screenPosition = window.innerHeight / 1.3;
            
            if (groupPosition < screenPosition) {
                setTimeout(() => {
                    group.style.opacity = '1';
                    group.style.transform = 'translateY(0)';
                }, index * 100);
            }
        });
    };
    
    // Set initial state for animation
    formGroups.forEach(group => {
        group.style.opacity = '0';
        group.style.transform = 'translateY(20px)';
        group.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    });
    
    // Run on load and scroll
    animateOnScroll();
    window.addEventListener('scroll', animateOnScroll);
    
    // Add hover effect to info cards
    const infoCards = document.querySelectorAll('.info-card');
    
    infoCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const iconCircle = this.querySelector('.icon-circle');
            if (iconCircle) {
                iconCircle.style.transform = 'rotate(15deg) scale(1.1)';
                iconCircle.style.backgroundColor = '#e67e22';
                iconCircle.style.color = 'white';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const iconCircle = this.querySelector('.icon-circle');
            if (iconCircle) {
                iconCircle.style.transform = 'rotate(0) scale(1)';
                iconCircle.style.backgroundColor = '#fae5d3';
                iconCircle.style.color = '#e67e22';
            }
        });
    });
});

window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 30) {
        navbar.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.15)';
        navbar.style.padding = '10px 0';
    } else {
        navbar.style.boxShadow = '0 4px 30px rgba(0, 0, 0, 0.1)';
        navbar.style.padding = '15px 0';
    }
});