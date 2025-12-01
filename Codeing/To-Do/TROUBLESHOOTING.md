# 🛠️ Hibaelhárítási Útmutató

## ❌ Internal Server Error

Ha "Internal Server Error" jelenik meg, kövesse ezeket a lépéseket:

### 1. Ellenőrizze az Adatbázis Kapcsolatot

```bash
# Nyissa meg a böngészőben:
http://localhost:8888/Codeing/To-Do/test-db.php
```

Ez a script meg fogja mondani:
- ✅ Működik-e a MySQL
- ✅ Létezik-e az adatbázis
- ✅ Hozzáférhetőek-e a táblák
- ✅ Hány felhasználó van

### 2. Adatbázis Létrehozása

Ha az adatbázis nem létezik, futtassa:

**Opció A - Automatikus**:
```
http://localhost:8888/Codeing/To-Do/install.php
```

**Opció B - Manuális**:
```bash
# Terminal-ben (Mac)
/Applications/MAMP/Library/bin/mysql -u root -proot < database.sql

# Vagy ha a mysql parancs elérhető:
mysql -u root -proot < database.sql
```

### 3. MAMP Beállítások Ellenőrzése

Ellenőrizze a MAMP Settings-ben:
- **Apache Port**: 8888 (vagy amit beállított)
- **MySQL Port**: 8889 (vagy amit beállított)
- **PHP Version**: PHP 7.4 vagy újabb

### 4. config.php Beállítások

Ha a MySQL port nem 8889 (alapértelmezett MAMP):
```php
// Állítsa be a config.php-ben:
define('DB_HOST', 'localhost:8889'); // Vagy a tényleges MySQL port
```

### 5. PHP Hibák Ellenőrzése

Engedélyezze a PHP hibák megjelenítését:

1. Nyissa meg: `MAMP` → `Preferences` → `PHP`
2. Válassza: "Display all errors"
3. Mentse és indítsa újra a MAMP-et

Vagy használja a böngészőben:
```
http://localhost:8888/Codeing/To-Do/api/check-connection.php
```

### 6. Gyakori Hibák és Megoldások

#### "Connection refused"
- **Probléma**: MAMP nem fut vagy a MySQL leállt
- **Megoldás**: Indítsa újra a MAMP-et

#### "Unknown database 'company_dashboard'"
- **Probléma**: Az adatbázis nincs létrehozva
- **Megoldás**: Futtassa az `install.php`-t

#### "Access denied for user 'root'"
- **Probléma**: Helytelen MySQL felhasználónév/jelszó
- **Megoldás**: Ellenőrizze a MAMP MySQL beállításait

#### "Headers already sent"
- **Probléma**: PHP fájlban van whitespace a `<?php` előtt
- **Megoldás**: Ellenőrizze az összes PHP fájlt

### 7. Manuális Adatbázis Létrehozás

Ha a scriptek nem működnek:

```sql
-- Nyissa meg a phpMyAdmin-t:
http://localhost:8888/phpMyAdmin

-- Vagy használja a Terminal-t:
mysql -u root -proot

-- Majd futtassa:
CREATE DATABASE IF NOT EXISTS company_dashboard CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci;
USE company_dashboard;

-- Másolja ide az database.sql tartalmát
```

### 8. Permissions (Engedélyek)

Ha a fájlok nem olvashatók:

```bash
chmod 644 /Applications/MAMP/htdocs/Codeing/To-Do/*.php
chmod 755 /Applications/MAMP/htdocs/Codeing/To-Do/api/*.php
```

### 9. Cache Törlés

```bash
# Böngésző cache
Ctrl + Shift + R (Windows/Linux)
Cmd + Shift + R (Mac)

# Vagy manuálisan:
Chrome → Settings → Clear browsing data
```

### 10. Teljes Újra Telepítés

Ha semmi sem működik:

1. Állítsa le a MAMP-et
2. Törölje az adatbázist phpMyAdmin-ben
3. Futtassa újra az `install.php`-t
4. Próbálja újra

## ✅ Sikeres Kapcsolat Ellenőrzése

Ha ez az üzenet jelenik meg:
```json
{
  "success": true,
  "message": "Database connection successful",
  "users_count": 6
}
```

Akkor minden rendben van! 🎉

## 📞 További Segítség

Ha még mindig probléma van:

1. Ellenőrizze a MAMP error log-ot
2. Nézze meg a böngésző Console-t (F12)
3. Tesztelje az API-t közvetlenül
4. Ellenőrizze a fájl elérési útjakat

**Hasznos linkek:**
- MAMP Documentation: https://documentation.mamp.info/
- MySQL Documentation: https://dev.mysql.com/doc/

---

**Jó sikerrel! 💪**
