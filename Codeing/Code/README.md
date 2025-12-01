# Fitness Studio - Modern Weboldal Sablon

## 📋 Leírás

Ez egy modern, letisztult és professzionális weboldal sablon, amelyet **fitness és wellness központok** számára terveztünk. A sablon követi a **Bold Minimalism** design trendet, és tökéletesen alkalmas ThemeForest értékesítésre.

## ✨ Főbb Jellemzők

### 🎨 Design
- **Bold Minimalism** stílus
- Modern, letisztult megjelenés
- Erős színkontrasztok (#ff6b35 narancs szín)
- Professzionális tipográfia (Inter font)
- Smooth animációk és átmenetek

### 📱 Reszponzív Design
- Mobil-first megközelítés
- Teljesen reszponzív minden eszközön
- Optimalizált tablet és desktop nézetek
- Touch-friendly navigáció

### 🚀 Technológiai Alapok
- **HTML5** semantic elemekkel
- **CSS3** modern funkciókkal (Grid, Flexbox, Custom Properties)
- **JavaScript ES6+** interaktivitásért
- **SEO optimalizált** struktúra
- **Teljesítmény optimalizált** kód

### 🏃‍♀️ Fitness Specifikus Funkciók
- Órarend szekció szűrőkkel
- Szolgáltatás bemutatás
- Kapcsolat űrlap szolgáltatás választóval
- Edzői információk
- Nyitvatartás megjelenítés

## 📁 Fájlstruktúra

```
fitness-studio-template/
├── index.html                 # Fő HTML fájl
├── assets/
│   ├── css/
│   │   ├── style.css         # Fő stílusok
│   │   └── responsive.css    # Reszponzív stílusok
│   ├── js/
│   │   └── script.js         # JavaScript funkcionalitás
│   └── images/               # Képek mappája
├── README.md                 # Ez a fájl
└── LICENSE                   # Licenc információ
```

## 🛠️ Telepítés és Használat

### 1. Fájlok Letöltése
```bash
# Klónozd a repository-t vagy töltsd le a ZIP fájlt
git clone [repository-url]
cd fitness-studio-template
```

### 2. Testreszabás

#### Színek Módosítása
A fő színeket a `assets/css/style.css` fájlban találod:
```css
:root {
    --primary-color: #ff6b35;    /* Fő szín */
    --secondary-color: #1a1a1a;   /* Szöveg szín */
    --accent-color: #ff8c42;     /* Kiemelő szín */
}
```

#### Tartalom Szerkesztése
1. **Cég neve**: Keress rá a "FitnessStudio" szövegre és cseréld le
2. **Kapcsolat adatok**: Frissítsd a telefonszámot, emailt és címet
3. **Szolgáltatások**: Módosítsd az órákat és szolgáltatásokat
4. **Képek**: Cseréld le a placeholder emojikat valódi képekre

#### Képek Hozzáadása
```html
<!-- Helyettük: -->
<div class="service-placeholder">💪</div>

<!-- Használj: -->
<img src="assets/images/personal-training.jpg" alt="Személyi edzés" class="service-image">
```

### 3. Szerverre Feltöltés
1. Töltsd fel az összes fájlt a webhosting szolgáltatódra
2. Győződj meg róla, hogy az `index.html` a gyökér könyvtárban van
3. Teszteld a weboldalt különböző eszközökön

## 🎯 SEO Optimalizálás

### Meta Tag-ek
```html
<meta name="description" content="Modern fitness és wellness központ. Személyi edzés, jóga órák, csoportos edzések és egészséges életmód tanácsadás.">
<meta name="keywords" content="fitness, jóga, személyi edzés, wellness, egészség, edzőterem, csoportos edzés">
```

### Strukturált Adatok
A sablon készen áll a Google My Business és helyi SEO integrációhoz.

## 📱 Böngésző Támogatás

- **Chrome** 60+
- **Firefox** 55+
- **Safari** 12+
- **Edge** 79+
- **Mobil böngészők** (iOS Safari, Chrome Mobile)

## 🎨 Testreszabási Lehetőségek

### Színpaletta Változtatás
```css
/* Példa: Kék színpaletta */
:root {
    --primary-color: #3b82f6;
    --secondary-color: #1e40af;
    --accent-color: #60a5fa;
}
```

### Betűtípus Cseréje
```css
/* Google Fonts import */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

body {
    font-family: 'Poppins', sans-serif;
}
```

### Animációk Kikapcsolása
```css
/* Accessibility-hez */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
```

## 🔧 Fejlesztői Információk

### CSS Architektúra
- **Mobile-first** responsive design
- **BEM** metodológia osztálynevekhez
- **CSS Custom Properties** színekhez
- **Flexbox és Grid** layout-hoz

### JavaScript Funkciók
- Smooth scrolling navigáció
- Mobil menü toggle
- Órák szűrő rendszer
- Kapcsolat űrlap validáció
- Scroll-triggered animációk

### Teljesítmény Optimalizálás
- Minified CSS és JS (production verzióban)
- Lazy loading támogatás
- Optimized images
- Critical CSS inline

## 📞 Támogatás

Ha kérdésed van a sablonnal kapcsolatban:

1. **Dokumentáció**: Olvasd el ezt a README fájlt
2. **Kód kommentek**: A CSS és JS fájlokban részletes kommentek
3. **Issues**: Jelentsd a problémákat a GitHub repository-ban

## 📄 Licenc

Ez a sablon **MIT licenc** alatt áll, ami azt jelenti, hogy:
- ✅ Szabadon használhatod kereskedelmi projektekben
- ✅ Módosíthatod és terjesztheted
- ✅ Sublicencelheted
- ❌ Nem vállalunk felelősséget a szoftverért

## 🚀 Jövőbeli Fejlesztések

### Tervezett Funkciók
- [ ] WordPress integráció
- [ ] Online foglalási rendszer
- [ ] Blog szekció
- [ ] Galéria modul
- [ ] Többnyelvű támogatás
- [ ] Dark mode

### Verzió Történet
- **v1.0.0** - Első kiadás (2024)
  - Alapvető fitness sablon
  - Reszponzív design
  - Modern animációk

## 💡 Tippek a Legjobb Használathoz

1. **Képek**: Használj minőségi, professzionális fotókat
2. **Tartalom**: Írj egyedi, releváns szövegeket
3. **SEO**: Frissítsd a meta tag-eket a saját tartalmadra
4. **Tesztelés**: Mindig teszteld különböző eszközökön
5. **Backup**: Mindig készíts biztonsági másolatot

---

**Készítette**: ModernMinimal Designs  
**Verzió**: 1.0.0  
**Utolsó frissítés**: 2024

*Ez a sablon kifejezetten ThemeForest értékesítésre lett tervezve, és követi a platform minőségi követelményeit.*
