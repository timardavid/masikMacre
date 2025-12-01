const navbar = document.querySelector('.navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) { navbar.classList.add('scrolled'); } else { navbar.classList.remove('scrolled'); }
        });

        const fadeElements = document.querySelectorAll('.fade-in');
        const appearOptions = { threshold: 0.2, rootMargin: "0px 0px -50px 0px" };
        const appearOnScroll = new IntersectionObserver(function(entries, appearOnScroll) {
            entries.forEach(entry => {
                if (!entry.isIntersecting) { return; } else { entry.target.classList.add('appear'); appearOnScroll.unobserve(entry.target); }
            });
        }, appearOptions);
        fadeElements.forEach(element => { appearOnScroll.observe(element); });

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault(); document.querySelector(this.getAttribute('href')).scrollIntoView({ behavior: 'smooth' });
            });
        });