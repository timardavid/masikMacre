# Email beállítás - GYORS ÚTMUTATÓ

## Probléma
Az email nem megy, mert a Gmail App Password rossz vagy nincs beállítva.

## Megoldás - 2 lehetőség:

### 1. LEHETSÉGES: .env fájl használata (ajánlott)

1. Másold ki a `.env.example` fájlt `.env` néven:
   ```bash
   cd Backend
   cp .env.example .env
   ```

2. Szerkeszd a `.env` fájlt és töltsd ki:
   ```env
   SMTP_HOST=smtp.gmail.com
   SMTP_PORT=587
   SMTP_ENCRYPTION=tls
   SMTP_USERNAME=timar.david1974@gmail.com
   SMTP_PASSWORD=IDE_KELL_AZ_ÚJ_APP_PASSWORD
   EMAIL_FROM=timar.david1974@gmail.com
   EMAIL_FROM_NAME=Himesházi Óvoda Website
   EMAIL_TO=leibbea81@gmail.com
   ```

3. **Az új Gmail App Password létrehozása:**
   - Menj: https://myaccount.google.com
   - Biztonság → Kétfaktoros hitelesítés (ha nincs, kapcsold be)
   - App jelszavak → Új app jelszó létrehozása
   - Nevezd el: "Himesházi Óvoda Website"
   - Másold ki a 16 karakteres jelszót (pl: `abcd efgh ijkl mnop`)
   - **Szóközök nélkül** írd be a `.env` fájlba: `SMTP_PASSWORD=abcdefghijklmnop`

### 2. LEHETSÉGES: Közvetlen beállítás a kódban (csak fejlesztéshez!)

Ha nem akarsz .env fájlt használni, szerkeszd a `Backend/Config/EmailConfig.php` fájlt:

1. Nyisd meg: `Backend/Config/EmailConfig.php`
2. Menj a 26. sorhoz
3. Cseréld le az üres jelszó mezőt:
   ```php
   'smtp_password' => $_ENV['SMTP_PASSWORD'] ?? $_SERVER['SMTP_PASSWORD'] ?? ($isProduction ? '' : 'IDE_KELL_AZ_ÚJ_APP_PASSWORD'),
   ```

## Gmail App Password létrehozása - RÉSZLETESEN

1. **Bejelentkezés:** https://myaccount.google.com
2. **Bal oldali menü → Biztonság**
3. **Kétfaktoros hitelesítés** - Ha nincs bekapcsolva:
   - Kapcsold be
   - Add meg a telefonszámod
   - Erősítsd meg
4. **App jelszavak:**
   - Görgess lejjebb a Biztonság oldalon
   - Keress rá: **"App jelszavak"** vagy **"Alkalmazás jelszavak"**
   - Kattints: **"App jelszavak kezelése"**
5. **Új App Password létrehozása:**
   - Válassz: **"Más (Egyedi név)"** / **"Other (Custom name)"**
   - Add meg: `Himesházi Óvoda Website`
   - Kattints: **Generálás** / **Generate**
   - **Másold ki a 16 karakteres jelszót!** (pl: `abcd efgh ijkl mnop`)
6. **Használat:**
   - A jelszó **szóközök nélkül** kell! (`abcdefghijklmnop`)

## Tesztelés

1. Küldj egy teszt emailt az oldalon keresztül
2. Ellenőrizd a `logs/php_errors.log` fájlt
3. Ha sikeres, ezt látod: `Email sikeresen elküldve: ...`

## Hibaelhárítás

### "Could not authenticate" hiba

1. ✅ Ellenőrizd, hogy a kétfaktoros hitelesítés be van-e kapcsolva
2. ✅ Használj App Password-t, ne a sima Gmail jelszót
3. ✅ A jelszó szóközök nélkül legyen
4. ✅ Új App Password-t hozz létre, ha a régi nem működik

### További segítség

Ha továbbra sem működik, nézd meg a `logs/php_errors.log` fájlt a részletes hibáért.

