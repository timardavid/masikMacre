# Esküvői Weboldal - Teljes Alkalmazás

## Áttekintés
Ez egy teljes webes alkalmazás esküvői meghívóhoz, amely dinamikus tartalommal rendelkezik és könnyen testreszabható.

## Főbb jellemzők

### 🎯 Dinamikus tartalom
- **SQL adatbázis** teszt adatokkal és lorem ipsum szövegekkel
- **PHP backend API** adatok lekéréséhez és validálásához
- **Frontend** dinamikus adatok megjelenítésével
- **Képkezelési rendszer** könnyű képcsere lehetőséggel

### 📅 Jövőbeli dátum
- **Esküvői dátum:** 2025. június 15.
- **Visszaszámláló** működő countdown timer
- **RSVP határidő:** 2025. május 15.

### 🛠 Technológiai stack
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Backend:** PHP 7.4+
- **Adatbázis:** MySQL 5.7+
- **API:** RESTful API végpontok

## Fájlstruktúra

```
wedding-website-complete.zip
├── index.html                 # Főoldal
├── assets/
│   ├── css/style.css         # Stílusok
│   ├── js/script.js          # JavaScript funkcionalitás
│   └── images/
│       ├── gallery/           # Galéria képek mappája
│       └── *.svg             # Ikonok és favicon
├── api/
│   ├── index.php             # Fő API végpont
│   └── upload.php            # Képfeltöltés kezelő
├── config/
│   └── config.php            # Konfigurációs fájl
├── database/
│   └── wedding_db.sql        # SQL adatbázis séma és teszt adatok
├── documentation/
│   └── installation-guide.md # Telepítési útmutató
├── INSTALLATION.md           # Részletes telepítési útmutató
└── README.md                 # Projekt dokumentáció
```

## Telepítési lépések

### 1. Fájlok kicsomagolása
```bash
unzip wedding-website-complete.zip
```

### 2. Webszerver beállítása
- Töltsd fel a fájlokat a webszerver root könyvtárába
- Győződj meg róla, hogy PHP és MySQL elérhető

### 3. Adatbázis létrehozása
```sql
-- Importáld a database/wedding_db.sql fájlt
mysql -u username -p database_name < database/wedding_db.sql
```

### 4. Konfiguráció
Szerkeszd a `config/config.php` fájlt:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'wedding_website');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 5. Képek feltöltése
- Töltsd fel a képeket az `assets/images/gallery/` mappába
- Használd a következő fájlneveket: `gallery1.jpg`, `gallery2.jpg`, stb.

## API végpontok

### Adatok lekérése
- `GET /api/couple` - Pár információk
- `GET /api/events` - Események listája
- `GET /api/story` - Sztori idővonal
- `GET /api/gallery` - Galéria képek
- `GET /api/contact` - Kapcsolattartási információk
- `GET /api/settings` - Weboldal beállítások
- `GET /api/all` - Összes adat egyszerre

### RSVP küldése
- `POST /api/rsvp` - RSVP válasz küldése
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "attendance": "yes",
    "guests": 2,
    "message": "Looking forward to it!"
}
```

### Képfeltöltés
- `POST /api/upload.php` - Képfeltöltés
```javascript
const formData = new FormData();
formData.append('image', fileInput.files[0]);
formData.append('category', 'engagement');
```

## Testreszabás

### Adatok módosítása
1. **Pár információk:** `couples` tábla
2. **Események:** `events` tábla
3. **Sztori:** `story_timeline` tábla
4. **Galéria:** `gallery_images` tábla
5. **Beállítások:** `site_settings` tábla

### Képek cseréje
1. **Automatikus:** API-n keresztül feltöltés
2. **Manuális:** Fájlok cseréje + adatbázis frissítés

### Stílusok módosítása
- Szerkeszd a `assets/css/style.css` fájlt
- CSS változók a `:root` szekcióban

## Biztonsági funkciók

### Adatvalidálás
- Email cím validálás
- Telefonszám ellenőrzés
- Fájltípus és méret validálás
- SQL injection védelem (prepared statements)

### Biztonsági beállítások
- CORS fejlécek
- Fájl feltöltési korlátok
- MIME típus ellenőrzés
- XSS védelem

## Teljesítmény optimalizálás

### Frontend
- Lazy loading képekhez
- Debounced scroll események
- CSS és JS minifikálás
- Képoptimalizálás

### Backend
- PDO prepared statements
- Adatbázis indexek
- Caching stratégia
- Error handling

## Támogatott böngészők
- Chrome 80+
- Firefox 75+
- Safari 13+
- Edge 80+

## Responsive design
- Mobile-first megközelítés
- Flexbox és CSS Grid
- Breakpoints: 768px, 480px
- Touch-friendly interfész

## Accessibility
- ARIA címkék
- Keyboard navigation
- Screen reader támogatás
- Kontrasztos színek

## Hibaelhárítás

### Gyakori problémák
1. **Adatbázis kapcsolat:** Ellenőrizd a `config.php` beállításokat
2. **Képek nem jelennek meg:** Ellenőrizd a fájlneveket és útvonalakat
3. **API hibák:** Nézd meg a PHP error logokat
4. **RSVP form:** Ellenőrizd a JavaScript konzolt

### Debug mód
```php
// config/config.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Licenc
Ez a projekt MIT licenc alatt áll. Lásd a `LICENSE.txt` fájlt részletekért.

## Kapcsolat és támogatás
- **Dokumentáció:** Lásd a `documentation/` mappát
- **Telepítési útmutató:** `INSTALLATION.md`
- **Projekt összefoglaló:** `PROJECT-SUMMARY.md`

---

**Készítve:** 2024  
**Verzió:** 1.0  
**Kompatibilitás:** PHP 7.4+, MySQL 5.7+
