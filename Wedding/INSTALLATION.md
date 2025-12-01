# Esküvői Weboldal - Telepítési útmutató

## Rendszerkövetelmények

### Szerver követelmények
- **PHP:** 7.4 vagy újabb
- **MySQL:** 5.7 vagy újabb (vagy MariaDB 10.2+)
- **Webszerver:** Apache vagy Nginx
- **PHP bővítmények:** PDO, PDO_MySQL, GD, fileinfo

### Lokális fejlesztéshez
- **XAMPP** (Windows/Mac/Linux)
- **WAMP** (Windows)
- **MAMP** (Mac)
- **Docker** (opcionális)

## Telepítési lépések

### 1. Fájlok feltöltése
1. Töltsd fel az összes fájlt a webszerver root könyvtárába
2. Győződj meg róla, hogy a következő mappák írhatók:
   - `assets/images/gallery/`
   - `uploads/` (ha használod)

### 2. Adatbázis létrehozása
```sql
-- Futtasd le a következő SQL fájlt:
source database/wedding_db.sql;
```

Vagy importáld a `database/wedding_db.sql` fájlt phpMyAdmin-ban.

### 3. Adatbázis kapcsolat beállítása
Szerkeszd a `config/config.php` fájlt:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'wedding_website');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 4. Képek feltöltése
1. Töltsd fel a képeket az `assets/images/gallery/` mappába
2. A fájlnevek megfeleljenek az adatbázisban tárolt neveknek:
   - `gallery1.jpg`
   - `gallery2.jpg`
   - `gallery3.jpg`
   - `gallery4.jpg`
   - `gallery5.jpg`
   - `gallery6.jpg`

### 5. Weboldal tesztelése
1. Nyisd meg a böngészőt
2. Navigálj a weboldal URL-jére
3. Ellenőrizd, hogy minden szekció betöltődik
4. Teszteld az RSVP formot

## Konfiguráció

### Adatbázis beállítások
A `config/config.php` fájlban módosíthatod:
- Adatbázis kapcsolat paramétereket
- Fájl feltöltési korlátokat
- Email beállításokat (opcionális)

### Weboldal tartalom módosítása
Az adatbázis `site_settings` táblájában módosíthatod:
- Weboldal címet
- Leírásokat
- Üzeneteket

### Pár információk módosítása
Az `couples` táblában módosíthatod:
- Pár neveit
- Esküvői dátumot
- Esküvői időt

## API végpontok

### Adatok lekérése
- `GET /api/couple` - Pár információk
- `GET /api/events` - Események
- `GET /api/story` - Sztori idővonal
- `GET /api/gallery` - Galéria képek
- `GET /api/contact` - Kapcsolattartási információk
- `GET /api/settings` - Weboldal beállítások
- `GET /api/all` - Összes adat egyszerre

### RSVP küldése
- `POST /api/rsvp` - RSVP válasz küldése

### Képfeltöltés
- `POST /api/upload.php` - Képfeltöltés

## Hibaelhárítás

### Gyakori problémák

#### 1. Adatbázis kapcsolat hiba
- Ellenőrizd a `config/config.php` beállításokat
- Győződj meg róla, hogy a MySQL szolgáltatás fut
- Ellenőrizd a felhasználói jogosultságokat

#### 2. Képek nem jelennek meg
- Ellenőrizd a fájlneveket az adatbázisban
- Győződj meg róla, hogy a képek léteznek a helyes mappában
- Ellenőrizd a fájl jogosultságokat

#### 3. API hibák
- Ellenőrizd a PHP error logokat
- Győződj meg róla, hogy a PDO bővítmény telepítve van
- Teszteld a kapcsolatot a böngésző fejlesztői eszközeivel

#### 4. RSVP form nem működik
- Ellenőrizd a JavaScript konzolt hibákért
- Győződj meg róla, hogy az API elérhető
- Ellenőrizd az adatbázis kapcsolatot

### Debug mód
A `config/config.php` fájlban bekapcsolhatod a debug módot:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Biztonsági megjegyzések

### Fontos biztonsági beállítások
1. **Adatbázis jelszó:** Használj erős jelszót
2. **Fájl jogosultságok:** Csak szükséges mappák legyenek írhatók
3. **PHP beállítások:** Kapcsold ki a `display_errors`-t éles környezetben
4. **HTTPS:** Használj SSL tanúsítványt éles környezetben

### Ajánlott biztonsági intézkedések
- Rendszeres adatbázis biztonsági mentés
- PHP és MySQL frissítések
- Tűzfal beállítások
- Intrusion detection rendszer

## Támogatás

### Dokumentáció
- `README.md` - Általános információk
- `PROJECT-SUMMARY.md` - Projekt összefoglaló
- `THEMEFOREST-SUBMISSION.md` - ThemeForest beküldési útmutató

### Kapcsolat
Ha problémába ütközöl, ellenőrizd:
1. A telepítési útmutatót
2. A hibaelhárítási szekciót
3. A PHP és MySQL error logokat

## Frissítések

### Verzió követés
- `package.json` - Függőségek és verziószám
- `CHANGELOG.md` - Változások naplója

### Backup stratégia
1. Teljes fájlrendszer biztonsági mentés
2. Adatbázis dump
3. Konfigurációs fájlok mentése
4. Képek külön mentése
