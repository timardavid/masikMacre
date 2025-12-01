# 🚀 Telepítési Útmutató - Fitness Studio Sablon

## 📋 Előfeltételek

- Webhosting szolgáltatás (Apache/Nginx)
- FTP/SFTP hozzáférés vagy cPanel File Manager
- Modern webböngésző (Chrome, Firefox, Safari, Edge)

## 📁 Fájlok Feltöltése

### 1. Fájlok Kicsomagolása
```bash
# Ha ZIP fájlt kaptál, csomagold ki:
unzip fitness-studio-template.zip
```

### 2. FTP Feltöltés
1. Kapcsolódj FTP klienssel a szerveredhez
2. Navigálj a `public_html` vagy `www` mappába
3. Töltsd fel az összes fájlt:
   - `index.html` → gyökér könyvtár
   - `assets/` mappa → teljes mappa tartalommal
   - `README.md` és `LICENSE` → opcionális

### 3. cPanel File Manager
1. Jelentkezz be a cPanel-be
2. Nyisd meg a File Manager-t
3. Navigálj a `public_html` mappába
4. Töltsd fel a fájlokat drag & drop módszerrel

## ⚙️ Alapvető Beállítások

### 1. Domain Beállítás
Győződj meg róla, hogy a domain a megfelelő könyvtárra mutat:
```
example.com → /public_html/index.html
```

### 2. SSL Tanúsítvány
```bash
# Let's Encrypt SSL (ingyenes)
# A legtöbb hosting szolgáltató automatikusan kezeli
```

### 3. .htaccess Beállítás (Apache)
Hozz létre egy `.htaccess` fájlt a gyökér könyvtárban:
```apache
# SEO friendly URLs
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^([^/]+)/?$ index.html [L,QSA]

# Gzip compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Browser caching
<IfModule mod_expires.c>
    ExpiresActive on
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
</IfModule>
```

## 🎨 Testreszabás Lépései

### 1. Cég Információk Frissítése

#### Logo és Cég Név
```html
<!-- index.html fájlban -->
<a href="#" class="logo">FitnessStudio</a>
```

#### Kapcsolat Adatok
```html
<!-- Telefon -->
<p class="contact-text">+36 XX XXX XXXX</p>

<!-- Email -->
<p class="contact-text">info@fitnessstudio.hu</p>

<!-- Cím -->
<p class="contact-text">Város, Utca név 123<br>1234 Magyarország</p>
```

### 2. Színek Módosítása
```css
/* assets/css/style.css fájlban */
:root {
    --primary-color: #ff6b35;    /* Fő szín */
    --secondary-color: #1a1a1a;   /* Szöveg szín */
    --accent-color: #ff8c42;     /* Kiemelő szín */
}
```

### 3. Képek Hozzáadása

#### Szolgáltatás Képek
```html
<!-- Helyettük: -->
<div class="service-placeholder">💪</div>

<!-- Használj: -->
<img src="assets/images/personal-training.jpg" alt="Személyi edzés" class="service-image">
```

#### Órák Képek
```html
<!-- Helyettük: -->
<div class="class-placeholder">🌅</div>

<!-- Használj: -->
<img src="assets/images/morning-yoga.jpg" alt="Reggeli jóga" class="class-image">
```

### 4. Órák Beállítása
```html
<!-- Órák szekcióban -->
<div class="class-item" data-category="morning">
    <div class="class-image">
        <div class="class-placeholder">🌅</div>
        <div class="class-overlay">
            <h3 class="class-title">Reggeli Jóga</h3>
            <p class="class-description">Hétfő, Szerda, Péntek 7:00-8:00</p>
            <a href="#contact" class="class-link">Jelentkezés</a>
        </div>
    </div>
</div>
```

## 📧 Email Beállítások

### 1. Kapcsolat Űrlap
A jelenlegi sablon statikus, de könnyen integrálható:

#### PHP Backend Példa
```php
<?php
if ($_POST) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $service = $_POST['service'];
    $message = $_POST['message'];
    
    $to = "info@fitnessstudio.hu";
    $subject = "Új üzenet: " . $service;
    $body = "Név: $name\nEmail: $email\nTelefon: $phone\nSzolgáltatás: $service\nÜzenet: $message";
    
    mail($to, $subject, $body);
    echo "Üzenet elküldve!";
}
?>
```

#### JavaScript AJAX Példa
```javascript
// assets/js/script.js fájlban
contactForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('send-email.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        showNotification('Üzenet sikeresen elküldve!', 'success');
        contactForm.reset();
    })
    .catch(error => {
        showNotification('Hiba történt!', 'error');
    });
});
```

## 🔍 SEO Optimalizálás

### 1. Meta Tag-ek Frissítése
```html
<head>
    <meta name="description" content="Modern fitness és wellness központ. Személyi edzés, jóga órák, csoportos edzések és egészséges életmód tanácsadás.">
    <meta name="keywords" content="fitness, jóga, személyi edzés, wellness, egészség, edzőterem, csoportos edzés">
    <meta name="author" content="Fitness Studio">
    <title>Fitness Studio - Modern Fitness & Wellness Központ</title>
</head>
```

### 2. Google My Business
1. Regisztrálj Google My Business fiókot
2. Add hozzá a cég adataidat
3. Tölts fel képeket
4. Kérj értékeléseket ügyfelektől

### 3. Google Analytics
```html
<!-- Google Analytics kód hozzáadása -->
<script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'GA_MEASUREMENT_ID');
</script>
```

## 📱 Mobil Optimalizálás

### 1. Viewport Beállítás
```html
<meta name="viewport" content="width=device-width, initial-scale=1.0">
```

### 2. Touch Icons
```html
<link rel="apple-touch-icon" sizes="180x180" href="assets/images/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon-16x16.png">
```

## 🚀 Teljesítmény Optimalizálás

### 1. Képek Optimalizálása
```bash
# Használj eszközöket a képek tömörítéséhez:
# - TinyPNG
# - ImageOptim
# - Squoosh
```

### 2. CSS/JS Minification
```bash
# Production verzióhoz:
# - CSS: csso, clean-css
# - JS: uglify-js, terser
```

### 3. CDN Használata
```html
<!-- Google Fonts CDN -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
```

## ✅ Tesztelési Checklist

### Funkcionális Tesztek
- [ ] Navigáció működik minden oldalon
- [ ] Mobil menü megnyílik/bezárul
- [ ] Órák szűrő működik
- [ ] Kapcsolat űrlap validáció
- [ ] Smooth scrolling
- [ ] Animációk lejátszódnak

### Reszponzív Tesztek
- [ ] Mobil (320px-768px)
- [ ] Tablet (768px-1024px)
- [ ] Desktop (1024px+)
- [ ] Landscape/Portrait orientáció

### Böngésző Tesztek
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge
- [ ] Mobil böngészők

### SEO Tesztek
- [ ] Meta tag-ek helyesek
- [ ] Alt szövegek képekhez
- [ ] Heading struktúra logikus
- [ ] Page speed optimalizált

## 🆘 Hibaelhárítás

### Gyakori Problémák

#### 1. Stílusok nem töltődnek be
```html
<!-- Ellenőrizd a CSS fájl útvonalát -->
<link rel="stylesheet" href="assets/css/style.css">
```

#### 2. JavaScript nem működik
```html
<!-- Ellenőrizd a JS fájl útvonalát -->
<script src="assets/js/script.js"></script>
```

#### 3. Képek nem jelennek meg
```html
<!-- Ellenőrizd a kép útvonalát és fájlnévét -->
<img src="assets/images/example.jpg" alt="Leírás">
```

#### 4. Mobil menü nem működik
```javascript
// Ellenőrizd, hogy a JavaScript betöltődik
console.log('Script loaded');
```

## 📞 Támogatás

Ha problémába ütközöl:

1. **Ellenőrizd a konzolt** (F12 → Console)
2. **Nézd meg a Network tabot** (F12 → Network)
3. **Teszteld különböző böngészőkben**
4. **Ellenőrizd a fájl útvonalakat**

---

**Sikeres telepítést! 🎉**

*Ha kérdésed van, nézd meg a README.md fájlt vagy keresd fel a támogatást.*
