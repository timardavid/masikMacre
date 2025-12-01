-- JDW - Kezdő adatok feltöltése
-- Ez a script feltölti az adatbázist a kezdő tartalommal

USE jdw_db;

-- ============================================
-- BEÁLLÍTÁSOK FRISSÍTÉSE
-- ============================================
UPDATE settings SET setting_value = 'JDW' WHERE setting_key = 'site_name';
UPDATE settings SET setting_value = 'info@jdw.hu' WHERE setting_key = 'site_email';

-- Induló vállalkozás statisztikák
INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES
('projects_completed', '0', 'number', 'Elkészült projektek száma'),
('happy_clients', '0', 'number', 'Elégedett ügyfelek száma'),
('years_experience', '0', 'number', 'Évek tapasztalat'),
('custom_solutions', '100', 'number', 'Egyedi megoldások százaléka');

-- ============================================
-- PORTFÓLIÓ PROJEKTEK
-- ============================================

-- OnEvent projekt
INSERT INTO portfolio (title, slug, category_id, client_name, description, project_url, thumbnail_image, gallery_images, technologies, display_order, is_featured, status) 
VALUES (
    'OnEvent Rendezvényszervező',
    'onevent',
    4, -- Vállalati kategória
    'OnEvent',
    'Professzionális rendezvényszervező weboldal Figmában tervezve. Modern, letisztult dizájn amely tökéletesen mutatja be a rendezvényszervező szolgáltatásait.',
    'https://onevent.hu/',
    'assets/photos/OnEvent.png',
    '["assets/photos/OnEvent.png"]',
    '["Figma", "UI/UX Design", "Branding"]',
    1,
    TRUE,
    'active'
);

-- Borsóház Pécs projekt
INSERT INTO portfolio (title, slug, category_id, client_name, description, project_url, thumbnail_image, gallery_images, technologies, display_order, is_featured, status) 
VALUES (
    'Borsóház Pécs',
    'borsohaz-pecs',
    4, -- Vállalati kategória
    'Borsóház',
    'Modern vállalati weboldal design Figmában tervezve. Reszponzív, gyors és felhasználóbarát megoldás.',
    'https://new.borsohazpecs.hu',
    'assets/photos/borsohaz.png',
    '["assets/photos/borsohaz.png"]',
    '["Figma", "UI/UX Design", "Responsive Design"]',
    2,
    TRUE,
    'active'
);

-- Veréb Gépészet projekt
INSERT INTO portfolio (title, slug, category_id, client_name, description, project_url, thumbnail_image, gallery_images, technologies, display_order, is_featured, status) 
VALUES (
    'Veréb Gépészet',
    'vereb-gepeszet',
    4, -- Vállalati kategória
    'Veréb Gépészet Kft.',
    'Professzionális gépészeti vállalkozás bemutatkozó oldal design Figmában. Reszponzív, modern megjelenés.',
    'https://verebgepesz.hu',
    'assets/photos/verebgepesz.png',
    '["assets/photos/verebgepesz.png"]',
    '["Figma", "UI/UX Design", "Branding"]',
    3,
    TRUE,
    'active'
);

-- ============================================
-- ÁRAZÁSI CSOMAGOK (TERMÉKEK)
-- ============================================

-- Kezdő Csomag
INSERT INTO products (name, slug, category_id, description, detailed_description, price, old_price, currency, features, preview_image, is_featured, status)
VALUES (
    'Kezdő Csomag',
    'kezdo-csomag',
    1, -- Landing Page kategória
    'Tökéletes kisvállalkozásoknak és induló vállalkozásoknak',
    'A Kezdő csomag ideális választás azoknak, akik most indítanak vállalkozást vagy egyszerű online jelenlétre van szükségük. Egy professzionálisan megtervezett egyoldalas weboldal designnal hatékonyan tudod bemutatni vállalkozásodat.',
    149000,
    NULL,
    'HUF',
    '["1 oldalas modern weboldal design (Figma)", "Teljesen reszponzív dizájn (mobil, tablet, desktop)", "Professzionális kapcsolati űrlap design", "Képgaléria / Portfólió szekció design", "Hírlevél feliratkozás funkció design", "Egyedi grafikai elemek és ikonok", "Design rendszer és style guide", "Interaktív Figma prototípus", "Kódolásra kész export", "Design dokumentáció", "3 hónap ingyenes design támogatás", "2 felülvizsgálati kör a tervezés során", "+Opcionális: HTML/CSS/JS kódolt változat"]',
    NULL,
    FALSE,
    'active'
);

-- Professzionális Csomag
INSERT INTO products (name, slug, category_id, description, detailed_description, price, old_price, currency, features, preview_image, is_featured, status)
VALUES (
    'Professzionális Csomag',
    'professzionalis-csomag',
    4, -- Vállalati kategória
    'A legtöbb vállalkozás választása - teljes körű design megoldás',
    'A Professzionális csomag komplett weboldal design megoldás középvállalkozásoknak, akik komolyabb online jelenlétre vágynak. Több aloldal design, admin panel design és blog funkció designnal bővítheted vállalkozásod kommunikációját.',
    299000,
    NULL,
    'HUF',
    '["Korlátlan számú aloldal design (Figma)", "Teljesen reszponzív és modern dizájn", "Egyedi grafikai elemek és ikonok", "Adminisztrációs felület design (CMS)", "Blog rendszer design cikk publikáláshoz", "Kapcsolati űrlap design email értesítéssel", "Hírlevél feliratkozás funkció design", "Haladó design elemek", "Social media integráció design", "Képgaléria / Portfólió szekció design", "GYIK (Gyakran Ismételt Kérdések) oldal design", "6 hónap ingyenes design támogatás", "3 felülvizsgálati kör a tervezés során"]',
    NULL,
    TRUE,
    'active'
);

-- Prémium Csomag
INSERT INTO products (name, slug, category_id, description, detailed_description, price, old_price, currency, features, preview_image, is_featured, status)
VALUES (
    'Prémium Csomag',
    'premium-csomag',
    4, -- Vállalati kategória
    'Teljes körű prémium design megoldás egyedi igényekre',
    'A Prémium csomag a legtöbbet nyújtó design megoldás azoknak, akik komplex funkciókat, korlátlan számú oldalt és prémium design szolgáltatásokat igényelnek. Egyedi modulokkal és prémium design támogatással.',
    549000,
    NULL,
    'HUF',
    '["Korlátlan számú aloldal design (Figma)", "Prémium egyedi dizájn", "Ügyfél fiók és bejelentkezés rendszer design", "Teljes admin panel design minden funkcióval", "Blog és hírlevél rendszer design", "Professzionális design audit és optimalizálás", "Haladó design elemek és animációk", "Biztonsági és adatvédelmi oldal design", "Social media teljes integráció design", "Többnyelvűség opció design", "Egyedi funkciók és modulok design", "Cookie bar és adatvédelmi oldal design", "12 hónap prémium design támogatás", "Korlátlan felülvizsgálati kör"]',
    NULL,
    FALSE,
    'active'
);

-- ============================================
-- KÉSZ!
-- ============================================
SELECT 'Adatok sikeresen feltöltve!' as message;

