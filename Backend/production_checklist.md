# 🔒 Éles Weboldal Biztonsági Checklist

## 📋 **Kötelező biztonsági intézkedések**

### **1. Szerver szintű biztonság**
- [ ] **SSL/TLS tanúsítvány** telepítése (Let's Encrypt ingyenes)
- [ ] **Firewall** beállítása (csak szükséges portok nyitva)
- [ ] **Rendszer frissítések** telepítése
- [ ] **Backup rendszer** beállítása (napi automatikus mentés)

### **2. Adatbázis biztonság**
- [ ] **Erős jelszavak** használata (min. 12 karakter, vegyes)
- [ ] **Dedikált DB felhasználó** létrehozása (nem root)
- [ ] **Adatbázis titkosítás** engedélyezése
- [ ] **Rendszeres backup** beállítása

### **3. PHP biztonság**
- [ ] **PHP verzió** frissítése (8.1+ ajánlott)
- [ ] **Biztonsági beállítások**:
  ```php
  display_errors = Off
  log_errors = On
  expose_php = Off
  allow_url_fopen = Off
  allow_url_include = Off
  ```

### **4. Fájlrendszer biztonság**
- [ ] **Fájl jogosultságok** beállítása:
  - Mappák: 755
  - Fájlok: 644
  - Konfigurációs fájlok: 600
- [ ] **Szenzitív fájlok** elrejtése (.htaccess)
- [ ] **Upload mappa** biztonságos beállítása

### **5. Web szerver beállítások**
- [ ] **Apache/Nginx** biztonsági beállítások
- [ ] **ModSecurity** telepítése (WAF)
- [ ] **Rate limiting** beállítása
- [ ] **Gzip tömörítés** engedélyezése

### **6. Monitoring és naplózás**
- [ ] **Hibanapló** beállítása
- [ ] **Hozzáférések naplózása**
- [ ] **Rendszer monitoring** (CPU, RAM, lemez)
- [ ] **Biztonsági riasztások** beállítása

### **7. Backup stratégia**
- [ ] **Napi adatbázis backup**
- [ ] **Heti teljes backup**
- [ ] **Backup tesztelése** (helyreállítás)
- [ ] **Offsite backup** (felhő tárolás)

### **8. Frissítési stratégia**
- [ ] **Rendszeres biztonsági frissítések**
- [ ] **Alkalmazás frissítések** tesztelése
- [ ] **Rollback terv** készítése
- [ ] **Karbantartási ablakok** beállítása

## 🚨 **Sürgős biztonsági ellenőrzések**

### **Azonnal ellenőrizendő:**
1. **Gyenge jelszavak** cseréje
2. **Alapértelmezett beállítások** módosítása
3. **Felesleges szolgáltatások** kikapcsolása
4. **Hibakezelés** beállítása (ne mutassa a hibákat)
5. **SQL injection** védelem (Prepared Statements)

### **Hetente ellenőrizendő:**
- Log fájlok átnézése
- Biztonsági frissítések telepítése
- Backup működésének ellenőrzése
- Hozzáférések auditálása

## 📞 **Sürgősségi kapcsolatok**
- **Szerver szolgáltató**: [telefonszám]
- **Domain szolgáltató**: [telefonszám]
- **Biztonsági szakértő**: [telefonszám]

## 🔧 **Hasznos parancsok**

### **Backup létrehozása:**
```bash
mysqldump -u username -p himeshazi_ovoda > backup_$(date +%Y%m%d).sql
```

### **Fájl jogosultságok ellenőrzése:**
```bash
find /path/to/website -type f -exec chmod 644 {} \;
find /path/to/website -type d -exec chmod 755 {} \;
```

### **SSL tanúsítvány frissítése:**
```bash
certbot renew --dry-run
```
