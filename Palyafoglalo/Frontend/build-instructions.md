# Build és Telepítési Útmutató (MAMP)

## Production Build

Ha a `http://localhost/Palyafoglalo/Frontend/` címen szeretnéd elérni a frontend-et MAMP-ban, production build-et kell készíteni.

### 1. lépés: Dependencies telepítése

```bash
cd /Applications/MAMP/htdocs/Palyafoglalo/Frontend
npm install
```

### 2. lépés: Production build készítése

```bash
npm run build
```

Ez létrehozza a `dist/` mappát a build fájlokkal.

### 3. lépés: Build fájlok másolása

**Opció A: Automatikus (ajánlott)**

A build után másold a `dist/` mappa tartalmát a `Frontend/` mappába:

```bash
# A Frontend mappában
cp -r dist/* .
```

Vagy ha Windows-on vagy:
```bash
xcopy /E /I dist\* .
```

**Opció B: Manuális**

Másold át a `dist/` mappában lévő fájlokat (index.html, assets/) a `Frontend/` mappába.

### 4. lépés: .htaccess ellenőrzés

Biztosítsd, hogy a `.htaccess` fájl a `Frontend/` mappában legyen és helyesen működjön.

### 5. lépés: Tesztelés

Nyisd meg böngészőben: `http://localhost/Palyafoglalo/Frontend/`

---

## Development módban futtatás

Ha development módban szeretnéd futtatni (gyorsabb fejlesztéshez):

```bash
npm run dev
```

Ez elindítja a Vite dev szervert a `http://localhost:3000` címen.

**Fontos:** A dev szerveren az API hívások automatikusan proxy-zva lesznek a backend-re.

---

## Hibaelhárítás

### Fehér oldal jelenik meg

1. Ellenőrizd, hogy fut-e a backend (`http://localhost/Palyafoglalo/Bakcend/api/v1/health`)
2. Nyisd meg a böngésző Developer Tools Console-ját (F12) és nézd meg a hibákat
3. Ellenőrizd, hogy a build fájlok a helyes helyen vannak-e
4. Ellenőrizd az `.htaccess` fájlt

### API hívások nem működnek

1. Ellenőrizd a backend elérését: `http://localhost/Palyafoglalo/Bakcend/api/v1/health`
2. Nyisd meg a Network tab-ot a Developer Tools-ban és nézd meg a hívásokat
3. Ellenőrizd, hogy az `api.js` fájlban helyes-e a baseURL

---

## Gyors telepítés (egy parancs)

```bash
cd /Applications/MAMP/htdocs/Palyafoglalo/Frontend
npm install
npm run build
cp -r dist/* .
```

Ezután elérhető lesz: `http://localhost/Palyafoglalo/Frontend/`

