// Leib Elsősegély - Main JavaScript
class LeibWebsite {
    constructor() {
        this.apiBase = 'api.php';
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadPageData();
        this.setupSmoothScrolling();
        this.setupBackToTop();
        this.setupImageModal();
    }

    setupEventListeners() {
        // Mobile menu toggle
        const mobileMenu = document.getElementById('mobile-menu');
        const navMenu = document.getElementById('nav-menu');
        
        if (mobileMenu && navMenu) {
            mobileMenu.addEventListener('click', () => {
                mobileMenu.classList.toggle('active');
                navMenu.classList.toggle('active');
            });
        }

        // Navigation links
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = link.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    targetElement.scrollIntoView({ behavior: 'smooth' });
                    
                    // Update active nav link
                    navLinks.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');
                    
                    // Close mobile menu
                    mobileMenu.classList.remove('active');
                    navMenu.classList.remove('active');
                }
            });
        });

        // Product filter buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('filter-btn')) {
                this.filterProducts(e.target.dataset.category);
                
                // Update active filter button
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                e.target.classList.add('active');
            }
        });

        // Contact form
        const contactForm = document.getElementById('contact-form');
        if (contactForm) {
            contactForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleContactForm(e.target);
            });
        }

        // Scroll event for back to top button
        window.addEventListener('scroll', () => {
            this.toggleBackToTop();
            this.updateActiveNavLink();
        });
    }

    async loadPageData() {
        try {
            // Load home page data
            const homeData = await this.fetchData('/api/home');
            this.renderHomePage(homeData);

            // Load products
            const productsData = await this.fetchData('/api/products');
            this.renderProducts(productsData);

            // Load services
            const servicesData = await this.fetchData('/api/services');
            this.renderServices(servicesData);

        } catch (error) {
            console.error('Error loading page data:', error);
            this.showError('Hiba történt az adatok betöltése során.');
        }
    }

    async fetchData(endpoint) {
        try {
            const response = await fetch(this.apiBase + endpoint);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error('Fetch error:', error);
            throw error;
        }
    }

    renderHomePage(data) {
        if (data.page) {
            const aboutContent = document.getElementById('about-content');
            if (aboutContent) {
                aboutContent.innerHTML = this.formatContent(data.page.content);
            }
        }

        if (data.contact) {
            this.renderContactInfo(data.contact);
        }
    }

    renderServices(data) {
        const servicesGrid = document.getElementById('services-grid');
        if (!servicesGrid || !data.services) return;

        servicesGrid.innerHTML = data.services.map(service => `
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-${service.icon || 'check-circle'}"></i>
                </div>
                <h3 class="service-title">${service.title}</h3>
                <p class="service-description">${service.description}</p>
            </div>
        `).join('');
    }

    renderProducts(data) {
        const productsGrid = document.getElementById('products-grid');
        const categoryFilters = document.getElementById('category-filters');
        
        if (!productsGrid || !data.products) return;

        // Render category filters
        if (categoryFilters && data.categories) {
            categoryFilters.innerHTML = data.categories.map(category => `
                <button class="filter-btn" data-category="${category.category}">
                    ${this.formatCategoryName(category.category)}
                </button>
            `).join('');
        }

        // Store all products for filtering
        this.allProducts = data.products;
        
        // Render all products initially
        this.renderProductsGrid(data.products);
    }

    renderProductsGrid(products) {
        const productsGrid = document.getElementById('products-grid');
        if (!productsGrid) return;

        productsGrid.innerHTML = products.map(product => `
            <div class="product-card" data-category="${product.category || 'other'}">
                <img src="assets/images/${product.image}" alt="${product.name}" class="product-image" 
                     onerror="this.src='assets/images/leib_logo.webp'"
                     data-image-src="assets/images/${product.image}"
                     data-image-alt="${product.name}">
                <div class="product-content">
                    <h3 class="product-name">${product.name}</h3>
                    <p class="product-description">${product.description || ''}</p>
                    ${product.category ? `<span class="product-category">${this.formatCategoryName(product.category)}</span>` : ''}
                </div>
            </div>
        `).join('');
        
        // Add click event listeners to all product images
        const productImages = productsGrid.querySelectorAll('.product-image');
        productImages.forEach(img => {
            img.addEventListener('click', () => {
                const imageSrc = img.getAttribute('data-image-src');
                const imageAlt = img.getAttribute('data-image-alt');
                this.openImageModal(imageSrc, imageAlt);
            });
        });
    }

    filterProducts(category) {
        if (!this.allProducts) return;

        const filteredProducts = category === 'all' 
            ? this.allProducts 
            : this.allProducts.filter(product => product.category === category);

        this.renderProductsGrid(filteredProducts);
    }

    renderContactInfo(contact) {
        const contactInfo = document.getElementById('contact-info');
        if (!contactInfo || !contact) return;

        contactInfo.innerHTML = `
            <div class="contact-item">
                <i class="fas fa-building contact-icon"></i>
                <div>
                    <strong>${contact.company_name || 'Leib Elsősegély Felszerelések Forgalmazása'}</strong>
                </div>
            </div>
            <div class="contact-item">
                <i class="fas fa-user contact-icon"></i>
                <div>
                    <strong>${contact.owner_name || 'Leib Roland'}</strong>
                </div>
            </div>
            ${contact.email ? `
                <div class="contact-item">
                    <i class="fas fa-envelope contact-icon"></i>
                    <div>
                        <a href="mailto:${contact.email}">${contact.email}</a>
                    </div>
                </div>
            ` : ''}
            ${contact.phone ? `
                <div class="contact-item">
                    <i class="fas fa-phone contact-icon"></i>
                    <div>
                        <a href="tel:${contact.phone}">${contact.phone}</a>
                    </div>
                </div>
            ` : ''}
            ${contact.address ? `
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt contact-icon"></i>
                    <div>${contact.address}</div>
                </div>
            ` : ''}
            ${contact.description ? `
                <div class="contact-item">
                    <i class="fas fa-info-circle contact-icon"></i>
                    <div>${contact.description}</div>
                </div>
            ` : ''}
        `;
    }

    async handleContactForm(form) {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        
        // Simple validation
        if (!data.name || !data.email || !data.message) {
            this.showError('Kérjük, töltse ki az összes kötelező mezőt.');
            return;
        }

        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(data.email)) {
            this.showError('Kérjük, adjon meg egy érvényes e-mail címet.');
            return;
        }

        try {
            // Disable submit button to prevent double submission
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Küldés...';

            // Send form data to backend
            const response = await fetch(this.apiBase + '/api/contact', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                this.showSuccess(result.message);
                form.reset();
            } else {
                this.showError(result.message);
            }

        } catch (error) {
            console.error('Error sending contact form:', error);
            this.showError('Hiba történt az üzenet küldése során. Kérjük, próbálja újra később.');
        } finally {
            // Re-enable submit button
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Üzenet küldése';
        }
    }

    setupSmoothScrolling() {
        // Smooth scrolling for anchor links
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
    }

    setupImageModal() {
        const modal = document.getElementById('image-modal');
        const modalImage = document.getElementById('modal-image');
        const modalClose = document.getElementById('modal-close');
        
        // Close modal when clicking the X button
        if (modalClose) {
            modalClose.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.closeImageModal();
            });
        }
        
        // Close modal when clicking outside the image
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeImageModal();
                }
            });
        }
        
        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal && modal.classList.contains('show')) {
                this.closeImageModal();
            }
        });
    }
    
    openImageModal(imageSrc, imageAlt) {
        const modal = document.getElementById('image-modal');
        const modalImage = document.getElementById('modal-image');
        
        if (modal && modalImage) {
            modalImage.src = imageSrc;
            modalImage.alt = imageAlt;
            modal.classList.add('show');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }
    }
    
    closeImageModal() {
        const modal = document.getElementById('image-modal');
        
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = ''; // Restore scrolling
            console.log('Modal closed'); // Debug message
        }
    }

    setupBackToTop() {
        // Create back to top button if it doesn't exist
        let backToTopBtn = document.getElementById('back-to-top');
        if (!backToTopBtn) {
            backToTopBtn = document.createElement('button');
            backToTopBtn.id = 'back-to-top';
            backToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
            backToTopBtn.className = 'back-to-top';
            backToTopBtn.setAttribute('aria-label', 'Vissza a tetejére');
            document.body.appendChild(backToTopBtn);
        }

        // Add click event listener
        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    toggleBackToTop() {
        const backToTopBtn = document.getElementById('back-to-top');
        if (backToTopBtn) {
            if (window.pageYOffset > 300) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        }
    }

    updateActiveNavLink() {
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.nav-link');
        
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 100;
            if (window.pageYOffset >= sectionTop) {
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

    formatContent(content) {
        if (!content) return '';
        
        // Convert line breaks to paragraphs
        return content
            .split('\n\n')
            .map(paragraph => paragraph.trim())
            .filter(paragraph => paragraph.length > 0)
            .map(paragraph => `<p>${paragraph.replace(/\n/g, '<br>')}</p>`)
            .join('');
    }

    formatCategoryName(category) {
        if (!category) return '';
        
        const categoryMap = {
            'diagnosztikai': 'Diagnosztikai',
            'kezelő': 'Kezelés',
            'védő': 'Védő',
            'szekrény': 'Szekrény',
            'hordagy': 'Hordágy',
            'fertőtlenítő': 'Fertőtlenítő',
            'mentőláda': 'Mentőláda'
        };
        
        return categoryMap[category] || category.charAt(0).toUpperCase() + category.slice(1);
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    showError(message) {
        this.showNotification(message, 'error');
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;

        // Add styles
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            z-index: 10000;
            animation: slideIn 0.3s ease-out;
        `;

        document.body.appendChild(notification);

        // Remove after 5 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }
}

// Add CSS for notifications
const notificationStyles = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .notification-content {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
`;

// Add styles to head
const styleSheet = document.createElement('style');
styleSheet.textContent = notificationStyles;
document.head.appendChild(styleSheet);

// Initialize the website when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new LeibWebsite();
});

// Handle page load errors
window.addEventListener('error', (e) => {
    console.error('Page error:', e.error);
});

// Handle unhandled promise rejections
window.addEventListener('unhandledrejection', (e) => {
    console.error('Unhandled promise rejection:', e.reason);
});
