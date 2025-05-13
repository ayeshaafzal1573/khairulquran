document.addEventListener('DOMContentLoaded', function() {
    // Pricing tab switching
    const tabBtns = document.querySelectorAll('.tab-btn');
    const monthlyPrices = document.querySelectorAll('.monthly-price');
    const yearlyPrices = document.querySelectorAll('.yearly-price');
    const monthlyBillings = document.querySelectorAll('.billing-monthly');
    const yearlyBillings = document.querySelectorAll('.billing-yearly');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            tabBtns.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            const tab = this.dataset.tab;
            
            if (tab === 'monthly') {
                // Show monthly prices
                monthlyPrices.forEach(el => el.classList.remove('hidden'));
                yearlyPrices.forEach(el => el.classList.add('hidden'));
                monthlyBillings.forEach(el => el.classList.remove('hidden'));
                yearlyBillings.forEach(el => el.classList.add('hidden'));
            } else {
                // Show yearly prices
                monthlyPrices.forEach(el => el.classList.add('hidden'));
                yearlyPrices.forEach(el => el.classList.remove('hidden'));
                monthlyBillings.forEach(el => el.classList.add('hidden'));
                yearlyBillings.forEach(el => el.classList.remove('hidden'));
            }
        });
    });

    // FAQ accordion functionality
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        
        question.addEventListener('click', function() {
            // Close all other items
            faqItems.forEach(i => {
                if (i !== item) {
                    i.classList.remove('active');
                }
            });
            
            // Toggle current item
            item.classList.toggle('active');
        });
    });

    // Plan selection buttons
    const selectBtns = document.querySelectorAll('.select-btn');
    
    selectBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const plan = this.closest('.pricing-card').querySelector('h3').textContent;
            alert(`You've selected the ${plan}. Redirecting to checkout...`);
            // In a real implementation, you would redirect to checkout page
        });
    });

    // CTA button
    const ctaBtn = document.querySelector('.cta-btn');
    if (ctaBtn) {
        ctaBtn.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Redirecting to contact page...');
            // In a real implementation, you would redirect to contact page
        });
    }

    // Animation for pricing cards on scroll
    const pricingCards = document.querySelectorAll('.pricing-card');
    
    const animateCards = function() {
        pricingCards.forEach((card, index) => {
            const cardPosition = card.getBoundingClientRect().top;
            const screenPosition = window.innerHeight / 1.3;
            
            if (cardPosition < screenPosition) {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 150);
            }
        });
    };
    
    // Set initial state for animation
    pricingCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    });
    
    // Run on load and scroll
    animateCards();
    window.addEventListener('scroll', animateCards);
});