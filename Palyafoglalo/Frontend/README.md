## Pályafoglaló – Frontend (React)

Modern React alkalmazás Vite build tool-lal, Tailwind CSS-sel, React Router-rel.

### Technológiai stack
- React 18.2 (funkcionális komponensek, hooks)
- Vite (gyors build és dev server)
- React Router 6 (routing)
- Tailwind CSS 3 (styling)
- Axios (HTTP kliens)
- date-fns (dátum formázás)
- lucide-react (ikonok)

### Struktúra
```
Frontend/
├── src/
│   ├── components/     # Újrafelhasználható komponensek
│   │   ├── Layout/    # Header, Footer, Layout wrapper
│   │   ├── CourtCard/  # Pálya kártya komponens
│   │   └── BookingForm/ # Foglalási űrlap
│   ├── pages/         # Oldal komponensek
│   │   ├── HomePage.jsx
│   │   ├── CourtsPage.jsx
│   │   ├── CourtDetailPage.jsx
│   │   ├── BookingPage.jsx
│   │   ├── LoginPage.jsx
│   │   └── MyBookingsPage.jsx
│   ├── hooks/         # Custom React hooks
│   │   ├── useCourts.js
│   │   └── useBookings.js
│   ├── services/      # API kommunikáció
│   │   └── api.js
│   ├── context/       # Context API providers
│   │   └── AuthContext.jsx
│   ├── config/        # Konfiguráció
│   │   └── api.js
│   ├── utils/         # Segédfüggvények
│   │   └── format.js
│   ├── App.jsx        # Fő alkalmazás komponens
│   ├── main.jsx       # Entry point
│   └── index.css      # Globális stílusok
├── package.json
├── vite.config.js
└── tailwind.config.js
```

### Telepítés és futtatás (MAMP - Port 80)

#### Production Build (Ajánlott MAMP-hoz)

Mivel a `http://localhost/Palyafoglalo/Frontend/` útvonalon szeretnéd elérni, production build-et kell készíteni:

1. **Dependency-k telepítése:**
```bash
cd /Applications/MAMP/htdocs/Palyafoglalo/Frontend
npm install
```

2. **Production build készítése:**
```bash
npm run build
```

3. **Build fájlok másolása a root-ba:**
```bash
cp -r dist/* .
```

Vagy használd a build scriptet:
```bash
chmod +x build.sh
./build.sh
```

4. **Elérés:**
Nyisd meg böngészőben: `http://localhost/Palyafoglalo/Frontend/`

#### Development módban futtatás

Ha gyors fejlesztéshez szeretnéd használni:
```bash
npm run dev
```
Ez elindítja a Vite dev szervert a `http://localhost:3000` címen.

### Főbb funkciók

- **Pályák listázása:** `/courts` oldal az összes elérhető pályával
- **Pálya részletek:** `/courts/:id` oldal egy konkrét pálya információival
- **Foglalás létrehozása:** `/book/:courtId` oldal foglalási űrlappal
- **Bejelentkezés:** `/login` oldal felhasználói autentikációval
- **Foglalásaim:** `/my-bookings` oldal a felhasználó foglalásaival (authentication szükséges)

### API integráció

A frontend a backend `/api/v1` endpoint-jaira hív, amit a Vite proxy konfiguráció kezel (lásd `vite.config.js`).

### Styling

Tailwind CSS utility class-okkal. Custom komponensek az `index.css`-ben definiálva (`.btn`, `.card`, `.input`, stb.).

### State management

- **Context API:** Authentication state (`AuthContext`)
- **Custom Hooks:** Adat lekérés és állapotkezelés (`useCourts`, `useBookings`)

### További fejlesztési lehetőségek

- Komponens library bővítése
- Toast notification rendszer
- Loading skeleton komponensek
- Form validation library (pl. react-hook-form)
- Tesztek (Jest + React Testing Library)

