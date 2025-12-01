# Képkezelési útmutató

## Képek feltöltése

### 1. Automatikus feltöltés API-n keresztül
```javascript
const formData = new FormData();
formData.append('image', fileInput.files[0]);
formData.append('category', 'engagement'); // vagy 'travel', 'daily', 'wedding', 'general'

fetch('./api/upload.php', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => {
    if (data.status === 'success') {
        console.log('Kép sikeresen feltöltve:', data.data.filename);
    }
});
```

### 2. Manuális képcsere
1. Töltsd fel a képeket az `assets/images/gallery/` mappába
2. Frissítsd az adatbázis `gallery_images` tábláját:
```sql
UPDATE gallery_images 
SET filename = 'uj_kep.jpg' 
WHERE id = 1;
```

## Képformátumok
- **Támogatott formátumok:** JPG, JPEG, PNG, GIF, WebP
- **Maximális fájlméret:** 5MB
- **Ajánlott méret:** 800x600px vagy nagyobb
- **Minőség:** Legalább 72 DPI

## Kategóriák
- `engagement` - Jegyesség
- `travel` - Utazás
- `daily` - Mindennapi élet
- `wedding` - Esküvő
- `general` - Általános

## Automatikus funkciók
- **Fájlnév generálás:** kategoria_datum_ido_random.extension
- **Alt text generálás:** Automatikus leírás a kategória alapján
- **Adatbázis mentés:** Automatikus beszúrás a gallery_images táblába
- **Sort order:** Automatikus rendezési sorrend

## Példa képek
A következő fájlneveket használd a teszteléshez:
- `gallery1.jpg` - Jegyesség
- `gallery2.jpg` - Utazás  
- `gallery3.jpg` - Mindennapi élet
- `gallery4.jpg` - Jegyesség
- `gallery5.jpg` - Utazás
- `gallery6.jpg` - Mindennapi élet
