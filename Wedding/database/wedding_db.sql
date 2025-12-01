-- ===== WEDDING WEBSITE DATABASE =====
-- SQL adatbázis esküvői weboldalhoz
-- Teszt adatokkal és lorem ipsum szövegekkel

-- Adatbázis létrehozása
CREATE DATABASE IF NOT EXISTS wedding_website CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE wedding_website;

-- ===== TÁBLÁK LÉTREHOZÁSA =====

-- Párok információi
CREATE TABLE couples (
    id INT PRIMARY KEY AUTO_INCREMENT,
    bride_name VARCHAR(100) NOT NULL,
    groom_name VARCHAR(100) NOT NULL,
    wedding_date DATE NOT NULL,
    wedding_time TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Esküvői események
CREATE TABLE events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    event_time TIME NOT NULL,
    location VARCHAR(200) NOT NULL,
    address TEXT NOT NULL,
    icon VARCHAR(50) NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sztori idővonal
CREATE TABLE story_timeline (
    id INT PRIMARY KEY AUTO_INCREMENT,
    year VARCHAR(10) NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Galéria képek
CREATE TABLE gallery_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    filename VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255) NOT NULL,
    category VARCHAR(50) NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Kapcsolattartási információk
CREATE TABLE contact_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    bride_phone VARCHAR(20),
    groom_phone VARCHAR(20),
    email VARCHAR(100),
    rsvp_deadline DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- RSVP válaszok
CREATE TABLE rsvp_responses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    attendance ENUM('yes', 'no') NOT NULL,
    guest_count INT DEFAULT 1,
    message TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Weboldal beállítások
CREATE TABLE site_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ===== TESZT ADATOK BESZÚRÁSA =====

-- Pár információi
INSERT INTO couples (bride_name, groom_name, wedding_date, wedding_time) VALUES
('Jane', 'John', '2026-06-16', '14:00:00');

-- Esküvői események
INSERT INTO events (title, description, event_time, location, address, icon, sort_order) VALUES
('Ceremony', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.', '14:00:00', 'Main Church', 'Downtown, Main Square 1', 'fas fa-church', 1),
('Photography', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.', '15:30:00', 'Central Park', 'Downtown, Central District', 'fas fa-camera', 2),
('Dinner & Reception', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.', '18:00:00', 'Grand Hotel Restaurant', 'Downtown, Main Street 2', 'fas fa-utensils', 3);

-- Sztori idővonal
INSERT INTO story_timeline (year, title, description, sort_order) VALUES
('2020', 'First Meeting', 'We met at a summer festival where music and dance brought us together. John was playing guitar, and I was dancing to the music. Since then, we dance together through life every day.', 1),
('2022', 'First Love', 'On a beautiful spring evening, under the stars, John told me for the first time that he loves me. That moment changed everything, and our love story truly began.', 2),
('2024', 'The Proposal', 'During a romantic weekend in the mountains, when the sunset painted the landscape in gold, John got down on one knee and asked for my hand in marriage.', 3);

-- Galéria képek
INSERT INTO gallery_images (filename, alt_text, category, title, description, sort_order) VALUES
('gallery1.jpg', 'Proposal moment', 'engagement', 'Proposal', 'August 2023 - Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 1),
('gallery2.jpg', 'Travel memories', 'travel', 'Travel', 'Paris, 2022 - Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 2),
('gallery3.jpg', 'Daily life moments', 'daily', 'Daily Life', 'Home moments - Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 3),
('gallery4.jpg', 'Engagement ceremony', 'engagement', 'Engagement', 'Ring ceremony - Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 4),
('gallery5.jpg', 'Holiday memories', 'travel', 'Holiday', 'Greece, 2023 - Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 5),
('gallery6.jpg', 'Shared moments', 'daily', 'Shared Moments', 'Everyday happiness - Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 6);

-- Kapcsolattartási információk
INSERT INTO contact_info (bride_phone, groom_phone, email, rsvp_deadline) VALUES
('+1 555 123 4567', '+1 555 765 4321', 'john.jane.wedding@gmail.com', '2026-05-15');

-- Weboldal beállítások
INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_title', 'John & Jane - Wedding Invitation'),
('site_description', 'Join us as we celebrate our love and begin our journey together as husband and wife!'),
('hero_subtitle', 'Together we begin the most beautiful chapter of our lives'),
('rsvp_message', 'We can\'t wait to celebrate with you! Please let us know by May 15th if you\'ll be able to join us for our special day.'),
('footer_message', 'Join us on our special day as we begin our journey together as husband and wife.'),
('countdown_message', 'The big day has arrived!'),
('gallery_title', 'Gallery'),
('story_title', 'Our Story'),
('events_title', 'Events'),
('rsvp_title', 'RSVP'),
('countdown_title', 'Countdown');

-- ===== INDEXEK LÉTREHOZÁSA =====
CREATE INDEX idx_events_sort ON events(sort_order);
CREATE INDEX idx_story_sort ON story_timeline(sort_order);
CREATE INDEX idx_gallery_sort ON gallery_images(sort_order);
CREATE INDEX idx_gallery_category ON gallery_images(category);
CREATE INDEX idx_rsvp_submitted ON rsvp_responses(submitted_at);

-- ===== MEGJEGYZÉSEK =====
-- Ez az adatbázis tartalmazza az összes szükséges adatot a weboldal működéséhez
-- A képek fájlnevei a 'gallery_images' táblában vannak tárolva
-- A lorem ipsum szövegek könnyen lecserélhetők valódi tartalomra
-- Az RSVP válaszok automatikusan kerülnek tárolásra
-- Minden adat UTF-8 kódolású a magyar karakterek támogatásához
