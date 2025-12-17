/**
 * SmartTutor Home Page JavaScript
 * Handles interactive features for the home page
 */

(function () {
    'use strict';

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '#!') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Search form validation
    const searchForm = document.querySelector('.home-search-card form');
    if (searchForm) {
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const subject = this.querySelector('input[name="subject"]');
            const location = this.querySelector('input[name="location"]');

            if (subject && !subject.value.trim()) {
                subject.focus();
                subject.classList.add('is-invalid');
                return false;
            }

            // Remove invalid class on input
            if (subject) {
                subject.addEventListener('input', function () {
                    this.classList.remove('is-invalid');
                });
            }

            // Here you can add AJAX submission or redirect logic
            console.log('Search submitted:', {
                subject: subject?.value,
                location: location?.value,
                budget: this.querySelector('select[name="budget"]')?.value
            });
        });
    }

    // Add animation on scroll for feature cards
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    entry.target.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, 100);

                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe feature cards, tutor cards, and testimonials
    document.querySelectorAll('.home-feature-card, .home-tutor-card, .home-testimonial-card').forEach(card => {
        observer.observe(card);
    });

    // Stats counter animation
    const statsObserver = new IntersectionObserver(function (entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const statNumber = entry.target.querySelector('.home-stat-number');
                if (statNumber && !statNumber.classList.contains('counted')) {
                    statNumber.classList.add('counted');
                    animateValue(statNumber);
                }
                statsObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.home-stat-item').forEach(stat => {
        statsObserver.observe(stat);
    });

    function animateValue(element) {
        const text = element.textContent;
        const value = parseFloat(text.replace(/[^0-9.]/g, ''));
        const suffix = text.replace(/[0-9.]/g, '');
        const duration = 2000;
        const steps = 60;
        const stepValue = value / steps;
        const stepDuration = duration / steps;
        let current = 0;

        const timer = setInterval(() => {
            current += stepValue;
            if (current >= value) {
                element.textContent = text;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current) + suffix;
            }
        }, stepDuration);
    }

    // Add hover effect enhancement for tutor cards
    document.querySelectorAll('.home-tutor-card').forEach(card => {
        card.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-4px)';
        });

        card.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
        });
    });

    // Mobile menu handling (if needed)
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function () {
            const navbarCollapse = document.querySelector('#navbarNav');
            if (navbarCollapse) {
                navbarCollapse.classList.toggle('show');
            }
        });
    }

})();
