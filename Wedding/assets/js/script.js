// ===== WEDDING WEBSITE JAVASCRIPT =====
// Modern, interactive features for wedding invitation

// API konfiguráció
const API_BASE_URL = './api';

// Globális adatok tárolása
let weddingData = {
    couple: null,
    events: [],
    story: [],
    gallery: [],
    contact: null,
    settings: {}
};

document.addEventListener('DOMContentLoaded', function() {
    // Adatok betöltése
    loadWeddingData();

    // ===== MOBILE NAVIGATION =====
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    const navLinks = document.querySelectorAll('.nav-link');

    navToggle.addEventListener('click', function() {
        navToggle.classList.toggle('active');
        navMenu.classList.toggle('active');
        document.body.classList.toggle('nav-open');
    });

    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            navToggle.classList.remove('active');
            navMenu.classList.remove('active');
            document.body.classList.remove('nav-open');
        });
    });

    // ===== SMOOTH SCROLLING =====
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            if (targetSection) {
                const offsetTop = targetSection.offsetTop - 70;
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });

    // ===== NAVBAR SCROLL EFFECT =====
    const navbar = document.querySelector('.navbar');
    let lastScrollTop = 0;

    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > 100) {
            navbar.style.background = 'rgba(255, 255, 255, 0.98)';
            navbar.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
        } else {
            navbar.style.background = 'rgba(255, 255, 255, 0.95)';
            navbar.style.boxShadow = 'none';
        }

        // Hide/show navbar on scroll
        if (scrollTop > lastScrollTop && scrollTop > 200) {
            navbar.style.transform = 'translateY(-100%)';
        } else {
            navbar.style.transform = 'translateY(0)';
        }
        
        lastScrollTop = scrollTop;
    });

    // ===== COUNTDOWN TIMER =====
    // Initialize countdown with static date
    initializeCountdown();

    // ===== SCROLL ANIMATIONS =====
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                
                // Add staggered animation for timeline items
                if (entry.target.classList.contains('timeline-item')) {
                    const timelineItems = document.querySelectorAll('.timeline-item');
                    const index = Array.from(timelineItems).indexOf(entry.target);
                    entry.target.style.animationDelay = `${index * 0.2}s`;
                }
                
                // Add staggered animation for gallery items
                if (entry.target.classList.contains('gallery-item')) {
                    const galleryItems = document.querySelectorAll('.gallery-item');
                    const index = Array.from(galleryItems).indexOf(entry.target);
                    entry.target.style.animationDelay = `${index * 0.1}s`;
                }
                
                // Add staggered animation for event cards
                if (entry.target.classList.contains('event-card')) {
                    const eventCards = document.querySelectorAll('.event-card');
                    const index = Array.from(eventCards).indexOf(entry.target);
                    entry.target.style.animationDelay = `${index * 0.2}s`;
                }
            }
        });
    }, observerOptions);

    // Observe elements for animation
    const animateElements = document.querySelectorAll('.timeline-item, .gallery-item, .event-card, .countdown-item, .gift-option');
    animateElements.forEach(el => {
        el.classList.add('animate-on-scroll', 'slide-bottom');
        observer.observe(el);
    });

    // ===== GALLERY LIGHTBOX =====
    // Galéria események az API adatok betöltése után lesznek bindelve

    // ===== RSVP FORM HANDLING =====
    const rsvpForm = document.querySelector('.rsvp-form');
    
    rsvpForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        // Simple validation
        if (!data.name || !data.email || !data.attendance) {
            showNotification('Please fill in all required fields!', 'error');
            return;
        }
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(data.email)) {
            showNotification('Please enter a valid email address!', 'error');
            return;
        }
        
        const submitBtn = this.querySelector('.submit-btn');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Sending...';
        submitBtn.disabled = true;
        
        try {
            // Send RSVP data to API
            const success = await submitRSVP(data);
            
            if (success) {
                // Reset form
                this.reset();
            }
        } catch (error) {
            console.error('RSVP submission error:', error);
            showNotification('An error occurred. Please try again.', 'error');
        } finally {
            // Restore button state
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }
    });

    // ===== NOTIFICATION SYSTEM =====
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 10px;
            color: white;
            font-weight: 500;
            z-index: 10000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            max-width: 300px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        `;
        
        // Set background color based on type
        switch(type) {
            case 'success':
                notification.style.background = '#4CAF50';
                break;
            case 'error':
                notification.style.background = '#f44336';
                break;
            case 'warning':
                notification.style.background = '#ff9800';
                break;
            default:
                notification.style.background = '#2196F3';
        }
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(400px)';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }

    // ===== PARALLAX EFFECT =====
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.hero-background');
        
        parallaxElements.forEach(element => {
            const speed = 0.5;
            element.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });


    // ===== SMOOTH REVEAL ANIMATIONS =====
    function revealOnScroll() {
        const reveals = document.querySelectorAll('.animate-on-scroll');
        
        reveals.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < window.innerHeight - elementVisible) {
                element.classList.add('animated');
            }
        });
    }

    window.addEventListener('scroll', revealOnScroll);

    // ===== HERO SCROLL BUTTON =====
    const heroScroll = document.querySelector('.hero-scroll');
    if (heroScroll) {
        heroScroll.addEventListener('click', function() {
            const countdownSection = document.querySelector('.countdown-section');
            if (countdownSection) {
                countdownSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    }


    // ===== RANDOM HEARTS ANIMATION =====
    function createFloatingHeart() {
        const heart = document.createElement('div');
        heart.className = 'floating-heart';
        heart.innerHTML = '💖';
        
        // Random pozíció a képernyő alján
        heart.style.left = Math.random() * window.innerWidth + 'px';
        heart.style.top = window.innerHeight + 'px';
        
        // Random szívek
        const hearts = ['💖', '💕', '💗', '💘', '💝', '💞'];
        heart.innerHTML = hearts[Math.floor(Math.random() * hearts.length)];
        
        document.body.appendChild(heart);
        
        // Eltávolítás az animáció után
        setTimeout(() => {
            if (document.body.contains(heart)) {
                document.body.removeChild(heart);
            }
        }, 4000);
    }

    // Szívecske animációk időnként
    setInterval(createFloatingHeart, 3000);

    // ===== CUSTOM CURSOR EFFECT =====
    const cursor = document.createElement('div');
    cursor.className = 'custom-cursor';
    cursor.style.cssText = `
        position: fixed;
        width: 20px;
        height: 20px;
        background: var(--primary-gold);
        border-radius: 50%;
        pointer-events: none;
        z-index: 9999;
        mix-blend-mode: difference;
        transition: transform 0.1s ease;
        display: none;
    `;
    document.body.appendChild(cursor);

    document.addEventListener('mousemove', function(e) {
        cursor.style.left = e.clientX - 10 + 'px';
        cursor.style.top = e.clientY - 10 + 'px';
        cursor.style.display = 'block';
    });

    document.addEventListener('mouseenter', function() {
        cursor.style.display = 'block';
    });

    document.addEventListener('mouseleave', function() {
        cursor.style.display = 'none';
    });

    // ===== PERFORMANCE OPTIMIZATION =====
    // Throttle scroll events
    let scrollTimeout;
    window.addEventListener('scroll', function() {
        if (scrollTimeout) {
            clearTimeout(scrollTimeout);
        }
        scrollTimeout = setTimeout(function() {
            // Scroll-based animations here
        }, 16); // ~60fps
    });

    // ===== ACCESSIBILITY IMPROVEMENTS =====
    // Add keyboard navigation for custom elements
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Tab') {
            document.body.classList.add('keyboard-navigation');
        }
    });

    document.addEventListener('mousedown', function() {
        document.body.classList.remove('keyboard-navigation');
    });

    // ===== INITIALIZATION COMPLETE =====
    console.log('Wedding website initialized successfully! 💕');
});

// ===== API FUNCTIONS =====

// Adatok betöltése az API-ból
async function loadWeddingData() {
    try {
        const response = await fetch(`${API_BASE_URL}/all`);
        const result = await response.json();
        
        if (result.status === 'success') {
            weddingData = result.data;
            updatePageContent();
        } else {
            console.error('API Error:', result.message);
            showError('Failed to load wedding data');
        }
    } catch (error) {
        console.error('Network Error:', error);
        showError('Network error occurred');
    }
}

// Oldal tartalmának frissítése
function updatePageContent() {
    updateCoupleInfo();
    updateEvents();
    updateStoryTimeline();
    updateGallery();
    updateContactInfo();
    updateSiteSettings();
}

// Pár információk frissítése
function updateCoupleInfo() {
    if (!weddingData.couple) return;
    
    const couple = weddingData.couple;
    
    // Név frissítése
    const brideName = document.querySelector('.title-line:first-child');
    const groomName = document.querySelector('.title-line:last-child');
    if (brideName) brideName.textContent = couple.bride_name;
    if (groomName) groomName.textContent = couple.groom_name;
    
    // Dátum frissítése
    const weddingDate = document.querySelector('.hero-date');
    if (weddingDate) {
        const date = new Date(couple.wedding_date);
        weddingDate.textContent = date.toLocaleDateString('hu-HU', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
    
    // Countdown frissítése
    updateCountdown(couple.wedding_date, couple.wedding_time);
}

// Események frissítése
function updateEvents() {
    if (!weddingData.events || weddingData.events.length === 0) return;
    
    const eventsContainer = document.querySelector('.events-container');
    if (!eventsContainer) return;
    
    eventsContainer.innerHTML = '';
    
    weddingData.events.forEach(event => {
        const eventCard = document.createElement('div');
        eventCard.className = 'event-card';
        eventCard.innerHTML = `
            <div class="event-icon">
                <i class="${event.icon}"></i>
            </div>
            <div class="event-details">
                <h3>${event.title}</h3>
                <p class="event-time">${formatTime(event.event_time)}</p>
                <p class="event-location">${event.location}</p>
                <p class="event-address">${event.address}</p>
            </div>
        `;
        eventsContainer.appendChild(eventCard);
    });
}

// Sztori idővonal frissítése
function updateStoryTimeline() {
    if (!weddingData.story || weddingData.story.length === 0) return;
    
    const timelineContainer = document.querySelector('.story-timeline');
    if (!timelineContainer) return;
    
    timelineContainer.innerHTML = '';
    
    weddingData.story.forEach((item, index) => {
        const timelineItem = document.createElement('div');
        timelineItem.className = 'timeline-item';
        timelineItem.innerHTML = `
            <div class="timeline-date">${item.year}</div>
            <div class="timeline-content">
                <h3>${item.title}</h3>
                <p>${item.description}</p>
            </div>
        `;
        timelineContainer.appendChild(timelineItem);
    });
}

// Galéria frissítése
function updateGallery() {
    if (!weddingData.gallery || weddingData.gallery.length === 0) return;
    
    const galleryContainer = document.querySelector('.gallery-grid');
    if (!galleryContainer) return;
    
    galleryContainer.innerHTML = '';
    
    weddingData.gallery.forEach(image => {
        const galleryItem = document.createElement('div');
        galleryItem.className = 'gallery-item';
        galleryItem.setAttribute('data-category', image.category);
        galleryItem.innerHTML = `
            <img src="assets/images/gallery/${image.filename}" alt="${image.alt_text}">
            <div class="gallery-overlay">
                <h3>${image.title}</h3>
                <p>${image.description}</p>
            </div>
        `;
        galleryContainer.appendChild(galleryItem);
    });
    
    // Galéria események újrabindelése
    bindGalleryEvents();
}

// Galéria események bindelése
function bindGalleryEvents() {
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    galleryItems.forEach(item => {
        item.addEventListener('click', function() {
            const img = this.querySelector('img');
            const overlay = this.querySelector('.gallery-overlay');
            
            // Create lightbox
            const lightbox = document.createElement('div');
            lightbox.className = 'lightbox';
            lightbox.innerHTML = `
                <div class="lightbox-content">
                    <span class="lightbox-close">&times;</span>
                    <img src="${img.src}" alt="${img.alt}">
                    <div class="lightbox-caption">
                        <h3>${overlay.querySelector('h3').textContent}</h3>
                        <p>${overlay.querySelector('p').textContent}</p>
                    </div>
                </div>
            `;
            
            // Add lightbox styles
            lightbox.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.9);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                opacity: 0;
                transition: opacity 0.3s ease;
            `;
            
            const lightboxContent = lightbox.querySelector('.lightbox-content');
            lightboxContent.style.cssText = `
                position: relative;
                max-width: 90%;
                max-height: 90%;
                text-align: center;
            `;
            
            const lightboxImg = lightbox.querySelector('img');
            lightboxImg.style.cssText = `
                max-width: 100%;
                max-height: 80vh;
                border-radius: 10px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            `;
            
            const lightboxClose = lightbox.querySelector('.lightbox-close');
            lightboxClose.style.cssText = `
                position: absolute;
                top: -40px;
                right: 0;
                color: white;
                font-size: 2rem;
                cursor: pointer;
                background: none;
                border: none;
                z-index: 10000;
            `;
            
            const lightboxCaption = lightbox.querySelector('.lightbox-caption');
            lightboxCaption.style.cssText = `
                color: white;
                margin-top: 20px;
                text-align: center;
            `;
            
            document.body.appendChild(lightbox);
            document.body.style.overflow = 'hidden';
            
            // Animate in
            setTimeout(() => {
                lightbox.style.opacity = '1';
            }, 10);
            
            // Close lightbox
            function closeLightbox() {
                lightbox.style.opacity = '0';
                setTimeout(() => {
                    document.body.removeChild(lightbox);
                    document.body.style.overflow = '';
                }, 300);
            }
            
            lightboxClose.addEventListener('click', closeLightbox);
            lightbox.addEventListener('click', function(e) {
                if (e.target === lightbox) {
                    closeLightbox();
                }
            });
            
            // Close with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeLightbox();
                }
            });
        });
    });
}

// Kapcsolattartási információk frissítése
function updateContactInfo() {
    if (!weddingData.contact) return;
    
    const contact = weddingData.contact;
    
    // Telefonszámok frissítése
    const bridePhone = document.querySelector('.contact-item:nth-child(1) span');
    const groomPhone = document.querySelector('.contact-item:nth-child(2) span');
    const email = document.querySelector('.contact-item:nth-child(3) span');
    
    if (bridePhone) bridePhone.textContent = `Jane: ${contact.bride_phone}`;
    if (groomPhone) groomPhone.textContent = `John: ${contact.groom_phone}`;
    if (email) email.textContent = contact.email;
}

// Weboldal beállítások frissítése
function updateSiteSettings() {
    if (!weddingData.settings) return;
    
    const settings = weddingData.settings;
    
    // Címek frissítése
    if (settings.site_title) {
        document.title = settings.site_title;
    }
    
    if (settings.site_description) {
        const metaDescription = document.querySelector('meta[name="description"]');
        if (metaDescription) metaDescription.content = settings.site_description;
    }
    
    if (settings.hero_subtitle) {
        const heroSubtitle = document.querySelector('.hero-subtitle');
        if (heroSubtitle) heroSubtitle.textContent = settings.hero_subtitle;
    }
    
    if (settings.rsvp_message) {
        const rsvpMessage = document.querySelector('.rsvp-info p');
        if (rsvpMessage) rsvpMessage.textContent = settings.rsvp_message;
    }
}

// Initialize countdown with static date from HTML
function initializeCountdown() {
    const weddingDate = '2026-06-16'; // Static wedding date
    const weddingTime = '14:00:00'; // Static wedding time
    const weddingDateTime = new Date(`${weddingDate}T${weddingTime}`).getTime();
    
    function updateCountdownTimer() {
        const now = new Date().getTime();
        const timeLeft = weddingDateTime - now;

        if (timeLeft > 0) {
            const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
            const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

            const daysEl = document.getElementById('days');
            const hoursEl = document.getElementById('hours');
            const minutesEl = document.getElementById('minutes');
            const secondsEl = document.getElementById('seconds');
            
            if (daysEl) daysEl.textContent = days.toString().padStart(2, '0');
            if (hoursEl) hoursEl.textContent = hours.toString().padStart(2, '0');
            if (minutesEl) minutesEl.textContent = minutes.toString().padStart(2, '0');
            if (secondsEl) secondsEl.textContent = seconds.toString().padStart(2, '0');
        } else {
            const countdownContainer = document.querySelector('.countdown-container');
            if (countdownContainer) {
                countdownContainer.innerHTML = 
                    '<div style="text-align: center; font-size: 2rem; color: var(--primary-gold);">🎉 The big day has arrived! 🎉</div>';
            }
        }
    }
    
    updateCountdownTimer();
    setInterval(updateCountdownTimer, 1000);
}

// Countdown frissítése dinamikus dátummal (API-hoz)
function updateCountdown(weddingDate, weddingTime) {
    const weddingDateTime = new Date(`${weddingDate}T${weddingTime}`).getTime();
    
    function updateCountdownTimer() {
        const now = new Date().getTime();
        const timeLeft = weddingDateTime - now;

        if (timeLeft > 0) {
            const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
            const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

            const daysEl = document.getElementById('days');
            const hoursEl = document.getElementById('hours');
            const minutesEl = document.getElementById('minutes');
            const secondsEl = document.getElementById('seconds');
            
            if (daysEl) daysEl.textContent = days.toString().padStart(2, '0');
            if (hoursEl) hoursEl.textContent = hours.toString().padStart(2, '0');
            if (minutesEl) minutesEl.textContent = minutes.toString().padStart(2, '0');
            if (secondsEl) secondsEl.textContent = seconds.toString().padStart(2, '0');
        } else {
            const countdownContainer = document.querySelector('.countdown-container');
            if (countdownContainer) {
                countdownContainer.innerHTML = 
                    '<div style="text-align: center; font-size: 2rem; color: var(--primary-gold);">🎉 The big day has arrived! 🎉</div>';
            }
        }
    }
    
    updateCountdownTimer();
    setInterval(updateCountdownTimer, 1000);
}

// RSVP form elküldése
async function submitRSVP(formData) {
    try {
        const response = await fetch(`${API_BASE_URL}/rsvp`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            showNotification('Thank you for your RSVP! We will contact you soon.', 'success');
            return true;
        } else {
            showNotification(result.message, 'error');
            return false;
        }
    } catch (error) {
        console.error('RSVP Error:', error);
        showNotification('Network error occurred', 'error');
        return false;
    }
}

// Segédfüggvények
function formatTime(timeString) {
    const time = new Date(`2000-01-01T${timeString}`);
    return time.toLocaleTimeString('hu-HU', {
        hour: '2-digit',
        minute: '2-digit'
    });
}


function showError(message) {
    showNotification(message, 'error');
}

// ===== UTILITY FUNCTIONS =====

// Debounce function for performance
function debounce(func, wait, immediate) {
    let timeout;
    return function executedFunction() {
        const context = this;
        const args = arguments;
        const later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

// Check if element is in viewport
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

// Format date for countdown
function formatDate(date) {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        weekday: 'long'
    };
    return date.toLocaleDateString('hu-HU', options);
}
