# Maintenance Scripts

## cleanup-expired-bookings.php

Ez a script automatikusan törli a lejárt, de még nem lemondott foglalásokat.

### Használat

**Manuális futtatás:**
```bash
php /Applications/MAMP/htdocs/Palyafoglalo/Bakcend/scripts/cleanup-expired-bookings.php
```

**Cron job beállítása (napi futtatás 2:00-kor):**
```bash
crontab -e
```

Adja hozzá ezt a sort:
```
0 2 * * * /usr/bin/php /Applications/MAMP/htdocs/Palyafoglalo/Bakcend/scripts/cleanup-expired-bookings.php >> /var/log/palyafoglalo-cleanup.log 2>&1
```

### Mit csinál?

- Megkeresi azokat a foglalásokat, ahol:
  - `end_datetime < NOW()` (már lejárt)
  - `status NOT IN ('cancelled', 'completed')` (nem le van mondva)
  - `payment_status = 'unpaid'` (nem fizetett)
  
- Törli ezeket a foglalásokat
- Audit log-ot vezet a törlésekről
- **A pénzt nem téríti vissza** (az üzleti logika szerint)

