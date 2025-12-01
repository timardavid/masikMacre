## Pályafoglaló – Backend (PHP)

REST API MVC felépítéssel (Model, Service, Controller), egyszerű routerrel, middleware-ekkel.

### Könyvtárstruktúra
- `config/` – konfiguráció (`config.php`)
- `core/` – keretréteg: `Database`, `Model`, `BaseController`, `Router`
- `models/` – adatmodellek: `UserModel`, `CourtModel`, `SurfaceModel`, `BookingModel`, `PricingModel`, `BookingRuleModel`, `RoleModel`
- `services/` – üzleti logika: `AuthService`, `BookingService`, `PricingService`
- `controllers/` – REST végpontok: `AuthController`, `CourtController`, `BookingController`, `PricingController`
- `middleware/` – `CorsMiddleware`, `AuthMiddleware`, `ErrorHandler`
- `index.php` – belépési pont; `.htaccess` – URL rewrite

### Környezet
- PHP 8.1+ ajánlott
- MAMP MySQL elérés: állítsd a `config/config.php`-t (alapértelmezés: `root:root`)
- Alap API útvonal: `/api/v1`

### Telepítés
1) Adatbázis létrehozás (ha még nincs): futtasd a `Database/schema.sql` és `Database/seed.sql` fájlokat.
2) Webszerver: mutasson a `Bakcend/` könyvtárra. Az `.htaccess` gondoskodik a route-olásról.
3) CORS: a `config.php`-ban állítsd a `CORS_ALLOWED_ORIGINS` listát.

### Fő végpontok (v1)
- Auth
  - `POST /api/v1/auth/login` – bejelentkezés (email, password)
  - `GET /api/v1/auth/me` – bejelentkezett felhasználó (Bearer token szükséges)
- Courts
  - `GET /api/v1/courts` – aktív pályák, nyitvatartással
  - `GET /api/v1/courts/{id}` – pálya részletek
  - `GET /api/v1/courts/{id}/availability?start=YYYY-MM-DD&end=YYYY-MM-DD` – foglalások + blackoutok
  - `GET /api/v1/surfaces` – borítások
- Bookings
  - `GET /api/v1/bookings` – foglalások listázása (auth)
  - `GET /api/v1/bookings/{id}` – foglalás (auth)
  - `POST /api/v1/bookings` – új foglalás létrehozás
  - `PUT /api/v1/bookings/{id}` – foglalás frissítés (auth)
  - `DELETE /api/v1/bookings/{id}` – foglalás lemondás (auth)
  - `GET /api/v1/bookings/availability?court_id=..&start_datetime=..&end_datetime=..` – elérhetőség ellenőrzés
  - `GET /api/v1/bookings/calculate-price?court_id=..&start_datetime=..&end_datetime=..` – árkalkuláció
- Pricing
  - `GET /api/v1/pricing/rules` – aktív árazási szabályok
  - `GET /api/v1/pricing/rules/court/{courtId}` – pálya-specifikus szabályok
  - `GET /api/v1/pricing/calculate?court_id=..&start_datetime=..&end_datetime=..` – árkalkuláció

### Auth/JWT
- Egyszerű JWT implementáció `AuthService`-ben; produkcióban javasolt könyvtár: `firebase/php-jwt`.
- Token a `Authorization: Bearer <token>` headerben.

### Megjegyzések
- A bejelentkezéshez a seed admin jelszó hash placeholder – állíts be valós hash-t.
- A foglalás ütközések ellen az adatbázis trigger-ei is védenek.


