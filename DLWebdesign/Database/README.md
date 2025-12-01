# DLWebdesign - Adatbázis Dokumentáció

## Áttekintés

Ez az adatbázis egy webdesign eladással foglalkozó weboldal teljes funkcionalitását támogatja.

## Telepítés

1. Indítsd el a MAMP-ot
2. Nyisd meg a phpMyAdmin-t (http://localhost:8888/phpMyAdmin/)
3. Importáld be a `database_structure.sql` fájlt, vagy futtasd le a benne lévő SQL parancsokat

## Adatbázis Táblák Részletesen

### 1. **users** - Felhasználók
**Mire való:** Admin és vásárló fiókok tárolása

**Fontos mezők:**
- `role`: 'admin' = te (admin), 'customer' = vásárlók
- `password`: Titkosított jelszó (bcrypt)
- `status`: aktív vagy inaktív fiók

**Használat:** Bejelentkezés, rendelések követése, admin felület hozzáférés

---

### 2. **categories** - Kategóriák
**Mire való:** Webdesignok csoportosítása (pl. Landing Page, Webshop, Blog)

**Fontos mezők:**
- `slug`: URL-barát név (pl. 'landing-page')
- `display_order`: Milyen sorrendben jelenjenek meg

**Használat:** Termékek szűrése, navigáció

---

### 3. **products** - Termékek/Webdesign Csomagok
**Mire való:** Az általad kínált webdesignok tárolása

**Fontos mezők:**
- `price`: Ár forintban
- `old_price`: Akciós ár esetén az eredeti ár
- `features`: JSON formátumban a csomag jellemzői (pl. ["5 oldal", "Reszponzív", "SEO optimalizált"])
- `demo_url`: Élő demo link
- `preview_image`: Előnézeti kép
- `is_featured`: Kiemelt termék-e
- `is_bestseller`: Legkelendőbb termék-e

**Használat:** Termékek megjelenítése, vásárlás

---

### 4. **portfolio** - Portfólió
**Mire való:** Korábbi munkáid bemutatása

**Fontos mezők:**
- `client_name`: Ügyfél neve (ha megjeleníthető)
- `project_url`: Az elkészült projekt link
- `technologies`: Használt technológiák JSON-ben
- `is_featured`: Kiemelt munka-e

**Használat:** Referenciák megjelenítése, portfólió oldal

---

### 5. **orders** - Rendelések
**Mire való:** Vásárlások nyilvántartása

**Fontos mezők:**
- `order_number`: Egyedi rendelésszám (pl. ORD-2025-0001)
- `payment_status`: 'pending', 'paid', 'failed', 'refunded'
- `order_status`: 'new', 'processing', 'completed', 'cancelled'
- `total_amount`: Végösszeg

**Használat:** Rendelések kezelése, számlázás

---

### 6. **order_items** - Rendelési Tételek
**Mire való:** Egy rendelés konkrét termékei

**Kapcsolat:** Egy order-hez több order_item tartozhat

**Használat:** Részletes rendelési információk

---

### 7. **contact_messages** - Kapcsolati Üzenetek
**Mire való:** A kapcsolati űrlapon érkező üzenetek tárolása

**Fontos mezők:**
- `status`: 'new', 'read', 'replied', 'archived'
- `ip_address`: Feladó IP címe (spam védelem)

**Használat:** Ügyfélkapcsolat, megkeresések kezelése

---

### 8. **reviews** - Értékelések
**Mire való:** Vásárlói vélemények a termékekről

**Fontos mezők:**
- `rating`: 1-5 csillag
- `status`: 'pending', 'approved', 'rejected' (moderálás)

**Használat:** Termékértékelések megjelenítése, hitelesség növelése

---

### 9. **faq** - Gyakran Ismételt Kérdések
**Mire való:** Gyakori kérdések és válaszok tárolása

**Használat:** GYIK oldal, ügyfélszolgálat tehermentesítése

---

### 10. **settings** - Beállítások
**Mire való:** Globális weboldal beállítások

**Példák:**
- Weboldal név, email, telefon
- Pénznem, ÁFA
- Karbantartási mód

**Használat:** Konfiguráció adatbázisból, admin panel

---

### 11. **newsletter_subscribers** - Hírlevél Feliratkozók
**Mire való:** Email lista építése

**Fontos mezők:**
- `verified`: Email cím megerősítve-e
- `verification_token`: Megerősítő token

**Használat:** Marketing, újdonságok küldése

---

## Alapértelmezett Adatok

### Admin Felhasználó
- **Felhasználónév:** admin
- **Email:** admin@dlwebdesign.hu
- **Jelszó:** admin123

⚠️ **FONTOS:** Első bejelentkezés után változtasd meg a jelszót!

### Kategóriák
Már berakva 5 alapkategória:
1. Landing Page
2. Webshop
3. Portfólió
4. Vállalati
5. Blog

---

## Kapcsolatok (Foreign Keys)

```
categories ←─── products (category_id)
         └──── portfolio (category_id)

products ←────── order_items (product_id)
        └─────── reviews (product_id)

users ←────── orders (user_id)
     └─────── reviews (user_id)

orders ←────── order_items (order_id)
```

---

## JSON Mezők

Bizonyos mezők JSON formátumban tárolnak adatokat:

### products.features
```json
[
  "Reszponzív dizájn",
  "5 aloldal",
  "Kapcsolati űrlap",
  "SEO optimalizált",
  "1 év ingyenes támogatás"
]
```

### products.gallery_images
```json
[
  "images/product1_1.jpg",
  "images/product1_2.jpg",
  "images/product1_3.jpg"
]
```

### portfolio.technologies
```json
["HTML5", "CSS3", "JavaScript", "PHP", "MySQL"]
```

---

## Következő Lépések

1. ✅ Adatbázis struktúra elkészült
2. ⏳ PHP Model osztályok létrehozása
3. ⏳ Adatbázis kapcsolat beállítása
4. ⏳ Backend Controller-ek
5. ⏳ Frontend oldal kialakítása

Készen állsz a következő lépésre? 🚀

