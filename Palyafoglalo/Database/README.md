## Pályafoglaló – Adatbázis

Ez a könyvtár tartalmazza a MySQL 8.0+ sémát és alapszintű seed adatokat egy tenisz pályafoglaló rendszerhez.

### Fő célok
- **Foglalások ütközésének megelőzése**: trigger-ek akadályozzák a pályánkénti időátfedést és a blokk időszakokkal való ütközést.
- **Tiszta normalizált séma**: külön táblák a felhasználókhoz, pályákhoz, nyitvatartáshoz, kizárásokhoz.
- **Indexek és integritás**: idegen kulcsok, ellenőrző feltételek, összetett indexek.

### Fájlok
- `schema.sql`: teljes adatbázis séma, táblák, indexek, nézetek, függvény, triggerek.
- `seed.sql`: kezdeti adatok (szerepkörök, borítások, pályák, nyitvatartás, példa blackout).

### Telepítés
1. MySQL 8.0+ szükséges (InnoDB, utf8mb4).
2. Futtasd sorrendben:
   - `schema.sql`
   - `seed.sql`

CLI példa (MAMP esetén cseréld a felhasználót/jelszót):
```bash
mysql -u root -p < Database/schema.sql
mysql -u root -p < Database/seed.sql
```

### Entitások röviden
- `roles`: admin/staff/customer.
- `users`: rendszer felhasználók (BCrypt hash tárolása `password_hash` mezőben).
- `surfaces`: pályaborítások (pl. Clay/Hard/Grass/Carpet).
- `courts`: pályák, beltéri/kültéri, világítás.
- `court_opening_hours`: heti nyitvatartás pályánként (0=Vasárnap..6=Szombat).
- `blackout_intervals`: karbantartás/ünnep/időszakos zárás (globális vagy pályaspecifikus).
- `pricing_rules`: dinamikus árazás szabályok (időszak, pálya, hétköznap/hétvége, prioritás alapján).
- `booking_rules`: rendszer szintű foglalási szabályok (min/max időtartam, előre foglalhatósági limit, stb.).
- `bookings`: foglalások (időintervallum, státusz, ár-információ, fizetési státusz).
- `booking_notes`: belső megjegyzések foglaláshoz.
- `payment_transactions`: részletes fizetési tranzakciók (fizetés/visszatérítés, fizetési mód, külső tranzakció ID, státusz).
- `audit_log`: változás-követés (ki, mikor, mit változtatott) JSON formátumban.
- `notifications`: értesítési log (email/SMS, státusz, hibaüzenetek).

### Ütközéskezelés
- `trg_bookings_before_insert` és `trg_bookings_before_update` triggerek:
  - Megakadályozzák az időátfedést ugyazon pályán nem‑cancelled foglalásokkal.
  - Megakadályozzák az ütközést blackout időszakokkal (globális és pályaszintű).
- `fn_is_court_available(court_id, start, end)`: 1 ha szabad, 0 ha nem – kliens oldali előellenőrzéshez hasznos.

### Konvenciók
- Időpontok UTC-ben tárolva javasoltak; alkalmazás szinten kezeljük a zónát.
- Pénz `price_cents` egészként, `currency` 3 betűs ISO.
- `status`: `pending|confirmed|cancelled|completed|no_show`; ütközést csak a nem‑cancelled foglalások képeznek.

### Új funkciók (v2.0)

#### Dinamikus árazás rendszer (`pricing_rules`)
- Időszak-alapú árazás (pl. csúcsidő hétvégén magasabb ár).
- Pálya/beltéri/kültéri/borítás specifikus szabályok.
- Prioritás-alapú szabály alkalmazás (magasabb prioritás = specifikusabb szabály).
- Érvényességi időszak (`valid_from`, `valid_until`).

#### Foglalási szabályok (`booking_rules`)
- Konfigurálható limitációk (min/max időtartam, előre foglalhatósági korlát).
- Automatikus megerősítés be/ki kapcsolható.
- Lemondási feltételek és visszatérítési szabályok.

#### Fizetési tranzakciók (`payment_transactions`)
- Részletes fizetési előzmények (fizetés, visszatérítés, részleges visszatérítés).
- Több fizetési mód támogatás (készpénz, kártya, átutalás, online, voucher).
- Külső fizetési provider integráció (pl. Stripe, PayPal) - `external_transaction_id` mezővel.

#### Audit log (`audit_log`)
- Automatikus változás-követés (INSERT, UPDATE, DELETE műveletek).
- JSON formátumban tárolt régi/új értékek.
- IP cím és user agent követés biztonsági célokra.

#### Értesítések (`notifications`)
- Email/SMS/Push értesítések log-olása.
- Státusz követés (pending, sent, failed, bounced).
- Hibaüzenetek tárolása az újrapróbáláshoz.

### További bővíthetőség
- Visszatérő foglalások (recurring bookings) külön táblával.
- Csoportos események és tornák.
- Ügyfél értékelések és visszajelzések.
- Bérlet rendszer és előfizetések.
- Dinamikus kedvezmények és kupongok.

### Megjegyzések
- A `CHECK` constraint-ek MySQL 8.0‑ban támogatottak; korábbi verziók figyelmen kívül hagyhatják.
- A seed admin jelszó hash helykitöltő – a backend fogja beállítani biztonságosan.


