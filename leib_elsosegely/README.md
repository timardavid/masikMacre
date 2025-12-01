# Leib Elsősegély Felszerelések Weboldal

Ez a projekt a [Leib Elsősegély WordPress oldal](https://leibelsosegely.wordpress.com/) átvitele egy professzionális, kódolt weboldalra.

## Projekt Struktúra

```
leib_elsosegely/
├── Backend/
│   ├── Config/
│   │   └── Database.php          # Adatbázis kapcsolat
│   ├── Controller/
│   │   └── HomeController.php    # Főoldal kontroller
│   ├── Model/
│   │   ├── PageModel.php         # Oldal modell
│   │   ├── ProductModel.php      # Termék modell
│   │   ├── ServiceModel.php      # Szolgáltatás modell
│   │   └── ContactModel.php      # Kapcsolat modell
│   └── api.php                   # API endpoint
├── Database/
│   └── schema.sql                # Adatbázis séma
├── Frontend/
│   ├── assets/
│   │   ├── css/
│   │   │   └── style.css         # Fő CSS fájl
│   │   ├── js/
│   │   │   └── main.js           # Fő JavaScript fájl
│   │   └── images/               # Képek mappa
│   └── index.html                # Főoldal
└── README.md
```

## Funkciók

### Frontend
- **Reszponzív design** - Mobil és desktop kompatibilis
- **Modern UI/UX** - Elsősegély témájú színsémával
- **Interaktív navigáció** - Smooth scrolling és aktív linkek
- **Termék szűrés** - Kategóriák szerinti szűrés
- **Kapcsolati űrlap** - Üzenet küldési lehetőség
- **Back to top gomb** - Könnyű navigáció

### Backend
- **RESTful API** - PHP alapú API
- **MVC architektúra** - Tiszta kód struktúra
- **Adatbázis integráció** - MySQL/MariaDB támogatás
- **Biztonság** - PDO prepared statements

### Adatbázis
- **Oldalak tábla** - Tartalom kezelés
- **Termékek tábla** - Elsősegély felszerelések
- **Szolgáltatások tábla** - Vállalkozás szolgáltatásai
- **Kapcsolat tábla** - Cég információk

## Telepítés

### Előfeltételek
- MAMP/XAMPP vagy hasonló lokális szerver
- PHP 7.4+
- MySQL/MariaDB
- Webböngésző

### Lépések

1. **Adatbázis létrehozása**
   ```sql
   mysql -u root -p < Database/schema.sql
   ```

2. **Adatbázis konfiguráció**
   - Szerkessze a `Backend/Config/Database.php` fájlt
   - Állítsa be a helyes adatbázis adatokat

3. **Webszerver indítása**
   - Indítsa el a MAMP/XAMPP szervert
   - Navigáljon a `Frontend/index.html` fájlhoz

## Használat

### Főoldal
- Bemutatkozás szöveg
- Szolgáltatások listázása
- Termékek megjelenítése
- Kapcsolati információk

### Navigáció
- **Főoldal** - Bemutatkozás és áttekintés
- **Bemutatkozás** - Részletes vállalkozás leírás
- **Szolgáltatások** - Térítésmentes szolgáltatások
- **Eszközök** - Elsősegély felszerelések
- **Kapcsolat** - Elérhetőségek és üzenet küldés

### Termékek
- Kategóriák szerinti szűrés
- Részletes termék leírások
- Képek megjelenítése
- Kategória címkék

## Színséma

Az oldal elsősegély témájú színsémát használ:
- **Főszín**: Vörös (#dc2626) - elsősegély szimbólum
- **Másodlagos**: Sötétszürke (#1f2937) - professzionális megjelenés
- **Kiemelés**: Narancssárga (#f59e0b) - figyelemfelhívás
- **Siker**: Zöld (#10b981) - biztonság
- **Háttér**: Világosszürke (#f8fafc) - tisztaság

## Technológiai stack

- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Backend**: PHP 7.4+
- **Adatbázis**: MySQL/MariaDB
- **Ikonok**: Font Awesome 6
- **Design**: CSS Grid, Flexbox, CSS Variables

## Fejlesztői információk

### API Endpoints
- `GET /api/home` - Főoldal adatok
- `GET /api/products` - Termékek listája
- `GET /api/services` - Szolgáltatások listája
- `GET /api/page/{slug}` - Oldal tartalom slug alapján

### CSS Architektúra
- CSS Variables használata
- Mobile-first responsive design
- BEM metodológia elemek
- Utility osztályok

### JavaScript
- ES6+ szintaxis
- Async/await API hívások
- Event delegation
- Error handling

## Következő lépések

- [ ] Admin felület fejlesztése
- [ ] SEO optimalizálás
- [ ] Performance optimalizálás
- [ ] Tesztelés különböző böngészőkben
- [ ] HTTPS támogatás
- [ ] Caching implementálás

## Licenc

Ez a projekt a Leib Elsősegély Felszerelések Forgalmazása tulajdona.

## Kapcsolat

**Leib Roland**  
Leib Elsősegély Felszerelések Forgalmazása

---

*Fejlesztve 2024-ben - Professzionális elsősegély felszerelések és oktatás*
