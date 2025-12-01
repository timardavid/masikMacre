# 📦 Adatbázis Telepítési Útmutató

## Lépésről lépésre

### 1. MAMP Indítása
- Indítsd el a MAMP alkalmazást
- Kattints a **Start Servers** gombra
- Várj, amíg mindkét szerver (Apache és MySQL) zöld lesz

### 2. phpMyAdmin Megnyitása
- Nyisd meg a böngészőt
- Menj a következő címre: **http://localhost:8888/phpMyAdmin/**
- (Windows esetén lehet: http://localhost/phpMyAdmin/)

### 3. Adatbázis Létrehozása

#### 3.1. Manuális importálás (Ajánlott)
1. phpMyAdmin-ban kattints az **Import** fülre (felül)
2. Kattints a **Choose File** gombra
3. Válaszd ki a `database_structure.sql` fájlt
4. Kattints a **Go** gombra (lent)
5. Várj, amíg az importálás befejeződik

#### 3.2. Manuális futtatás
1. phpMyAdmin-ban kattints a **SQL** fülre (felül)
2. Nyisd meg a `database_structure.sql` fájlt egy szövegszerkesztővel
3. Másold ki a teljes tartalmat
4. Illeszd be az SQL mezőbe
5. Kattints a **Go** gombra

### 4. Konfiguráció Ellenőrzése

Nyisd meg a `config.php` fájlt és ellenőrizd:

```php
define('DB_HOST', 'localhost');
define('DB_PORT', '8889'); // Mac: 8889, Windows: 3306
define('DB_NAME', 'jdw_db');
define('DB_USER', 'root');
define('DB_PASS', 'root');
```

**Windows MAMP esetén** változtasd meg:
```php
define('DB_PORT', '3306');
```

### 5. Kapcsolat Tesztelése

1. Nyisd meg a böngészőt
2. Menj a címre: **http://localhost:8888/JDW/Database/test_connection.php**
3. Ha minden rendben, zöld sikerüzenetet látsz
4. Láthatod az összes táblát és az alapértelmezett adatokat

### 6. Bejelentkezési Adatok

**Admin felhasználó:**
- Felhasználónév: `admin`
- Email: `admin@dlwebdesign.hu`
- Jelszó: `admin123`

⚠️ **FONTOS:** Első bejelentkezés után azonnal változtasd meg a jelszót!

## 🎯 Mit kaptál?

### 11 adatbázis tábla:
1. ✅ **users** - Felhasználók (admin, vásárlók)
2. ✅ **categories** - Kategóriák (5 db már létrehozva)
3. ✅ **products** - Webdesign termékek
4. ✅ **portfolio** - Portfólió munkák
5. ✅ **orders** - Rendelések
6. ✅ **order_items** - Rendelési tételek
7. ✅ **contact_messages** - Kapcsolati üzenetek
8. ✅ **reviews** - Értékelések
9. ✅ **faq** - Gyakran Ismételt Kérdések
10. ✅ **settings** - Weboldal beállítások
11. ✅ **newsletter_subscribers** - Hírlevél feliratkozók

### Előre telepített adatok:
- ✅ 1 admin felhasználó
- ✅ 5 kategória (Landing Page, Webshop, Portfólió, Vállalati, Blog)
- ✅ 6 alapvető beállítás

## 🔍 Hibaelhárítás

### "Connection refused" vagy "Cannot connect"
**Probléma:** Nem tud csatlakozni az adatbázishoz

**Megoldás:**
1. Ellenőrizd, hogy a MAMP fut-e
2. Nézd meg a MySQL port számát a MAMP beállításokban
3. Állítsd be a megfelelő portot a `config.php`-ban

### "Access denied for user"
**Probléma:** Rossz felhasználónév vagy jelszó

**Megoldás:**
1. Alapértelmezett MAMP beállítások: user = `root`, pass = `root`
2. Ellenőrizd a `config.php` fájlban

### "Unknown database 'jdw_db'"
**Probléma:** Az adatbázis még nem létezik

**Megoldás:**
1. Importáld be a `database_structure.sql` fájlt
2. Vagy futtasd le az SQL parancsokat manuálisan

### "Table doesn't exist"
**Probléma:** A táblák nincsenek létrehozva

**Megoldás:**
1. Futtasd újra az importálást
2. Ellenőrizd, hogy az importálás hibamentesen lefutott-e

## 📞 Port Beállítások

### macOS MAMP:
- Apache Port: `8888`
- MySQL Port: `8889`

### Windows MAMP:
- Apache Port: `80` vagy `8888`
- MySQL Port: `3306`

## ✅ Ellenőrző Lista

- [ ] MAMP fut
- [ ] MySQL szerver (zöld)
- [ ] phpMyAdmin elérhető
- [ ] `database_structure.sql` importálva
- [ ] `config.php` beállítások jók
- [ ] `test_connection.php` futtatva
- [ ] Zöld sikerüzenet kapva
- [ ] 11 tábla létezik
- [ ] Admin felhasználó létezik

## 🚀 Következő Lépések

Ha minden tábla létrejött és a kapcsolat működik:

1. **Backend fejlesztés**: PHP Model és Controller osztályok
2. **API végpontok**: REST API a frontend számára
3. **Admin felület**: Termékek, rendelések kezelése
4. **Frontend**: HTML/CSS/JS felhasználói felület

Készen állsz? Mehetünk tovább! 💪

