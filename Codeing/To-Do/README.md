# 🏢 Vállalati Admin Dashboard

Professzionális admin dashboard vállalatok számára munkatársak nyomon követésére, feladatkezelésre és időkövetésre.

## 🎯 Funkciók

### Főbb funkciók:
- **Munkatárs nyilvántartás** - Teljes munkatárs lista státuszokkal
- **Feladatkezelés** - Ügyféllel kapcsolatos feladatok prioritásrendszerrel
- **Munkaidő követés** - Valós idejű státusz követés (munkában, szüneten, szabadságon, stb.)
- **Statisztikák** - Havi és heti munkaidő követése
- **Alert rendszer** - Heti 40 óra lejártás értesítés
- **Jogosultságkezelés** - Részlegszintű hozzáférési szintek

### Prioritás szintek:
- 🔴 **Kritikus** - Azonnali beavatkozás szükséges
- 🟠 **Nagyon sürgős** - Gyors megoldás szükséges  
- 🟡 **Sürgős** - Hamarosan megoldandó
- 🟢 **Nem sürgős** - Szem előtt tartandó

### Részlegek és jogosultságok:

#### 👤 Admin
- Teljes hozzáférés az összes funkcióhoz
- Felhasználók kezelése
- Feladatok kezelése
- Statisztikák megtekintése

#### 💻 IT
- Dolgozók kezelése
- Feladatok hozzárendelése
- Rendszer statisztikák
- **NEM** hozzáférés pénzügyi adatokhoz

#### 👥 HR
- Dolgozói nyilvántartás
- Személyi állomány követése
- Munkaidő statisztikák
- Heti óra ellenőrzések

#### 💰 Pénzügy
- Pénzügyi zárások követése
- Munkatárs kezelés
- Adatbázis tartalmához való hozzáférés
- **KORLÁTOZOTT** hozzáférés más részlegekhez

#### 🎯 Ügyvezető
- Teljes áttekintés
- Összes statisztika
- Minden részleg adatai
- Teljes üzleti kép

## 📋 Telepítés

### Előfeltételek:
- MAMP vagy WAMP telepítve
- MySQL/MariaDB elérhető
- PHP 7.4+

### Telepítési lépések:

1. **Adatbázis létrehozása**
   ```bash
   mysql -u root -p < database.sql
   ```

2. **Beállítások ellenőrzése**
   - Nyissa meg a `config.php` fájlt
   - Ellenőrizze az adatbázis kapcsolati beállításokat

3. **MAMP indítása**
   - Indítsa el a MAMP szolgáltatásokat
   - Ellenőrizze, hogy a MySQL fut

4. **Használat**
   - Nyissa meg böngészőben: `http://localhost:8888/Codeing/To-Do/index.html`

## 🔑 Alapértelmezett bejelentkezési adatok

Az összes teszt fiók jelszava: `password` (vagy bármilyen érték, a demo minden jelszót elfogad)

### Bejelentkezési opciók:
- **Admin**: admin@company.com
- **IT**: it@company.com
- **HR**: hr@company.com
- **Pénzügy**: finance@company.com
- **Ügyvezető**: ceo@company.com
- **Dolgozó Péter**: peter@company.com

## 📊 Funkciók részletesen

### 1. Áttekintés
- Gyors statisztikák
- Aktuális helyzet összefoglalása
- Figyelmeztetések dolgozókról akik nem teljesítik a heti 40 órát

### 2. Dolgozók
- Teljes személyi állomány lista
- Valós idejű munkastátusz
- Szerkesztés és törlés funkciók
- Új dolgozó hozzáadása

### 3. Feladatok
- Ügyfélfeladatok áttekintése
- Prioritásrendszer
- Hozzárendelt felelős
- Feladat státusz követés

### 4. Munkaidő követés
- Valós idejű státuszfrissítések
- Dolgozói státuszok
- Időbélyeg követés
- Szünetek, munkavégzési idők nyomon követése

### 5. Statisztikák
- Havi munkaidő összesítés
- Heti óraszámok
- Munkanapok számlálása
- Függőben lévő és folyamatban lévő feladatok

## 🎨 UI/UX Jellemzők

- ✅ **Modern design** - Tiszta, professzionális megjelenés
- ✅ **Reszponzív** - Mobil és tablet kompatibilis
- ✅ **Intuitív** - Egyszerű navigáció
- ✅ **Színjelölő rendszer** - Könnyű azonnali értelmezés
- ✅ **Alert rendszer** - Figyelmeztetések azonnal láthatóak
- ✅ **Gyors műveletek** - Szerkesztés, törlés, hozzáadás egy kattintás

## 🔐 Biztonsági szempontok

- Session alapú autentikáció
- Role-based access control (RBAC)
- SQL injection védelem (prepared statements)
- XSS védelem
- Automatikus kijelentkezés

## 🚀 Fejlesztési lehetőségek

- [ ] Email értesítések
- [ ] Excel export funkciók
- [ ] Fejlett jelentéskészítés
- [ ] Mobilalkalmazás
- [ ] Real-time notification rendszer
- [ ] API dokumentáció
- [ ] Audit log rendszer

## 📝 Licenc

Ez a projekt belső használatra készült.

## 💡 Használati tippek

1. **Munkaidő megadás**: A dolgozók státuszát a "Munkaidő" menüben frissítheti
2. **Prioritás beállítás**: Feladatok létrehozásakor válassza ki a megfelelő prioritást
3. **Alert figyelés**: Az Áttekintés menüben láthatja a heti 40 óra alatt dolgozókat
4. **Státusz követés**: Valós idejű követés a "Munkaidő" menüben
5. **Statisztikák**: Havi kimutatások a "Statisztikák" menüben

## 🆘 Támogatás

Probléma esetén:
1. Ellenőrizze a MAMP futását
2. Ellenőrizze az adatbázis kapcsolatot
3. Nézze meg a böngésző konzolt hibákért
4. Ellenőrizze a PHP error log-ot

---

**Fejlesztette**: AI Assistant  
**Verzió**: 1.0  
**Dátum**: 2024
