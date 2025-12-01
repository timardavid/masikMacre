# 📖 Használati Útmutató

## 🚀 Gyors Start

### 1. Telepítés (Válasszon egyet):

**Opció A - Automatikus telepítés**:
```
Nyissa meg: http://localhost:8888/Codeing/To-Do/install.php
```

**Opció B - Manuális telepítés**:
```bash
mysql -u root -p < database.sql
```

### 2. Bejelentkezés

1. Nyissa meg: `http://localhost:8888/Codeing/To-Do/index.html`
2. Válassza ki a szerepkört (Admin, IT, HR, stb.)
3. Írja be a bejelentkezési adatokat

### 3. Használat

---

## 📋 Alapértelmezett bejelentkezési adatok

Minden fiók jelszava: `password` (vagy bármi - demo módban)

| Szerepkör | Email | Funkció |
|-----------|-------|---------|
| Admin | admin@company.com | Teljes hozzáférés |
| IT | it@company.com | Felhasználók és rendszerek |
| HR | hr@company.com | Emberi erőforrások |
| Pénzügy | finance@company.com | Pénzügyi zárások |
| Ügyvezető | ceo@company.com | Teljes áttekintés |

---

## 🎯 Főbb Funkciók

### 👥 Dolgozók Kezelése

**Előnyök**:
- Teljes személyzet lista
- Valós idejű munkastátusz
- Szerkesztés/Törlés opciók

**Használat**:
1. Menj a "Dolgozók" menübe
2. "Új dolgozó" gomb
3. Töltse ki az űrlapot
4. Mentse el

**Státuszok**:
- 🟢 **Munkában** - Aktívan dolgozik
- 🟡 **Szünet** - Éppen szünetben van
- 🔵 **Szabadság** - Szabadságon van
- 🔴 **Táppénz** - Betegszabadságon
- ⚪ **Nincs munkaidő** - Nem dolgozik

---

### 📝 Feladatkezelés

**Prioritás szintek**:
1. 🔴 **Kritikus** - Azonnali beavatkozás
2. 🟠 **Nagyon sürgős** - Gyors megoldás kell
3. 🟡 **Sürgős** - Hamarosan megoldandó
4. 🟢 **Nem sürgős** - Szem előtt tartandó

**Használat**:
1. Menj a "Feladatok" menübe
2. "Új feladat" gomb
3. Adja meg:
   - Feladat címe
   - Ügyfél neve
   - Leírás
   - Felelős
   - Prioritás
   - Státusz
4. Mentse el

---

### ⏰ Munkaidő Követés

**Használat**:
1. Menj a "Munkaidő" menübe
2. Kattintson a "Státusz frissítés" gombra
3. Válasszon státuszt:
   - 1 = Munkában
   - 2 = Szünet
   - 3 = Szabadság
   - 4 = Táppénz

**Automatikus követés**:
- A rendszer automatikusan rögzíti az időt
- Előzmények láthatóak
- Szűrés dátum szerint

---

### 📊 Statisztikák

**Havi kimutatás**:
- Összes ledolgozott óra
- Munkanapok száma
- Függőben lévő feladatok
- Folyamatban lévő feladatok

**Heti ellenőrzés**:
- Heti óraszámok
- Piros alert ha < 40 óra
- Vörös jelzés a gráfokban

**Használat**:
1. Menj a "Statisztikák" menübe
2. Válasszon dolgozót
3. Tekintse meg a havi/heti adatokat

---

## ⚠️ Alert Rendszer

### Heti 40 óra Ellenőrzés

Ha egy dolgozó nem dolgozta le a heti 40 órát:
- 🔴 **Piros jelzés** az Áttekintés oldalon
- ⚠️ **Alert box** a dolgozó nevével
- 📊 **Piros bar** a Statisztikák grafikájában
- 📧 **Figyelmeztetés szövege**: "Óralejártás!"

**Mit lehet tenni**:
1. Ellenőrizze a "Statisztikák" menüben
2. Nézze meg pontos adatokat
3. Lépjen kapcsolatba a dolgozóval
4. Frissítse a munkatervezést

---

## 👤 Szerepkörök és Jogosultságok

### Admin (👤)
✅ Minden funkcionalitás  
✅ Felhasználók kezelése  
✅ Feladatok kezelése  
✅ Statisztikák  
✅ Figyelmeztetések  

### IT (💻)
✅ Dolgozók kezelése  
✅ Feladatok hozzárendelése  
✅ Rendszer statisztikák  
❌ Pénzügyi adatok (korlátozva)  

### HR (👥)
✅ Dolgozói nyilvántartás  
✅ Személyi állomány követése  
✅ Munkaidő statisztikák  
✅ Heti óra ellenőrzések  
✅ Alert rendszer  

### Pénzügy (💰)
✅ Pénzügyi zárások  
✅ Munkatárs kezelés  
⚠️ Korlátozott hozzáférés más részlegekhez  

### Ügyvezető (🎯)
✅ Teljes áttekintés  
✅ Összes statisztika  
✅ Minden részleg adatai  
✅ Teljes üzleti kép  

---

## 💡 Pro Tippek

### 1. Hatékony Munkaidő Követés
- Frissítse rendszeresen a státuszt
- Használja a szüneteket "Szünet" státusszal
- Jegyezze fel a különleges eseteket (notes mező)

### 2. Prioritás Rendszer
- Használja konzisztensen a prioritásokat
- Kritikus csak tényleg kritikus esetekre
- Átnézze hetente a nem sürgős feladatokat

### 3. Alert Rendszer Kihasználása
- Nézze meg minden reggel az Áttekintést
- Figyelje a piros figyelmeztetéseket
- Lépjen kapcsolatba a lejárt órás dolgozókkal

### 4. Statisztikák Elemezése
- Heti átnézés minden hétfőn
- Havi zárás év végén
- Trend követés hosszú távon

### 5. Tiszta Névadás
- Könnyen azonosítható feladatcímek
- Ügyfél neve mindig megadva
- Részletes leírások

---

## 🆘 Gyakori Problémák

### "Nem tudok bejelentkezni"
- Ellenőrizze: MAMP fut-e?
- Ellenőrizze: MySQL fut-e?
- Próbálja újra a jelszót

### "Nincs adat"
- Futtassa az install.php-t
- Ellenőrizze az adatbázist

### "CSS nem tölt be"
- Ellenőrizze a fájl struktúrát
- Törölje a cache-t (Ctrl+F5)

### "API hiba"
- Ellenőrizze a config.php-t
- Ellenőrizze az adatbázis kapcsolatot
- Nézze meg a PHP error log-ot

---

## 📞 További Segítség

Dokumentáció és kód:
- README.md - Általános információk
- index.html - Bejelentkezési oldal
- dashboard.html - Fő oldal
- api/ - Backend API-k

Visszajelzés és fejlesztési ötletek:
- email@example.com

---

**Jó használatot! 🎉**
