document.addEventListener('DOMContentLoaded', () => {
    
    // --- Mobil Menü Kezelés (RÉGI) ---
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');

    if(hamburger) {
        hamburger.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
    }

    // Ha rákattintunk egy menüpontra, zárja be a menüt (mobilon)
    document.querySelectorAll('.nav-links li a').forEach(link => {
        link.addEventListener('click', () => {
            if(navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
            }
        });
    });

    // --- Sima görgetés (Smooth Scroll) (RÉGI) ---
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            /* Csak akkor, ha nem a főoldalra mutat (pl. #) */
            if(this.getAttribute('href') !== '#') {
                 e.preventDefault();
                 const target = document.querySelector(this.getAttribute('href'));
                 if (target) {
                     target.scrollIntoView({
                         behavior: 'smooth'
                     });
                 }
            }
        });
    });

    // --- Navbar háttér váltás görgetéskor (RÉGI) ---
    const navbar = document.querySelector('.navbar');
    if(navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.style.boxShadow = "0 2px 10px rgba(0,0,0,0.1)";
            } else {
                navbar.style.boxShadow = "none";
            }
        });
    }

    // Megkeressük az összes elemet, amin rajta van a 'fade-in' class
    const faders = document.querySelectorAll('.fade-in');

    // Beállítások az observernek
    const appearOptions = {
        threshold: 0.15, // Akkor aktiválódjon, ha az elem 15%-a már látszik
        rootMargin: "0px 0px -50px 0px" // Kicsit hamarabb aktiválódjon, mielőtt teljesen beér
    };

    // Létrehozzuk a megfigyelőt
    const appearOnScroll = new IntersectionObserver(function(entries, appearOnScroll) {
        entries.forEach(entry => {
            // Ha nem látszik az elem, nem csinálunk semmit
            if (!entry.isIntersecting) {
                return;
            } else {
                // Ha látszik, hozzáadjuk az 'appear' class-t -> elindul a CSS animáció
                entry.target.classList.add('appear');
                // És leállítjuk a megfigyelést erről az elemről (csak egyszer animáljon be)
                appearOnScroll.unobserve(entry.target);
            }
        });
    }, appearOptions);

    // Ráállítjuk a megfigyelőt minden 'fade-in' elemre
    faders.forEach(fader => {
        appearOnScroll.observe(fader);
    });
});