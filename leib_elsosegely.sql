-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Dec 05, 2025 at 11:20 AM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `leib_elsosegely`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_info`
--

DROP TABLE IF EXISTS `contact_info`;
CREATE TABLE `contact_info` (
  `id` int NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text,
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `contact_info`
--

INSERT INTO `contact_info` (`id`, `company_name`, `owner_name`, `email`, `phone`, `address`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Leib Elsősegély Felszerelések Forgalmazása\r\nMunkahelyi elsősegély oktatás és felszerelések', 'Leib Roland', 'leib.roland@gmail.com', '06 20 913-0771', '7700 Mohács Szentháromság utca 38.', 'Főállásban az Országos Mentőszolgálatnál dolgozom.', 1, '2025-10-06 10:57:41', '2025-10-06 11:12:04');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text,
  `meta_description` text,
  `meta_keywords` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `title`, `slug`, `content`, `meta_description`, `meta_keywords`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Bemutatkozás', 'bemutatkozas', 'Engedje meg, hogy egy pár szót írjak a vállalkozásomról és a munkámról. Főállásban az Országos Mentőszolgálatnál dolgozom. Ezért különösen fontos számomra az emberek felvilágosítása, képzése, tudatosítása, nemcsak a civil életben, hanem üzemekben és a különböző tevékenységet folytató cégeken belül is. Több mint tízéves vállalkozói tapasztalatommal úgy gondolom és most már több mint száz partnerem is ezt igazolja, hogy egy munkahelyen igen is fontos, hogy legyen a jogszabályoknak megfelelő elsősegély felszerelés és megfelelően kiképzett elsősegélynyújtó is. Remélem, hogy az önök cégénél is fontos szempont és fogunk tudni segíteni közeljövőben akár oktatás akár munkavédelmi felszerelések, vagy elsősegély felszerelésekkel kapcsolatban.\r\n\r\nVállalkozásom nemcsak oktatást nyújt, és elsősegély felszereléseket értékesít önmagában, hanem az önök által megvásárolt elsősegély felszerelések folyamatos ellenőrzését és karbantartását végzi. Ez az jelenti, hogy a mentődobozok megvásárlása mellett az alábbi szolgáltatásokat nyújtjuk térítésmentesen Önöknek:\r\n\r\n– A felszerelések helyszínre szállítását\r\n\r\n– A felszerelések falra történő rögzítése\r\n\r\n– Utántöltés folyamatos biztosítása\r\n\r\n– Folyamatosan tájékoztatjuk Önöket a legújabb törvényi előírásokról\r\n\r\n– Keretszerződés\r\n\r\nTisztelettel: Leib Roland ', 'Leib Elsősegély Felszerelések Forgalmazása', NULL, 1, '2025-10-06 10:57:19', '2025-10-06 11:13:29');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `image`, `price`, `category`, `is_active`, `created_at`, `updated_at`) VALUES
(3, 'MASTER AID STERIBLOCK VÉRZÉS- CSILLAPÍTÓ SPRAY 50ML', 'A spray kalcium- és nátrium alginátot tartalmaz nagyon finom por formájában, amely egy mechanikus típusú rendkívül hatékony vérzéscsillapítást idéz elő. A spray segít a sebek körüli nedves környezet kialakításában is, mely segíti a sebgyógyulást.', 'verzescsillapito-spray-1.jpg', NULL, 'kezelés', 1, '2025-10-06 11:04:50', '2025-10-06 11:19:52'),
(4, 'BURN JEL GÉL ÉGÉSRE 50ML', 'A Water-Jel® Burn Jel egy víztiszta zselé, amely a legkomolyabb égési problémaek esetén is azonnal hűsíti az égett bőrfelületet, csillapítja a fájdalmat, gyengéden fertőtlenít, és megakadályozza a fertőzések bejutását a nyílt sebbe. Minden esetben gyorsabban alkalmazható, mint a hidegvizes hűtés, hatékonysága miatt kevesebb időt rabol el a tényleges segítségnyújtástól. Minden égéstípusra alkalmas, tűz által okozott és vegyi égésre éppúgy, mint a napégés kezelésére. A Burn Jel mihamarabb az égési felületre kenve fejti ki hatását, óvja a sejteket a további roncsolódástól, elvonja a hőt, ezáltal megakadályozza a hőhatás továbbterjedését a mélyebben fekvő érzékeny szövetek felé. A gél megakadályozza a baktériumok sebbe való bejutását, teafaolaj-tartalma gyengéden fertőtlenít. 4-5 perc múlva a kezelt terület normál testhőmérsékletű lesz. ', 'burnjel_50ml-1.jpg', NULL, 'kezelés', 1, '2025-10-06 11:04:50', '2025-10-06 11:19:52'),
(5, 'INNO-SEPT GÉL EXTRA HIGIÉNÉS KÉZFERTŐTLENÍTŐ 100 ML', 'Higiénés kézfertőtlenítésre, foglalkozásszerű és lakossági felhasználók részére készült gél (terméktípus: PT 1). Alkalmazható minden olyan területen, ahol szükség van a kéz fertőtlenítésére.', 'kezfertotlenito-gel-inno-sept-extra-100-ml-1.jpg', NULL, 'fertőtlenítés', 1, '2025-10-06 11:04:50', '2025-10-06 11:19:52'),
(6, 'Egészségügyi szekrény', 'Üzemekben, intézményekben az elsősegélynyújtó eszközök biztonságos tárolására szolgál.', 'eu-szekreny.jpg', NULL, 'szekrény', 1, '2025-10-06 11:04:56', '2025-10-06 11:19:52'),
(7, 'PLUM 5503 QuickFix adagoló+2x45db detect', 'Kimutatható fémszálas ragtapasz beépített fémszállal, amely kimutatható fémdetektorral. A ragtapaszok PE anyagból készültek, amely lehetővé teszi a bőr lélegzését, valamint vízálló és bőrbarát is. Méret: Sz: 23 cm. x M: 13,5 cm', 'sebtapasz-adagolo-kek-1.jpg', NULL, 'védő', 1, '2025-10-06 11:04:56', '2025-10-06 11:19:52'),
(8, 'QuickFix UNO Fehér sebtapasz adagoló rugalmas sebtapasszal', 'Az adagoló 45 darab ragtapaszt tartalmaz, és gyors segítséget biztosít apró sebek, vágások azonnali kezeléséhez. Minden ragtapasz külön csomagolt – higiénikus, egyszerű és azonnal használható, csak ki kell húzni az adagolóból. A QuickFix UNO kis méretének köszönhetően (8,5 x 13 x 3,5 cm) bárhol könnyedén elhelyezhető. Csavarokkal, vagy akár erősebb ragasztószalaggal is rögzíthető a falra.', 'sebtapasz-adagolo-feher-1.jpg', NULL, 'védő', 1, '2025-10-06 11:04:56', '2025-10-06 11:19:52'),
(9, 'QuickFix 45db/csomag fémszálas (detektálható) sebtapasz', 'A detektálható fémszálas QuickFix egy speciális ragtapasz, elsősorban élelmiszeripari felhasználásra, vagy olyan munkahelyekre ajánlott, ahol fontos a könnyű kimutathatóság. A ragtapaszok PE anyagból készültek, amely lehetővé teszi a bőr lélegzését, valamint vízálló és bőrbarát is.', 'detektalhato-sebtapasz-1.jpg', NULL, 'védő', 1, '2025-10-06 11:05:01', '2025-10-06 11:19:52'),
(10, 'Munkahelyi mentőláda 30 főig (I.) falra is szerelhető', 'Jogszabályban előírt tartalommal rendelkeznek. Munkahelyi elsősegélyként: I. 30 főig. Munkahelyi elsősegély felszerelés, melynek tartalma az MSZ 13553-as szabványnak megfelelő.', 'munkahelyi_mentolada_1.webp', NULL, 'mentőláda', 1, '2025-10-06 11:05:01', '2025-10-06 11:19:52'),
(11, 'Munkahelyi mentőláda 50 főig (II.) falra is szerelhető', 'Ez az elsősegély felszerelés megfelel az II-es kategória munkavédelmi követelményeinek: cégek, vállalkozások, üzemek részére akik 31-50 dolgozót foglalkoztatnak (31-50 főig). A munkahelyi elsősegélynyújtás egyik kiegészítő eszköze lehet a céges mentőláda – a 2-es jelű elsősegélycsomag legfeljebb 50 fő részére megfelelő. Munkahelyi elsősegély felszerelés, melynek tartalma az MSZ 13553-as szabványnak megfelelő.', 'munkahelyi_mentolada_50.webp', NULL, 'mentőláda', 1, '2025-10-06 11:05:01', '2025-10-06 11:19:52'),
(12, 'Munkahelyi mentőláda 100 főig (III.) falra is szerelhető', 'Minden munkahelyen készenlétben kell tartani a megfelelő összeállítású elsősegély-felszerelést! Ez az elsősegély felszerelés megfelel az III-as kategória munkavédelmi követelményeinek: cégek, vállalkozások, üzemek részére akik 51-100 dolgozót foglalkoztatnak (51-100 főig). Munkahelyi elsősegély felszerelés, melynek tartalma az MSZ 13553-as szabványnak megfelelő.', 'munkahelyi_mentolada_100.webp', NULL, 'mentőláda', 1, '2025-10-06 11:05:07', '2025-10-06 11:19:52'),
(13, 'Hordágy 6 fogantyús', 'PVC bevonatú\r\nVízhatlan\r\nKönnyen mosható anyagból\r\n6 fogantyúval a biztonságos betegszállításért\r\nMéret: 190×60 cm\r\nÖsszehajtható', 'vaszon-hordagy-6-fogantyus-1.jpg', NULL, 'hordagy', 1, '2025-10-06 11:05:07', '2025-10-06 11:19:52'),
(14, 'Hordágy, 4 fele összehajtható', '4 felé hajtható\r\nAlumínium rúd\r\nPVC bevonatú tűzálló lap\r\nMérete: nyitva 203x50x15 cm, csukva 96x17x15 cm\r\nSúlya: 7 kg\r\nTerhelhetősége: 120 kg', 'hordagy-4-fele-hajlithato-1.webp', NULL, 'hordagy', 1, '2025-10-06 11:05:07', '2025-10-06 11:19:52'),
(15, 'ALTIK Alkoholszonda 10db-os', 'Az Altik egyszer használatos alkoholszondákat azért választottuk, mert ez az országban elérhető egyik legjobb ár-érték arányú egyszer használatos alkoholszondája. A szonda az alkohol tájékoztató jellegű kimutatására szolgál a kilélegzett levegőben. A termék nem tartalmaz krómot. A lejárati idő és a használat folyamatábrája a dobozon megtalálható.', 'alkohol-szonda-egyszer-hasznalatos-1.jpg', NULL, 'diagnosztikai', 1, '2025-10-06 11:05:14', '2025-10-06 11:19:52'),
(16, 'Szemöblítő palack folyadékkal 500ml. ', 'A flakon 0,9%-os steril nátrium-klorid oldatot tartalmaz. Ergonómikus kupakkal gyártják, amely illeszkedik a szemgödörbe. Porvédő kupakkal látták el, és a címkén részletes használati utasítás olvasható. A flakont használhatjuk készletben, vagy önmagában is.', 'ph_neutral-200ml-1.jpg', NULL, 'kezelő', 1, '2025-10-06 11:05:14', '2025-10-06 11:19:52'),
(17, 'Szemöblítő állomás 1 x 500ml palackkal + fali tartó + piktogram\r\n\r\n', 'Fali állomás 1 x 500 ml Plum szemöblítővel és külön piktogrammal. Különösen olyan munkahelyeken alkalmas, ahol szükség lehet azonnali szemöblítésre, valamint kisebb és mobil munkahelyeken.', 'szemmoso-fali-tartoval-1.jpg', NULL, 'kezelő', 1, '2025-10-06 11:05:14', '2025-10-06 11:25:27'),
(18, 'QuickRinse szemkimosó ampullák', '1 x 5 darab szemkimosó ampulla utántöltő készlet, melyek egyenként 20 ml 0.9%-os steril nátrium-klorid sóoldatot tartalmaznak. Egyszerű, gyors és higiénikus segítséget biztosít kisebb szembalesetek esetén, például ha por, vagy piszok kerül a szembe; vagy ha a szem kiszárad munkavégzés közben.', 'szemmoso-ampulla-5x20ml.jpg', NULL, 'kezelő', 1, '2025-10-06 11:05:19', '2025-10-06 11:25:27');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `icon` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `title`, `description`, `icon`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Szállítás', ' A felszerelések helyszínre szállítását', 'truck', 1, '2025-10-06 10:57:33', '2025-10-06 11:23:20'),
(2, 'Rögzítés', 'A felszerelések falra történő rögzítése', 'tools', 1, '2025-10-06 10:57:33', '2025-10-06 11:23:20'),
(3, 'Utánpótlás', 'Utántöltés folyamatos biztosítása', 'refresh', 1, '2025-10-06 10:57:33', '2025-10-06 11:23:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_info`
--
ALTER TABLE `contact_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_info`
--
ALTER TABLE `contact_info`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
