// Mobile Navigation Toggle
const hamburger = document.querySelector('.hamburger');
const navMenu = document.querySelector('.nav-menu');

if (hamburger && navMenu) {
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        navMenu.classList.toggle('active');
    });

    // Close mobile menu when clicking on a link
    document.querySelectorAll('.nav-menu a').forEach(link => {
        link.addEventListener('click', () => {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
        });
    });
}

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Navbar background change on scroll with enhanced effects
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        if (window.scrollY > 50) {
            navbar.style.background = 'rgba(254, 254, 254, 0.98)';
            navbar.style.boxShadow = '0 4px 20px rgba(212, 175, 140, 0.1)';
        } else {
            navbar.style.background = 'rgba(254, 254, 254, 0.95)';
            navbar.style.boxShadow = 'none';
        }
    }
});

// Contact form handling
const contactForm = document.querySelector('.contact-form form');
if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const name = this.querySelector('input[type="text"]').value;
        const email = this.querySelector('input[type="email"]').value;
        const message = this.querySelector('textarea').value;
        
        // Simple validation
        if (!name || !email || !message) {
            alert('Kérlek töltsd ki az összes mezőt!');
            return;
        }
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Kérlek adj meg egy érvényes email címet!');
            return;
        }
        
        // Simulate form submission
        alert('Köszönjük az üzeneted! Hamarosan felvesszük veled a kapcsolatot.');
        this.reset();
    });
}

// Intersection Observer for animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe elements for animation
document.addEventListener('DOMContentLoaded', () => {
    const animatedElements = document.querySelectorAll('.fact-item, .skill-item, .about-text, .contact-info, .portfolio-item');
    
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
});

// Skill bars animation with percentage counting
const skillBars = document.querySelectorAll('.skill-progress');
const skillObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const progressBar = entry.target;
            const width = progressBar.getAttribute('data-width');
            const percentageElement = progressBar.closest('.skill-item').querySelector('.skill-percentage');
            
            // Reset to 0
            progressBar.style.width = '0%';
            
            // Animate to target width
            setTimeout(() => {
                progressBar.style.width = width + '%';
                
                // Count up percentage
                let currentPercent = 0;
                const targetPercent = parseInt(width);
                const increment = targetPercent / 50; // 50 steps for smooth animation
                
                const countUp = setInterval(() => {
                    currentPercent += increment;
                    if (currentPercent >= targetPercent) {
                        currentPercent = targetPercent;
                        clearInterval(countUp);
                    }
                    percentageElement.textContent = Math.floor(currentPercent) + '%';
                }, 30);
            }, 200);
        }
    });
}, { threshold: 0.5 });

skillBars.forEach(bar => {
    skillObserver.observe(bar);
});

// Typing effect for hero title
function typeWriter(element, text, speed = 100) {
    let i = 0;
    element.innerHTML = '';
    
    function type() {
        if (i < text.length) {
            element.innerHTML += text.charAt(i);
            i++;
            setTimeout(type, speed);
        }
    }
    
    type();
}

// Initialize typing effect when page loads
window.addEventListener('load', () => {
    const heroTitle = document.querySelector('.hero-title .name');
    if (heroTitle) {
        const originalText = heroTitle.textContent;
        typeWriter(heroTitle, originalText, 80);
    }
});

// Tool hover effects
document.querySelectorAll('.tool').forEach(tool => {
    tool.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-2px)';
    });
    
    tool.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});

// Fact items hover effect
document.querySelectorAll('.fact-item').forEach(item => {
    item.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-4px)';
        this.style.boxShadow = '0 8px 25px rgba(212, 175, 140, 0.15)';
    });
    
    item.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = 'none';
    });
});

// Portfolio item hover effects
document.querySelectorAll('.portfolio-item').forEach(item => {
    item.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-8px)';
        this.style.boxShadow = '0 20px 40px rgba(212, 175, 140, 0.15)';
    });
    
    item.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.05)';
    });
});

// Tag hover effects
document.querySelectorAll('.tag').forEach(tag => {
    tag.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-1px)';
        this.style.background = '#d4af8c';
        this.style.color = 'white';
    });
    
    tag.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.background = 'rgba(212, 175, 140, 0.1)';
        this.style.color = '#d4af8c';
    });
});

// Add loading animation
window.addEventListener('load', () => {
    document.body.style.opacity = '0';
    document.body.style.transition = 'opacity 0.5s ease';
    
    setTimeout(() => {
        document.body.style.opacity = '1';
    }, 100);
});

// CV Download functionality
const cvDownloadBtn = document.querySelector('.cv-download-btn');
if (cvDownloadBtn) {
    cvDownloadBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Create a temporary link to download CV
        const link = document.createElement('a');
        link.href = '#'; // Replace with actual CV file path
        link.download = 'Leib_Jazmin_CV.pdf';
        
        // Show download message
        alert('CV letöltése hamarosan elérhető lesz!');
        
        // For now, just show a message
        // In production, you would set link.href to the actual CV file path
        // link.click();
    });
}

// Social links hover effects
document.querySelectorAll('.social-link').forEach(link => {
    link.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-2px)';
        this.style.background = '#d4af8c';
        this.style.color = '#2c2c2c';
    });
    
    link.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.background = 'rgba(255, 255, 255, 0.05)';
        this.style.color = '#999';
    });
});

// Console welcome message
console.log('%c👋 Üdvözöllek Leib Jázmin weboldalán!', 'color: #2c2c2c; font-size: 16px; font-weight: bold;');
console.log('%cHa érdekel a webdesign, ne habozz felvenni velem a kapcsolatot!', 'color: #666; font-size: 14px;');

// Parallax effect for visual element
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const visualElement = document.querySelector('.visual-element');
    if (visualElement) {
        const rate = scrolled * -0.5;
        visualElement.style.transform = `translateY(${rate}px)`;
    }
});

// Smooth reveal animation for sections
const revealElements = document.querySelectorAll('.section-title, .section-subtitle');
const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, { threshold: 0.1 });

revealElements.forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
    revealObserver.observe(el);
});