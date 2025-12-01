# 🚀 Himesházi Óvoda - Production Deployment Guide

## 📋 Előfeltételek

### Szerver követelmények:
- **PHP**: 7.4+ (ajánlott: 8.1+)
- **MySQL**: 5.7+ (ajánlott: 8.0+)
- **Apache/Nginx**: Legfrissebb verzió
- **SSL tanúsítvány**: Let's Encrypt vagy kereskedelmi
- **Disk terület**: Minimum 1GB

### PHP bővítmények:
```bash
php -m | grep -E "(pdo|mysql|json|mbstring|openssl|curl|gd|zip)"
```

## 🔧 Telepítési lépések

### 1. Fájlok feltöltése
```bash
# Tömörítés
tar -czf himeshazi-ovoda.tar.gz himeshaziOvoda/ Backend/ photos/

# Feltöltés szerverre
scp himeshazi-ovoda.tar.gz user@server:/var/www/

# Kicsomagolás
ssh user@server
cd /var/www/
tar -xzf himeshazi-ovoda.tar.gz
```

### 2. Adatbázis beállítás
```sql
-- Adatbázis létrehozása
CREATE DATABASE himeshazi_ovoda 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_hungarian_ci;

-- Felhasználó létrehozása
CREATE USER 'ovoda_user'@'localhost' 
IDENTIFIED BY 'erős_jelszó_itt';

-- Jogosultságok
GRANT SELECT, INSERT, UPDATE, DELETE 
ON himeshazi_ovoda.* 
TO 'ovoda_user'@'localhost';

FLUSH PRIVILEGES;
```

### 3. Adatok importálása
```bash
mysql -u ovoda_user -p himeshazi_ovoda < Backend/Database/himeshazi_ovoda.sql
```

### 4. Környezeti változók beállítása
```bash
cd /var/www/Backend/
cp .env.example .env
nano .env
```

**.env fájl tartalma:**
```env
DB_HOST=localhost
DB_NAME=himeshazi_ovoda
DB_USER=ovoda_user
DB_PASS=erős_jelszó_itt
ENVIRONMENT=production
DEBUG=false
ALLOWED_ORIGINS=https://yourdomain.com,https://www.yourdomain.com
```

### 5. Production konfiguráció aktiválása
```bash
cd /var/www/Backend/
php deploy.php
```

### 6. Jogosultságok beállítása
```bash
# Mappák
chmod 755 /var/www/Backend/logs/
chmod 755 /var/www/Backend/uploads/
chmod 644 /var/www/Backend/.htaccess

# Fájlok
find /var/www/ -type f -exec chmod 644 {} \;
find /var/www/ -type d -exec chmod 755 {} \;
```

## 🔒 Biztonsági beállítások

### 1. SSL tanúsítvány (Let's Encrypt)
```bash
# Certbot telepítése
sudo apt install certbot python3-certbot-apache

# Tanúsítvány kérése
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com
```

### 2. Firewall beállítás
```bash
# UFW aktiválása
sudo ufw enable
sudo ufw allow 22    # SSH
sudo ufw allow 80    # HTTP
sudo ufw allow 443   # HTTPS
```

### 3. PHP beállítások
```ini
# /etc/php/8.1/apache2/php.ini
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
expose_php = Off
upload_max_filesize = 5M
post_max_size = 5M
max_execution_time = 30
memory_limit = 128M
```

## 🌐 Web szerver konfiguráció

### Apache Virtual Host
```apache
<VirtualHost *:443>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/himeshaziOvoda/Frontend
    
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/yourdomain.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/yourdomain.com/privkey.pem
    
    <Directory /var/www/himeshaziOvoda/Frontend>
        AllowOverride All
        Require all granted
    </Directory>
    
    # API útvonalak
    Alias /Backend /var/www/Backend
    <Directory /var/www/Backend>
        AllowOverride All
        Require all granted
    </Directory>
    
    # Log fájlok
    ErrorLog ${APACHE_LOG_DIR}/ovoda_error.log
    CustomLog ${APACHE_LOG_DIR}/ovoda_access.log combined
</VirtualHost>
```

## 📊 Monitoring és karbantartás

### 1. Log fájlok figyelése
```bash
# PHP hibák
tail -f /var/log/php_errors.log

# Apache hibák
tail -f /var/log/apache2/ovoda_error.log

# Alkalmazás hibák
tail -f /var/www/Backend/logs/error.log
```

### 2. Automatikus backup
```bash
# Backup script létrehozása
cat > /home/user/backup.sh << 'EOF'
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u ovoda_user -p'password' himeshazi_ovoda > /backup/db_$DATE.sql
tar -czf /backup/files_$DATE.tar.gz /var/www/
find /backup/ -name "*.sql" -mtime +7 -delete
find /backup/ -name "*.tar.gz" -mtime +7 -delete
EOF

chmod +x /home/user/backup.sh

# Crontab beállítása
crontab -e
# Napi backup 2:00-kor
0 2 * * * /home/user/backup.sh
```

### 3. Frissítések
```bash
# Rendszer frissítések
sudo apt update && sudo apt upgrade

# SSL tanúsítvány megújítás
sudo certbot renew --dry-run
```

## 🧪 Tesztelés

### 1. API endpoint tesztelés
```bash
# Csoportok API
curl -s https://yourdomain.com/Backend/csoportok_api.php | jq .

# Szolgáltatások API
curl -s https://yourdomain.com/Backend/szolgaltatasok_api.php | jq .

# Kapcsolattartás API
curl -s https://yourdomain.com/Backend/kapcsolattartas_api.php | jq .
```

### 2. Biztonsági tesztelés
```bash
# SSL teszt
openssl s_client -connect yourdomain.com:443

# Security headers teszt
curl -I https://yourdomain.com/Backend/csoportok_api.php
```

## 🚨 Hibaelhárítás

### Gyakori problémák:

1. **500 Internal Server Error**
   - Ellenőrizd a PHP error log-ot
   - Jogosultságok ellenőrzése
   - .htaccess szintaxis ellenőrzése

2. **Adatbázis kapcsolat hiba**
   - .env fájl ellenőrzése
   - MySQL szolgáltatás állapota
   - Felhasználói jogosultságok

3. **CORS hibák**
   - ALLOWED_ORIGINS beállítás
   - Apache mod_headers modul

4. **Fájl feltöltés hiba**
   - Upload mappa jogosultságok
   - PHP upload beállítások
   - .htaccess fájl feltöltés korlátozások

## 📞 Támogatás

Ha problémák merülnek fel:
1. Ellenőrizd a log fájlokat
2. Teszteld a konfigurációt
3. Dokumentáld a hibát
4. Keresd fel a rendszergazdát

---

**🎉 Sikeres telepítés után az óvoda weboldala elérhető lesz: https://yourdomain.com**
