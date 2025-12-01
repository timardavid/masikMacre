-- Leib Elsősegély Adatbázis Séma
-- WordPress oldal átvitele

CREATE DATABASE IF NOT EXISTS leib_elsosegely CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE leib_elsosegely;

-- Főoldal tartalom tábla
CREATE TABLE pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content TEXT,
    meta_description TEXT,
    meta_keywords TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Termékek tábla
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    price DECIMAL(10,2),
    category VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Szolgáltatások tábla
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    icon VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Kapcsolat információk tábla
CREATE TABLE contact_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    owner_name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(50),
    address TEXT,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Kezdő adatok beszúrása
INSERT INTO pages (title, slug, content, meta_description) VALUES 
('Bemutatkozás', 'bemutatkozas', 'Kedves Látogató!

Engedje meg, hogy egy pár szót írjak a vállalkozásomról és a munkámról. Főállásban az Országos Mentőszolgálatnál dolgozom. Ezért különösen fontos számomra az emberek felvilágosítása, képzése, tudatosítása, nemcsak a civil életben, hanem üzemekben és a különböző tevékenységet folytató cégeken belül is. Több mint tízéves vállalkozói tapasztalatommal úgy gondolom és most már több mint száz partnerem is ezt igazolja, hogy egy munkahelyen igen is fontos, hogy legyen a jogszabályoknak megfelelő elsősegély felszerelés és megfelelően kiképzett elsősegélynyújtó is. Remélem, hogy az önök cégénél is fontos szempont és fogunk tudni segíteni közeljövőben akár oktatás akár munkavédelmi felszerelések, vagy elsősegély felszerelésekkel kapcsolatban.

Vállalkozásom nemcsak oktatást nyújt, és elsősegély felszereléseket értékesít önmagában, hanem az önök által megvásárolt elsősegély felszerelések folyamatos ellenőrzését és karbantartását végzi. Ez az jelenti, hogy a mentődobozok megvásárlása mellett az alábbi szolgáltatásokat nyújtjuk térítésmentesen Önöknek:

– A felszerelések helyszínre szállítását
– A felszerelések falra történő rögzítése
– Utántöltés folyamatos biztosítása
– Folyamatosan tájékoztatjuk Önöket a legújabb törvényi előírásokról
– Keretszerződés

Tisztelettel: Leib Roland', 'Leib Roland elsősegély felszerelések és oktatás szolgáltatásai', 'elsősegély, felszerelések, oktatás, mentőszolgálat, Leib Roland');

INSERT INTO services (title, description, icon) VALUES 
('Szállítás', 'A felszerelések helyszínre szállítását', 'truck'),
('Rögzítés', 'A felszerelések falra történő rögzítése', 'tools'),
('Utántöltés', 'Utántöltés folyamatos biztosítása', 'refresh'),
('Tájékoztatás', 'Folyamatosan tájékoztatjuk Önöket a legújabb törvényi előírásokról', 'info'),
('Keretszerződés', 'Keretszerződés', 'file-contract');

INSERT INTO products (name, description, image, category) VALUES 
('Alkohol szonda egyszer használatos', 'Egyszer használatos alkohol szonda', 'alkohol-szonda-egyszer-hasznalatos-1.jpg', 'diagnosztikai'),
('Burnjel 50ml', 'Égési sérülések kezelésére', 'burnjel_50ml-1.jpg', 'kezelő'),
('Detektálható sebtapasz', 'Detektálható sebtapasz', 'detektalhato-sebtapasz-1.jpg', 'védő'),
('EU szekrény', 'EU szabványú elsősegély szekrény', 'eu-szekreny.jpg', 'szekrény'),
('Hordagy 4 féle hajlítható', 'Hordagy 4 féle hajlítható', 'hordagy-4-fele-hajlithato-1.webp', 'hordagy'),
('Kézfertőtlenítő gel', 'Inno Sept Extra 100ml', 'kezfertotlenito-gel-inno-sept-extra-100-ml-1.jpg', 'fertőtlenítő'),
('Munkahelyi mentőláda', 'Munkahelyi mentőláda', 'munkahelyi_mentolada_1.webp', 'mentőláda'),
('PH semleges', '200ml PH semleges oldat', 'ph_neutral-200ml-1.jpg', 'kezelő'),
('Sebtapasz adagoló fehér', 'Sebtapasz adagoló fehér', 'sebtapasz-adagolo-feher-1.jpg', 'védő'),
('Sebtapasz adagoló kék', 'Sebtapasz adagoló kék', 'sebtapasz-adagolo-kek-1.jpg', 'védő'),
('Szemmosó ampulla', '5x20ml szemmosó ampulla', 'szemmoso-ampulla-5x20ml.jpg', 'kezelő'),
('Szemmosó fali tartóval', 'Szemmosó fali tartóval', 'szemmoso-fali-tartoval-1.jpg', 'kezelő'),
('Vászon hordagy', '6 fogantyús vászon hordagy', 'vaszon-hordagy-6-fogantyus-1.jpg', 'hordagy'),
('Vérzés csillapító spray', 'Vérzés csillapító spray', 'verzescsillapito-spray-1.jpg', 'kezelő');

INSERT INTO contact_info (company_name, owner_name, description) VALUES 
('Leib Elsősegély Felszerelések Forgalmazása', 'Leib Roland', 'Főállásban az Országos Mentőszolgálatnál dolgozom. Több mint tízéves vállalkozói tapasztalatommal és több mint száz partneremmel biztosítjuk a megfelelő elsősegély felszereléseket és oktatást.');
