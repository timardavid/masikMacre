# Cancellation Token Migration

## Futtatás

A cancellation token mező hozzáadásához futtasd a következő SQL scriptet:

```bash
mysql -u root -p palyafoglalo < /Applications/MAMP/htdocs/Palyafoglalo/Database/add_cancellation_token.sql
```

Vagy MySQL-ben:

```sql
USE palyafoglalo;
SOURCE /Applications/MAMP/htdocs/Palyafoglalo/Database/add_cancellation_token.sql;
```

## Mit csinál?

Hozzáadja a `cancellation_token` oszlopot a `bookings` táblához, amely:
- 64 karakter hosszú VARCHAR
- NULL értéket enged (régi foglalásoknál)
- Indexelve van a gyors kereséshez

Ez a token az email-ben küldött lemondási linkhez használatos.
