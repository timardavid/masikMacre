# Email Beállítások - Leib Elsősegély

## Gmail SMTP Beállítás

A kapcsolat form működéséhez be kell állítani a Gmail SMTP-t.

### 1. Gmail App Password létrehozása

1. Menj a Google Account beállításokba: https://myaccount.google.com/
2. Kattints a "Biztonság" fülre
3. A "Google bejelentkezés" résznél kattints a "2-lépcsős ellenőrzés" beállításra
4. Ha nincs bekapcsolva, kapcsold be
5. Görgess le a "App passwords" részhez
6. Kattints a "App passwords" linkre
7. Válaszd ki az "Egyéb (egyéni név)" opciót
8. Írd be: "Leib Elsősegély Website"
9. Kattints a "Generate" gombra
10. Másold ki a generált 16 karakteres jelszót

### 2. EmailConfig.php frissítése

Nyisd meg a `Backend/Config/EmailConfig.php` fájlt és cseréld le a `SMTP_PASSWORD` értékét:

```php
const SMTP_PASSWORD = 'your_16_character_app_password_here';
```

### 3. Tesztelés

1. Indítsd el a MAMP szervert
2. Menj a weboldalra
3. Töltsd ki a kapcsolat formot
4. Ellenőrizd, hogy megérkezett-e az email a `timar.david1974@gmail.com` címre

### Hibaelhárítás

- Ha nem működik, ellenőrizd a PHP error log-ot
- Győződj meg róla, hogy a Gmail fiókodban engedélyezve van a "kevésbé biztonságos alkalmazások hozzáférése"
- Ellenőrizd, hogy a 2-lépcsős ellenőrzés be van-e kapcsolva

### Biztonsági megjegyzések

- Soha ne commitold a valódi jelszót a Git-be
- Használj környezeti változókat éles környezetben
- Rendszeresen cseréld le az App Password-t
