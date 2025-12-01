/**
 * ModernMinimal - Main JavaScript File
 * Interactive Features & Animations
 * ES6+ Modern JavaScript
 */

// ========================================
// DOM Content Loaded Event
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

// ========================================
// Main Application Initialization
// ========================================
function initializeApp() {
    initNavigation();
    initScrollEffects();
    initServicesTabs();
    initClassesFunctionality();
    initContactForm();
    initAnimations();
    initLazyLoading();
}

// ========================================
// Navigation Functions
// ========================================
function initNavigation() {
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    const navLinks = document.querySelectorAll('.nav-link');
    const header = document.querySelector('.header');

    // Mobile menu toggle
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            navToggle.classList.toggle('active');
            
            // Prevent body scroll when menu is open
            if (navMenu.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });
    }

    // Close mobile menu when clicking on links
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            navMenu.classList.remove('active');
            navToggle.classList.remove('active');
            document.body.style.overflow = '';
        });
    });

    // Header scroll effect
    window.addEventListener('scroll', function() {
        if (window.scrollY > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // Smooth scrolling for anchor links
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href.startsWith('#')) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    const offsetTop = target.offsetTop - 80;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });

    // Active navigation link highlighting
    window.addEventListener('scroll', updateActiveNavLink);
}

function updateActiveNavLink() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link');
    
    let current = '';
    
    sections.forEach(section => {
        const sectionTop = section.offsetTop - 100;
        const sectionHeight = section.offsetHeight;
        
        if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
            current = section.getAttribute('id');
        }
    });
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === `#${current}`) {
            link.classList.add('active');
        }
    });
}

// ========================================
// Scroll Effects & Animations
// ========================================
function initScrollEffects() {
    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);

    // Observe elements for animation
    const animateElements = document.querySelectorAll('.about-card, .service-card, .portfolio-item, .contact-item');
    animateElements.forEach(el => {
        observer.observe(el);
    });

    // Parallax effect for hero section
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const heroVisual = document.querySelector('.hero-visual');
        
        if (heroVisual) {
            const rate = scrolled * -0.5;
            heroVisual.style.transform = `translateY(${rate}px)`;
        }
    });
}

// ========================================
// Services Tab Functionality
// ========================================
function initServicesTabs() {
    const serviceTabs = document.querySelectorAll('.service-tab');
    const servicePanels = document.querySelectorAll('.service-panel');

    serviceTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetService = this.getAttribute('data-service');
            
            // Remove active class from all tabs and panels
            serviceTabs.forEach(t => t.classList.remove('active'));
            servicePanels.forEach(p => p.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Show corresponding panel
            const targetPanel = document.getElementById(targetService);
            if (targetPanel) {
                targetPanel.classList.add('active');
            }
        });
    });
}

// ========================================
// Classes Functionality
// ========================================
function initClassesFunctionality() {
    initScheduleToggle();
    initClassFilter();
    initClassActions();
}

function initScheduleToggle() {
    const scheduleButtons = document.querySelectorAll('.schedule-btn');
    
    scheduleButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            scheduleButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Toggle between grid and timeline view
            const view = this.getAttribute('data-view');
            toggleView(view);
        });
    });
}

function toggleView(view) {
    const gridView = document.querySelector('.classes-grid-view');
    const timelineView = document.querySelector('.classes-timeline-view');
    
    console.log('Toggle view:', view);
    console.log('Grid view:', gridView);
    console.log('Timeline view:', timelineView);
    
    if (view === 'grid') {
        if (gridView) {
            gridView.style.display = 'block';
            gridView.classList.add('active');
        }
        if (timelineView) {
            timelineView.style.display = 'none';
            timelineView.classList.remove('active');
        }
    } else if (view === 'timeline') {
        if (gridView) {
            gridView.style.display = 'none';
            gridView.classList.remove('active');
        }
        if (timelineView) {
            timelineView.style.display = 'block';
            timelineView.classList.add('active');
        }
    }
}

function initClassFilter() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Filter classes based on category
            const category = this.getAttribute('data-category');
            filterClassesByCategory(category);
        });
    });
}

function initClassActions() {
    // Handle "Book Now" buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-book')) {
            e.preventDefault();
            const classTitle = e.target.closest('.class-card').querySelector('h4').textContent;
            showBookingModal(classTitle);
        }
        
        // Handle "Learn More" buttons
        if (e.target.classList.contains('btn-learn')) {
            e.preventDefault();
            const classCard = e.target.closest('.class-card');
            const classTitle = classCard.querySelector('h4').textContent;
            const classDescription = classCard.querySelector('.class-description').textContent;
            showClassDetails(classTitle, classDescription);
        }
    });
}

function filterClassesBySchedule(schedule) {
    const classCards = document.querySelectorAll('.class-card');
    
    classCards.forEach(card => {
        const cardSchedule = card.getAttribute('data-schedule');
        
        if (schedule === 'all' || cardSchedule === schedule) {
            card.style.display = 'block';
            card.style.animation = 'fadeInUp 0.5s ease forwards';
        } else {
            card.style.display = 'none';
        }
    });
}

function filterClassesByCategory(category) {
    const classCards = document.querySelectorAll('.class-card');
    
    classCards.forEach(card => {
        const cardCategory = card.getAttribute('data-category');
        
        if (category === 'all' || cardCategory === category) {
            card.style.display = 'block';
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            // Animate in
            setTimeout(() => {
                card.style.transition = 'all 0.3s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        } else {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '0';
            card.style.transform = 'translateY(-20px)';
            
            setTimeout(() => {
                card.style.display = 'none';
            }, 300);
        }
    });
}

function showBookingModal(classTitle) {
    // Create modal
    const modal = document.createElement('div');
    modal.className = 'booking-modal';
    modal.innerHTML = `
        <div class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-icon">📅</div>
                    <h3>Book ${classTitle}</h3>
                    <button class="modal-close">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="booking-info">
                        <p>Ready to book <strong>${classTitle}</strong>? Contact us to reserve your spot!</p>
                        <div class="booking-steps">
                            <div class="step">
                                <span class="step-number">1</span>
                                <span class="step-text">Contact us via form or phone</span>
                            </div>
                            <div class="step">
                                <span class="step-number">2</span>
                                <span class="step-text">Choose your preferred time</span>
                            </div>
                            <div class="step">
                                <span class="step-number">3</span>
                                <span class="step-text">Confirm your booking</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-actions">
                        <button class="btn btn-primary" onclick="scrollToContact()">Contact Us</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add modal styles
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        opacity: 0;
        transition: opacity 0.3s ease;
        backdrop-filter: blur(5px);
    `;
    
    const modalContent = modal.querySelector('.modal-content');
    modalContent.style.cssText = `
        background: white;
        border-radius: 20px;
        padding: 0;
        max-width: 500px;
        width: 90%;
        transform: scale(0.8);
        transition: transform 0.3s ease;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    `;
    
    document.body.appendChild(modal);
    
    // Animate in
    setTimeout(() => {
        modal.style.opacity = '1';
        modalContent.style.transform = 'scale(1)';
    }, 10);
    
    // Close modal handlers
    const closeButtons = modal.querySelectorAll('.modal-close');
    closeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            modal.style.opacity = '0';
            modalContent.style.transform = 'scale(0.8)';
            setTimeout(() => {
                document.body.removeChild(modal);
            }, 300);
        });
    });
    
    // Close on overlay click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.opacity = '0';
            modalContent.style.transform = 'scale(0.8)';
            setTimeout(() => {
                document.body.removeChild(modal);
            }, 300);
        }
    });
}

function showClassDetails(title, description) {
    // Create details modal
    const modal = document.createElement('div');
    modal.className = 'class-details-modal';
    modal.innerHTML = `
        <div class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-icon">📚</div>
                    <h3>${title}</h3>
                    <button class="modal-close">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="class-info-section">
                        <h4>About This Class</h4>
                        <p>${description}</p>
                    </div>
                    <div class="class-features">
                        <h4>What You'll Learn</h4>
                        <ul>
                            <li>Proper form and technique</li>
                            <li>Safety guidelines and modifications</li>
                            <li>Progressive skill development</li>
                            <li>Personalized instruction</li>
                        </ul>
                    </div>
                    <div class="class-benefits">
                        <h4>Benefits</h4>
                        <div class="benefits-grid">
                            <div class="benefit-item">
                                <span class="benefit-icon">💪</span>
                                <span>Strength Building</span>
                            </div>
                            <div class="benefit-item">
                                <span class="benefit-icon">🧘</span>
                                <span>Stress Relief</span>
                            </div>
                            <div class="benefit-item">
                                <span class="benefit-icon">❤️</span>
                                <span>Heart Health</span>
                            </div>
                            <div class="benefit-item">
                                <span class="benefit-icon">🎯</span>
                                <span>Goal Achievement</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-actions">
                        <button class="btn btn-primary" onclick="scrollToContact()">Book This Class</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add modal styles
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        opacity: 0;
        transition: opacity 0.3s ease;
        backdrop-filter: blur(5px);
    `;
    
    const modalContent = modal.querySelector('.modal-content');
    modalContent.style.cssText = `
        background: white;
        border-radius: 20px;
        padding: 0;
        max-width: 600px;
        width: 90%;
        max-height: 80vh;
        transform: scale(0.8);
        transition: transform 0.3s ease;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    `;
    
    document.body.appendChild(modal);
    
    // Animate in
    setTimeout(() => {
        modal.style.opacity = '1';
        modalContent.style.transform = 'scale(1)';
    }, 10);
    
    // Close modal handlers
    const closeButtons = modal.querySelectorAll('.modal-close');
    closeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            modal.style.opacity = '0';
            modalContent.style.transform = 'scale(0.8)';
            setTimeout(() => {
                document.body.removeChild(modal);
            }, 300);
        });
    });
    
    // Close on overlay click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.opacity = '0';
            modalContent.style.transform = 'scale(0.8)';
            setTimeout(() => {
                document.body.removeChild(modal);
            }, 300);
        }
    });
}

function scrollToContact() {
    const contactSection = document.querySelector('#contact');
    if (contactSection) {
        const offsetTop = contactSection.offsetTop - 80;
        window.scrollTo({
            top: offsetTop,
            behavior: 'smooth'
        });
    }
}

// ========================================
// Contact Form Handling (Updated for API)
// ========================================
function initContactForm() {
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            const formObject = {};
            
            formData.forEach((value, key) => {
                formObject[key] = value;
            });
            
            // Validate form
            if (validateForm(formObject)) {
                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Sending...';
                submitBtn.disabled = true;
                
                // Submit via API
                try {
                    const result = await window.contentLoader.submitContactForm(formObject);
                    
                    if (result.status === 201) {
                        showNotification('Message sent successfully!', 'success');
                        contactForm.reset();
                    } else {
                        showNotification(result.message || 'Failed to send message', 'error');
                    }
                } catch (error) {
                    showNotification('Network error. Please try again.', 'error');
                } finally {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
            }
        });
        
        // Real-time validation
        const formInputs = contactForm.querySelectorAll('.form-input');
        formInputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                clearFieldError(this);
            });
        });
    }
}

function validateForm(formData) {
    let isValid = true;
    
    // Required fields validation
    const requiredFields = ['name', 'email', 'message'];
    
    requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!formData[field] || formData[field].trim() === '') {
            showFieldError(input, 'This field is required');
            isValid = false;
        }
    });
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (formData.email && !emailRegex.test(formData.email)) {
        const emailInput = document.getElementById('email');
        showFieldError(emailInput, 'Please enter a valid email address');
        isValid = false;
    }
    
    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    
    if (field.hasAttribute('required') && !value) {
        showFieldError(field, 'Ez a mező kötelező');
        return false;
    }
    
    if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            showFieldError(field, 'Érvényes email címet adjon meg');
            return false;
        }
    }
    
    clearFieldError(field);
    return true;
}

function showFieldError(field, message) {
    clearFieldError(field);
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    errorDiv.style.color = '#ff6b35';
    errorDiv.style.fontSize = '0.875rem';
    errorDiv.style.marginTop = '0.25rem';
    
    field.parentNode.appendChild(errorDiv);
    field.style.borderColor = '#ff6b35';
}

function clearFieldError(field) {
    const errorDiv = field.parentNode.querySelector('.field-error');
    if (errorDiv) {
        errorDiv.remove();
    }
    field.style.borderColor = '#e9ecef';
}

// ========================================
// Animation Functions
// ========================================
function initAnimations() {
    // Counter animation for statistics
    const counters = document.querySelectorAll('.counter');
    
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // 60fps
        let current = 0;
        
        const updateCounter = () => {
            current += increment;
            if (current < target) {
                counter.textContent = Math.floor(current);
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target;
            }
        };
        
        // Start animation when element is visible
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateCounter();
                    observer.unobserve(entry.target);
                }
            });
        });
        
        observer.observe(counter);
    });
    
    // Typing animation for hero title
    const heroTitle = document.querySelector('.hero-title');
    if (heroTitle) {
        const titleLines = heroTitle.querySelectorAll('.title-line');
        titleLines.forEach((line, index) => {
            line.style.opacity = '0';
            line.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                line.style.transition = 'all 0.8s ease';
                line.style.opacity = '1';
                line.style.transform = 'translateY(0)';
            }, index * 200);
        });
    }
}

// ========================================
// Lazy Loading Implementation
// ========================================
function initLazyLoading() {
    const lazyImages = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });
    
    lazyImages.forEach(img => {
        imageObserver.observe(img);
    });
}

// ========================================
// Utility Functions
// ========================================
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Style the notification
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        z-index: 10000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto remove
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// ========================================
// Performance Optimizations
// ========================================
// Debounced scroll handler
const debouncedScrollHandler = debounce(function() {
    updateActiveNavLink();
}, 10);

window.addEventListener('scroll', debouncedScrollHandler);

// Throttled resize handler
const throttledResizeHandler = throttle(function() {
    // Handle resize events
    const heroVisual = document.querySelector('.hero-visual');
    if (heroVisual && window.innerWidth < 768) {
        heroVisual.style.transform = 'none';
    }
}, 100);

window.addEventListener('resize', throttledResizeHandler);

// ========================================
// Error Handling
// ========================================
window.addEventListener('error', function(e) {
    console.error('JavaScript Error:', e.error);
    // In production, you might want to send this to an error tracking service
});

// ========================================
// Export for module systems (if needed)
// ========================================
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        initializeApp,
        showNotification,
        debounce,
        throttle
    };
}
