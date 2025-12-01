/**
 * Dynamic Content Loader
 * Loads content from database via API
 */

class ContentLoader {
    constructor() {
        this.apiBase = 'api.php?endpoint=';
        this.csrfToken = null;
    }

    /**
     * Initialize content loading
     */
    async init() {
        try {
            // Load CSRF token
            await this.loadCSRFToken();
            
            // Load all dynamic content
            await Promise.all([
                this.loadSiteSettings(),
                this.loadServices(),
                this.loadClasses(),
                this.loadAboutContent(),
                this.loadContactInfo()
            ]);
            
            console.log('All content loaded successfully');
        } catch (error) {
            console.error('Error loading content:', error);
        }
    }

    /**
     * Load CSRF token
     */
    async loadCSRFToken() {
        try {
            const response = await fetch('api.php?endpoint=csrf-token');
            const data = await response.json();
            this.csrfToken = data.token;
        } catch (error) {
            console.error('Error loading CSRF token:', error);
        }
    }

    /**
     * Load site settings
     */
    async loadSiteSettings() {
        try {
            const response = await fetch(this.apiBase + 'site-settings');
            const data = await response.json();
            
            if (data.status === 200) {
                this.updateSiteSettings(data.data);
            }
        } catch (error) {
            console.error('Error loading site settings:', error);
        }
    }

    /**
     * Update site settings in DOM
     */
    updateSiteSettings(settings) {
        // Update page title
        if (settings.site_name) {
            document.title = settings.site_name + ' - ' + settings.site_tagline;
        }

        // Update hero section
        if (settings.hero_title_line1) {
            const titleLines = document.querySelectorAll('.title-line');
            if (titleLines[0]) titleLines[0].textContent = settings.hero_title_line1;
            if (titleLines[1]) titleLines[1].textContent = settings.hero_title_line2;
            if (titleLines[2]) titleLines[2].textContent = settings.hero_title_line3;
        }

        if (settings.hero_description) {
            const heroDesc = document.querySelector('.hero-description');
            if (heroDesc) heroDesc.textContent = settings.hero_description;
        }

        // Update meta description
        if (settings.site_description) {
            const metaDesc = document.querySelector('meta[name="description"]');
            if (metaDesc) metaDesc.setAttribute('content', settings.site_description);
        }

        // Update logo
        if (settings.site_name) {
            const logo = document.querySelector('.logo');
            if (logo) logo.textContent = settings.site_name;
        }

        // Update footer logo
        if (settings.site_name) {
            const footerLogo = document.querySelector('.footer-logo');
            if (footerLogo) footerLogo.textContent = settings.site_name;
        }
    }

    /**
     * Load services
     */
    async loadServices() {
        try {
            const response = await fetch(this.apiBase + 'services');
            const data = await response.json();
            
            if (data.status === 200) {
                this.updateServices(data.data);
            }
        } catch (error) {
            console.error('Error loading services:', error);
        }
    }

    /**
     * Update services in DOM
     */
    updateServices(services) {
        const servicesGrid = document.querySelector('.services-grid');
        if (!servicesGrid) return;

        servicesGrid.innerHTML = '';

        services.forEach(service => {
            const serviceCard = document.createElement('div');
            serviceCard.className = 'service-card';
            
            const features = service.features ? service.features.map(feature => `<li>${feature}</li>`).join('') : '';
            
            serviceCard.innerHTML = `
                <div class="service-image">
                    <div class="service-placeholder">${service.icon}</div>
                </div>
                <div class="service-content">
                    <h3 class="service-title">${service.title}</h3>
                    <p class="service-description">${service.description}</p>
                    <ul class="service-features">
                        ${features}
                    </ul>
                </div>
            `;
            
            servicesGrid.appendChild(serviceCard);
        });
    }

    /**
     * Load classes
     */
    async loadClasses() {
        try {
            const response = await fetch(this.apiBase + 'classes');
            const data = await response.json();
            
            if (data.status === 200) {
                this.updateClasses(data.data);
            }
        } catch (error) {
            console.error('Error loading classes:', error);
        }
    }

    /**
     * Update classes in DOM
     */
    updateClasses(classes) {
        const classesGrid = document.querySelector('.classes-grid-view');
        if (!classesGrid) return;

        classesGrid.innerHTML = '';

        classes.forEach(classItem => {
            const classCard = document.createElement('div');
            classCard.className = 'class-card';
            classCard.setAttribute('data-category', classItem.category);
            classCard.setAttribute('data-schedule', classItem.schedule_type || 'all');
            
            // Determine badge class based on schedule
            let badgeClass = 'morning';
            if (classItem.schedule_type === 'evening') badgeClass = 'evening';
            if (classItem.schedule_type === 'weekend') badgeClass = 'weekend';
            
            classCard.innerHTML = `
                <div class="class-header">
                    <div class="class-icon">${classItem.icon}</div>
                    <div class="class-badge ${badgeClass}">${classItem.schedule_type || 'All'}</div>
                </div>
                <div class="class-info">
                    <h4>${classItem.title}</h4>
                    <p class="class-description">${classItem.description}</p>
                </div>
                <div class="class-schedule">
                    <div class="schedule-days">${classItem.schedule_days || 'Mon-Fri'}</div>
                    <div class="schedule-time">${classItem.schedule_time || '9:00 AM'}</div>
                </div>
                <div class="class-details">
                    <div class="detail-item">
                        <span class="detail-label">Duration:</span>
                        <span class="detail-value">${classItem.duration || '60 min'}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Level:</span>
                        <span class="detail-value">${classItem.level || 'All Levels'}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Price:</span>
                        <span class="detail-value">$${classItem.price || '25'}</span>
                    </div>
                </div>
                <div class="class-actions">
                    <button class="btn btn-primary btn-book">Book Now</button>
                    <button class="btn btn-secondary btn-learn">Learn More</button>
                </div>
            `;
            
            classesGrid.appendChild(classCard);
        });

        // Reinitialize the filter functionality after loading classes
        this.initClassFilter();
    }

    /**
     * Initialize class filter functionality
     */
    initClassFilter() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        const classCards = document.querySelectorAll('.class-card');

        filterButtons.forEach(button => {
            // Remove existing event listeners to prevent duplicates
            button.replaceWith(button.cloneNode(true));
        });

        // Re-select buttons after cloning
        const newFilterButtons = document.querySelectorAll('.filter-btn');
        
        newFilterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-category');
                
                // Update active button
                newFilterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                // Filter class cards
                classCards.forEach(card => {
                    const category = card.getAttribute('data-category');
                    
                    if (filter === 'all' || category === filter) {
                        card.style.display = 'block';
                        card.style.animation = 'fadeInUp 0.5s ease forwards';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    }

    /**
     * Load about content
     */
    async loadAboutContent() {
        try {
            const response = await fetch(this.apiBase + 'about');
            const data = await response.json();
            
            if (data.status === 200) {
                this.updateAboutContent(data.data);
            }
        } catch (error) {
            console.error('Error loading about content:', error);
        }
    }

    /**
     * Update about content in DOM
     */
    updateAboutContent(aboutItems) {
        const aboutGrid = document.querySelector('.about-grid');
        if (!aboutGrid) return;

        aboutGrid.innerHTML = '';

        aboutItems.forEach(item => {
            const aboutCard = document.createElement('div');
            aboutCard.className = 'about-card';
            
            aboutCard.innerHTML = `
                <div class="card-icon">
                    <span style="font-size: 2rem;">${item.icon}</span>
                </div>
                <h3 class="card-title">${item.title}</h3>
                <p class="card-description">${item.description}</p>
            `;
            
            aboutGrid.appendChild(aboutCard);
        });
    }

    /**
     * Load contact info
     */
    async loadContactInfo() {
        try {
            const response = await fetch(this.apiBase + 'contact-info');
            const data = await response.json();
            
            if (data.status === 200) {
                this.updateContactInfo(data.data);
            }
        } catch (error) {
            console.error('Error loading contact info:', error);
        }
    }

    /**
     * Update contact info in DOM
     */
    updateContactInfo(contactItems) {
        const contactInfo = document.querySelector('.contact-info');
        if (!contactInfo) return;

        contactInfo.innerHTML = '';

        contactItems.forEach(item => {
            const contactItem = document.createElement('div');
            contactItem.className = 'contact-item';
            
            contactItem.innerHTML = `
                <div class="contact-icon">
                    <span style="font-size: 1.5rem;">${item.icon}</span>
                </div>
                <div class="contact-details">
                    <h3 class="contact-title">${item.title}</h3>
                    <p class="contact-text">${item.content}</p>
                </div>
            `;
            
            contactInfo.appendChild(contactItem);
        });
    }

    /**
     * Submit contact form
     */
    async submitContactForm(formData) {
        try {
            const response = await fetch(this.apiBase + 'contact-form', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    ...formData,
                    csrf_token: this.csrfToken
                })
            });

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error submitting contact form:', error);
            return { status: 500, message: 'Network error' };
        }
    }
}

// Initialize content loader when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const contentLoader = new ContentLoader();
    contentLoader.init();
    
    // Make contentLoader globally available for form submission
    window.contentLoader = contentLoader;
});
