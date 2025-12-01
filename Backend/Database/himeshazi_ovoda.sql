-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Oct 03, 2025 at 03:02 PM
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
-- Database: `himeshazi_ovoda`
--

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `AddNewPersonnel`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddNewPersonnel` (IN `p_role` VARCHAR(255), IN `p_name` VARCHAR(255), IN `p_order_number` INT)   BEGIN
    INSERT INTO personnel (role, name, order_number)
    VALUES (p_role, p_name, p_order_number);
END$$

DROP PROCEDURE IF EXISTS `GetActivePersonnel`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetActivePersonnel` ()   BEGIN
    SELECT 
        role,
        name
    FROM
        personnel
    WHERE
        is_deleted = FALSE
    ORDER BY
        order_number ASC;
END$$

DROP PROCEDURE IF EXISTS `SoftDeletePersonnel`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SoftDeletePersonnel` (IN `p_id` INT)   BEGIN
    UPDATE personnel
    SET
        is_deleted = TRUE
    WHERE
        id = p_id;
END$$

DROP PROCEDURE IF EXISTS `sp_add_program_item`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_add_program_item` (IN `p_school_year` VARCHAR(20), IN `p_month` TINYINT, IN `p_section_title` VARCHAR(100), IN `p_title` VARCHAR(200), IN `p_details` VARCHAR(500), IN `p_starts_on` DATE, IN `p_ends_on` DATE, IN `p_is_all_day` TINYINT, IN `p_sort_order` INT)   BEGIN
  INSERT INTO annual_program_items
    (school_year, month, section_title, title, details, starts_on, ends_on, is_all_day, sort_order)
  VALUES
    (p_school_year, NULLIF(p_month,0), NULLIF(p_section_title,''), p_title, NULLIF(p_details,''), p_starts_on, p_ends_on, IFNULL(p_is_all_day,1), IFNULL(p_sort_order,0));
END$$

DROP PROCEDURE IF EXISTS `sp_add_staff_member`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_add_staff_member` (IN `p_category_slug` VARCHAR(64), IN `p_name` VARCHAR(120), IN `p_role_title` VARCHAR(150), IN `p_bio` TEXT, IN `p_photo_url` VARCHAR(255), IN `p_email` VARCHAR(150), IN `p_phone` VARCHAR(50), IN `p_is_featured` TINYINT, IN `p_sort_order` INT UNSIGNED, IN `p_active` TINYINT)   BEGIN
  DECLARE v_category_id INT UNSIGNED;

  SELECT id INTO v_category_id FROM staff_category WHERE slug = p_category_slug AND active = 1 LIMIT 1;
  IF v_category_id IS NULL THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Nincs ilyen aktív kategória (slug).';
  END IF;

  INSERT INTO staff_member
    (category_id, name, role_title, bio, photo_url, email, phone, is_featured, sort_order, active)
  VALUES
    (v_category_id, p_name, p_role_title, p_bio, p_photo_url, p_email, p_phone,
     IFNULL(p_is_featured,0), IFNULL(p_sort_order,0), IFNULL(p_active,1));
END$$

DROP PROCEDURE IF EXISTS `sp_delete_group`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_delete_group` (IN `group_id` INT UNSIGNED)   BEGIN
    DELETE FROM classes WHERE id = group_id;
END$$

DROP PROCEDURE IF EXISTS `sp_delete_staff_member`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_delete_staff_member` (IN `p_id` INT UNSIGNED)   BEGIN
  UPDATE staff_member SET active = 0 WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS `sp_get_enrollment`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_enrollment` (IN `p_school_year` VARCHAR(20))   BEGIN
  SELECT
    school_year, period_text, start_date, end_date, status, notice, documents,
    mandatory_condition, optional_condition, signature_place_date,
    signature_name, signature_title, updated_at
  FROM enrollment_info
  WHERE school_year = p_school_year
  LIMIT 1;
END$$

DROP PROCEDURE IF EXISTS `sp_get_program`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_program` (IN `p_school_year` VARCHAR(20))   BEGIN
  -- Hónaphoz kötött pontok (1–12), majd a szekciócímhez kötöttek (month IS NULL), mind rendezve
  SELECT id, school_year, month, section_title, title, details, starts_on, ends_on, is_all_day, sort_order, created_at
  FROM annual_program_items
  WHERE school_year = p_school_year
  ORDER BY
    CASE WHEN month IS NULL THEN 99 ELSE month END,
    COALESCE(starts_on, '9999-12-31'),
    sort_order, id;
END$$

DROP PROCEDURE IF EXISTS `sp_get_program_by_month`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_program_by_month` (IN `p_school_year` VARCHAR(20), IN `p_month` TINYINT)   BEGIN
  SELECT id, school_year, month, section_title, title, details, starts_on, ends_on, is_all_day, sort_order, created_at
  FROM annual_program_items
  WHERE school_year = p_school_year AND month = p_month
  ORDER BY COALESCE(starts_on, '9999-12-31'), sort_order, id;
END$$

DROP PROCEDURE IF EXISTS `sp_get_staff_categories`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_staff_categories` ()   BEGIN
  SELECT id, slug, name, description, display_order, active
  FROM staff_category
  ORDER BY active DESC, display_order ASC, name ASC;
END$$

DROP PROCEDURE IF EXISTS `sp_get_staff_members`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_staff_members` (IN `p_only_active` TINYINT)   BEGIN
  SELECT
    sm.id,
    sc.slug AS category_slug,
    sc.name AS category_name,
    sm.name,
    sm.role_title,
    sm.bio,
    sm.photo_url,
    sm.email,
    sm.phone,
    sm.is_featured,
    sm.sort_order,
    sm.active,
    sm.created_at,
    sm.updated_at
  FROM staff_member sm
  JOIN staff_category sc ON sc.id = sm.category_id
  WHERE (p_only_active IS NULL OR p_only_active = 0 OR sm.active = 1)
  ORDER BY sc.display_order ASC, sm.sort_order ASC, sm.is_featured DESC, sm.name ASC;
END$$

DROP PROCEDURE IF EXISTS `sp_set_enrollment`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_set_enrollment` (IN `p_school_year` VARCHAR(20), IN `p_period_text` VARCHAR(100), IN `p_start_date` DATE, IN `p_end_date` DATE, IN `p_status` ENUM('hamarosan','folyamatban','lezarult'), IN `p_notice` TEXT, IN `p_documents` TEXT, IN `p_mandatory_condition` TEXT, IN `p_optional_condition` TEXT, IN `p_signature_place_date` VARCHAR(100), IN `p_signature_name` VARCHAR(100), IN `p_signature_title` VARCHAR(100))   BEGIN
  INSERT INTO enrollment_info
    (school_year, period_text, start_date, end_date, status, notice, documents,
     mandatory_condition, optional_condition, signature_place_date, signature_name, signature_title)
  VALUES
    (p_school_year, p_period_text, p_start_date, p_end_date, p_status, p_notice, p_documents,
     p_mandatory_condition, p_optional_condition, p_signature_place_date, p_signature_name, p_signature_title)
  ON DUPLICATE KEY UPDATE
    period_text = VALUES(period_text),
    start_date = VALUES(start_date),
    end_date = VALUES(end_date),
    status = VALUES(status),
    notice = VALUES(notice),
    documents = VALUES(documents),
    mandatory_condition = VALUES(mandatory_condition),
    optional_condition = VALUES(optional_condition),
    signature_place_date = VALUES(signature_place_date),
    signature_name = VALUES(signature_name),
    signature_title = VALUES(signature_title);
END$$

DROP PROCEDURE IF EXISTS `sp_update_staff_member`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_staff_member` (IN `p_id` INT UNSIGNED, IN `p_category_slug` VARCHAR(64), IN `p_name` VARCHAR(120), IN `p_role_title` VARCHAR(150), IN `p_bio` TEXT, IN `p_photo_url` VARCHAR(255), IN `p_email` VARCHAR(150), IN `p_phone` VARCHAR(50), IN `p_is_featured` TINYINT, IN `p_sort_order` INT UNSIGNED, IN `p_active` TINYINT)   BEGIN
  DECLARE v_category_id INT UNSIGNED;

  SELECT id INTO v_category_id FROM staff_category WHERE slug = p_category_slug LIMIT 1;
  IF v_category_id IS NULL THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Nincs ilyen kategória (slug).';
  END IF;

  UPDATE staff_member
  SET category_id = v_category_id,
      name = p_name,
      role_title = p_role_title,
      bio = p_bio,
      photo_url = p_photo_url,
      email = p_email,
      phone = p_phone,
      is_featured = IFNULL(p_is_featured, is_featured),
      sort_order = IFNULL(p_sort_order, sort_order),
      active = IFNULL(p_active, active)
  WHERE id = p_id;
END$$

DROP PROCEDURE IF EXISTS `sp_upsert_staff_category`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_upsert_staff_category` (IN `p_slug` VARCHAR(64), IN `p_name` VARCHAR(120), IN `p_description` TEXT, IN `p_display_order` INT UNSIGNED, IN `p_active` TINYINT)   BEGIN
  INSERT INTO staff_category (slug, name, description, display_order, active)
  VALUES (p_slug, p_name, p_description, p_display_order, IFNULL(p_active,1))
  ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    description = VALUES(description),
    display_order = VALUES(display_order),
    active = VALUES(active);
END$$

DROP PROCEDURE IF EXISTS `UpdatePersonnel`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdatePersonnel` (IN `p_id` INT, IN `p_role` VARCHAR(255), IN `p_name` VARCHAR(255), IN `p_order_number` INT)   BEGIN
    UPDATE personnel
    SET
        role = p_role,
        name = p_name,
        order_number = p_order_number
    WHERE
        id = p_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `annual_program_items`
--

DROP TABLE IF EXISTS `annual_program_items`;
CREATE TABLE `annual_program_items` (
  `id` int UNSIGNED NOT NULL,
  `school_year` varchar(20) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `month` tinyint UNSIGNED DEFAULT NULL,
  `section_title` varchar(100) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `details` varchar(500) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `starts_on` date DEFAULT NULL,
  `ends_on` date DEFAULT NULL,
  `is_all_day` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `annual_program_items`
--

INSERT INTO `annual_program_items` (`id`, `school_year`, `month`, `section_title`, `title`, `details`, `starts_on`, `ends_on`, `is_all_day`, `sort_order`, `created_at`) VALUES
(57, '2025/2026', 9, NULL, 'Óvoda kezdés', NULL, '2025-09-01', NULL, 1, 10, '2025-09-16 19:01:40'),
(58, '2025/2026', 9, NULL, 'Szülői értekezlet', NULL, '2025-09-04', NULL, 1, 20, '2025-09-16 19:01:40'),
(59, '2025/2026', 9, NULL, 'Új óvodások beszoktatása', 'Folyamatos', NULL, NULL, 1, 30, '2025-09-16 19:01:40'),
(60, '2025/2026', 9, NULL, 'Logopédiai felmérések', NULL, NULL, NULL, 1, 40, '2025-09-16 19:01:40'),
(61, '2025/2026', 9, NULL, 'A népmese napja', NULL, '2025-09-30', NULL, 1, 50, '2025-09-16 19:01:40'),
(62, '2025/2026', 10, NULL, 'Megemlékezés október 23.-ról', NULL, '2025-10-22', NULL, 1, 40, '2025-09-16 19:01:52'),
(63, '2025/2026', 10, NULL, 'Nemzeti Ünnep - munkaszüneti nap', '(csütörtök)', '2025-10-23', NULL, 1, 50, '2025-09-16 19:01:52'),
(64, '2025/2026', 10, NULL, 'Munkaszüneti nap', '(péntek)', '2025-10-24', NULL, 1, 60, '2025-09-16 19:01:52'),
(65, '2025/2026', 10, NULL, 'Nevelés nélküli munkanap', '(hétfő)', '2025-10-27', NULL, 1, 70, '2025-09-16 19:01:52'),
(66, '2025/2026', 10, NULL, 'Iskolai őszi szünet', 'október 31-ig tart.', NULL, NULL, 1, 80, '2025-09-16 19:01:52'),
(67, '2025/2026', 11, NULL, 'Mindenszentek', '(szombat)', '2025-11-01', NULL, 1, 10, '2025-09-16 19:01:52'),
(68, '2025/2026', 11, NULL, 'Megemlékezés a Halottak napjáról, séta a temetőbe - gyertyagyújtás', '(hétfő)', '2025-11-03', NULL, 1, 20, '2025-09-16 19:01:52'),
(69, '2025/2026', 11, NULL, '\"Tök jó nap\" - Márton-napi lampionos felvonulás', '(kedd)', '2025-11-11', NULL, 1, 30, '2025-09-16 19:01:52'),
(70, '2025/2026', 11, NULL, 'Advent, karácsonyi készülődés hónapja - közös készülődés a szülőkkel csoportszinten', '(péntek)', '2025-11-28', NULL, 1, 40, '2025-09-16 19:01:52'),
(71, '2025/2026', 12, NULL, 'Karácsony', NULL, '2025-12-24', '2025-12-28', 1, 30, '2025-09-16 19:01:52'),
(72, '2025/2026', 12, NULL, 'Nevelés nélküli munkanapok - Téli karbantartás', NULL, '2025-12-29', '2025-12-31', 1, 40, '2025-09-16 19:01:52'),
(73, '2025/2026', 1, NULL, 'Munkaszüneti napok', NULL, '2026-01-01', '2026-01-02', 1, 10, '2025-09-16 19:01:52'),
(74, '2025/2026', 1, NULL, 'Csoportszülői értekezletek', NULL, '2026-01-15', NULL, 1, 20, '2025-09-16 19:01:52'),
(75, '2025/2026', 1, NULL, 'Farsangi bál', 'alternatív időpont: febr. 6.', '2026-01-30', NULL, 1, 30, '2025-09-16 19:01:52'),
(76, '2025/2026', 2, NULL, 'Medve nap', NULL, '2026-02-02', NULL, 1, 10, '2025-09-16 19:01:52'),
(77, '2025/2026', 2, NULL, 'Fánknap', NULL, NULL, NULL, 1, 20, '2025-09-16 19:01:52'),
(78, '2025/2026', 2, NULL, 'Busók érkezése', NULL, NULL, NULL, 1, 30, '2025-09-16 19:01:52'),
(79, '2025/2026', 2, NULL, 'Farsangi Bál', NULL, '2026-02-06', NULL, 1, 40, '2025-09-16 19:01:52'),
(80, '2025/2026', 3, NULL, 'IKT projekthét', NULL, '2026-03-03', '2026-03-07', 1, 10, '2025-09-16 19:01:52'),
(81, '2025/2026', 3, NULL, 'Nyílt nap a leendő első osztályosok számára', NULL, NULL, NULL, 1, 20, '2025-09-16 19:01:52'),
(82, '2025/2026', 3, NULL, 'Szülői értekezlet a leendő első osztályosok szüleinek', NULL, NULL, NULL, 1, 30, '2025-09-16 19:01:52'),
(83, '2025/2026', 3, NULL, 'Március 15-i ünnepség, koszorúzás', '(péntek)', '2026-03-13', NULL, 1, 40, '2025-09-16 19:01:52'),
(84, '2025/2026', 3, NULL, 'Víz világnapja', NULL, '2026-03-23', NULL, 1, 50, '2025-09-16 19:01:52'),
(85, '2025/2026', 3, NULL, 'Vár-Lak nyílt nap az óvodában', '(hétfő)', '2026-03-30', NULL, 1, 60, '2025-09-16 19:01:52'),
(86, '2025/2026', 4, NULL, 'Nagypéntek', '(munkaszüneti nap)', '2026-04-03', NULL, 1, 10, '2025-09-16 19:01:52'),
(87, '2025/2026', 4, NULL, 'Húsvét hétfő', '(munkaszüneti nap)', '2026-04-06', NULL, 1, 20, '2025-09-16 19:01:52'),
(88, '2025/2026', 4, NULL, 'Nevelés nélküli munkanap', '(kedd)', '2026-04-07', NULL, 1, 30, '2025-09-16 19:01:52'),
(89, '2025/2026', 4, NULL, 'Iskolai tavaszi szünet', 'április 10-ig tart.', NULL, NULL, 1, 40, '2025-09-16 19:01:52'),
(90, '2025/2026', 4, NULL, 'A költészet napja', '(április 11.)', '2026-04-13', NULL, 1, 50, '2025-09-16 19:01:52'),
(91, '2025/2026', 4, NULL, 'Német Nemzetiségi Projekthét', '(ez lehet hosszabb is)', '2026-04-20', '2026-04-24', 1, 60, '2025-09-16 19:01:52'),
(92, '2025/2026', 4, NULL, 'Óvodai beiratkozás', NULL, NULL, NULL, 1, 70, '2025-09-16 19:01:52'),
(93, '2025/2026', 4, NULL, 'Föld napja', NULL, '2026-04-24', NULL, 1, 80, '2025-09-16 19:01:52'),
(94, '2025/2026', 5, NULL, 'Kirándulások csoportszinten', '(elmenős!)', NULL, NULL, 1, 10, '2025-09-16 19:01:52'),
(95, '2025/2026', 5, NULL, 'Munka ünnepe - munkaszüneti nap', '(péntek)', '2026-05-01', NULL, 1, 20, '2025-09-16 19:01:52'),
(96, '2025/2026', 5, NULL, 'Anyák napja csoportszinten 16 órától', NULL, '2026-05-05', NULL, 1, 30, '2025-09-16 19:01:52'),
(97, '2025/2026', 5, NULL, 'Madarak és Fák napja', NULL, '2026-05-08', NULL, 1, 40, '2025-09-16 19:01:52'),
(98, '2025/2026', 5, NULL, 'Méhek világnapja', NULL, '2026-05-21', NULL, 1, 50, '2025-09-16 19:01:52'),
(99, '2025/2026', 6, NULL, 'Sportnap - ovitorna, versenyek, meghívni az OVIKÉZI által, a BOZSIK ÁLTAL a vezetőket', NULL, NULL, NULL, 1, 10, '2025-09-16 19:01:52'),
(100, '2025/2026', 6, NULL, 'Nemzeti összetartozás napja', NULL, '2026-06-04', NULL, 1, 20, '2025-09-16 19:01:52'),
(101, '2025/2026', 6, NULL, 'Ballagás és évzáró', '(péntek)', '2026-06-12', NULL, 1, 30, '2025-09-16 19:01:52');

-- --------------------------------------------------------

--
-- Table structure for table `csoportok`
--

DROP TABLE IF EXISTS `csoportok`;
CREATE TABLE `csoportok` (
  `id` int UNSIGNED NOT NULL,
  `nev` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL,
  `leiras` text CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci,
  `photo_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `csoportok`
--

INSERT INTO `csoportok` (`id`, `nev`, `leiras`, `photo_url`, `active`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'Pillangó csoport', NULL, '../photos/pillango_csoport.jpg', 1, 1, '2025-09-27 07:37:04', '2025-09-27 07:37:04'),
(2, 'Százszorszép csoport', NULL, '../photos/szazszorszep_csoport_terem.jpg', 1, 2, '2025-09-27 07:37:04', '2025-09-27 07:37:04'),
(3, 'Szivárvány csoport', NULL, '../photos/szivarvany_csoport.jpg', 1, 3, '2025-09-27 07:37:04', '2025-09-27 07:37:04');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_info`
--

DROP TABLE IF EXISTS `enrollment_info`;
CREATE TABLE `enrollment_info` (
  `id` int UNSIGNED NOT NULL,
  `school_year` varchar(20) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `period_text` varchar(100) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('hamarosan','folyamatban','lezarult') COLLATE utf8mb4_hungarian_ci DEFAULT 'hamarosan',
  `notice` text COLLATE utf8mb4_hungarian_ci,
  `documents` text COLLATE utf8mb4_hungarian_ci,
  `mandatory_condition` text COLLATE utf8mb4_hungarian_ci,
  `optional_condition` text COLLATE utf8mb4_hungarian_ci,
  `signature_place_date` varchar(100) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `signature_name` varchar(100) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `signature_title` varchar(100) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `enrollment_info`
--

INSERT INTO `enrollment_info` (`id`, `school_year`, `period_text`, `start_date`, `end_date`, `status`, `notice`, `documents`, `mandatory_condition`, `optional_condition`, `signature_place_date`, `signature_name`, `signature_title`, `updated_at`) VALUES
(1, '2026', '2026. április', NULL, NULL, 'hamarosan', 'A lentiek a 2026-os beiratkozási időszakra vonatkoznak. A pontos dátumokért figyeld a honlapot vagy vedd fel velünk a kapcsolatot!', 'A gyermek nevére kiállított személyi azonosító (születési anyakönyvi kivonat, útlevél).\nA gyermek lakcímkártyája.\nA gyermek TAJ kártyája.\nA gyermek anyakönyvi kivonata (amennyiben az azonosító nem helyettesíti).\nA szülő személyi azonosító és lakcímet igazoló hatósági igazolványa.', 'Azokat a gyermekeket köteles beíratni a szülő, akik 2026. augusztus 31-ig betöltik a 3. életévüket.', 'Beíratható az a gyermek is, aki a 2026/2027-es nevelési évben tölti be a 3. életévét (pl. szeptember 1. után születettek).', 'Himesháza, 2026. április', 'Leib Rolandné', 'Igazgató', '2025-09-16 19:37:49');

-- --------------------------------------------------------

--
-- Table structure for table `fotok`
--

DROP TABLE IF EXISTS `fotok`;
CREATE TABLE `fotok` (
  `foto_id` int NOT NULL,
  `fajlnev` varchar(255) NOT NULL,
  `eleresi_ut` text NOT NULL,
  `cim` varchar(255) DEFAULT NULL,
  `leiras` text,
  `feltoltes_datum` datetime DEFAULT CURRENT_TIMESTAMP,
  `esemeny_tipus` varchar(100) DEFAULT NULL,
  `kapcsolodo_esemeny_id` int DEFAULT NULL,
  `lathato_a_weblapon` tinyint(1) DEFAULT '1',
  `sorrend` int DEFAULT NULL,
  `tag_lista` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `hirek`
--

DROP TABLE IF EXISTS `hirek`;
CREATE TABLE `hirek` (
  `id` int NOT NULL,
  `cim` varchar(255) NOT NULL,
  `szoveg` text NOT NULL,
  `datum` date NOT NULL,
  `letrehozva` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `hirek`
--

INSERT INTO `hirek` (`id`, `cim`, `szoveg`, `datum`, `letrehozva`) VALUES
(1, 'Visszatérés az óvodába', '2025. augusztus 25-től a megszokott rend szerint várjuk a gyermekeket intézményünkben, minden hétköznap 6.30-14.30-ig.', '2025-08-25', '2025-08-07 09:16:17'),
(2, 'Nevelési év kezdete', 'A következő nevelési év hivatalos első napja: 2025. szeptember 1. (hétfő).', '2025-09-01', '2025-08-07 09:16:17'),
(3, 'Szülői értekezlet', '2025. szeptember 4-én tartjuk az év eleji csoport szülői értekezleteket 16.30-tól, ahol minden szükséges/ fontos információt megtudhatnak a szülők az év menetéről, programjainkról.', '2025-09-04', '2025-08-07 09:16:17');

-- --------------------------------------------------------

--
-- Table structure for table `kapcsolattartas`
--

DROP TABLE IF EXISTS `kapcsolattartas`;
CREATE TABLE `kapcsolattartas` (
  `kapcsolattartas_id` int NOT NULL,
  `tipus` varchar(50) NOT NULL,
  `esemeny_nev` varchar(255) DEFAULT NULL,
  `pedagogus_id` int DEFAULT NULL,
  `kezdet_datum` date DEFAULT NULL,
  `kezdet_ido` time DEFAULT NULL,
  `veg_ido` time DEFAULT NULL,
  `gyakorisag` varchar(50) DEFAULT NULL,
  `nap_a_heten` varchar(20) DEFAULT NULL,
  `nevelesi_ev` varchar(9) DEFAULT NULL,
  `megjegyzes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `kapcsolattartas`
--

INSERT INTO `kapcsolattartas` (`kapcsolattartas_id`, `tipus`, `esemeny_nev`, `pedagogus_id`, `kezdet_datum`, `kezdet_ido`, `veg_ido`, `gyakorisag`, `nap_a_heten`, `nevelesi_ev`, `megjegyzes`) VALUES
(1, 'Szülői értekezlet', 'Szülői értekezlet', NULL, NULL, NULL, NULL, 'Évente két-három alkalommal', 'Szept, Jan, Máj', NULL, 'Évente két-három alkalommal: szeptemberben, januárban, májusban.'),
(2, 'Fogadó óra', 'Igazgatói fogadó óra - Leib Rolandné', NULL, NULL, '08:00:00', '11:00:00', 'Hetente', 'Péntek', NULL, 'Előre egyeztetett időpontban. Igény esetén az igazgatóval.'),
(3, 'Fogadó óra', 'Pillangó csoport fogadó óra - Ivanics Andrea', NULL, NULL, '13:00:00', '14:30:00', 'Kéthetente', 'Szerda', NULL, 'Előre egyeztetett időpontban.'),
(4, 'Fogadó óra', 'Pillangó csoport fogadó óra - Jakab Melinda', NULL, NULL, '13:00:00', '14:30:00', 'Kéthetente', 'Hétfő', NULL, 'Előre egyeztetett időpontban.'),
(5, 'Fogadó óra', 'Pillangó csoport fogadó óra - Pozsgai Andrea', NULL, NULL, '13:00:00', '14:30:00', 'Kéthetente', 'Csütörtök', NULL, 'Előre egyeztetett időpontban.'),
(6, 'Fogadó óra', 'Százszorszép csoport fogadó óra - Schnell Márta', NULL, NULL, '13:00:00', '14:30:00', 'Kéthetente', 'Kedd', NULL, 'Előre egyeztetett időpontban.'),
(7, 'Fogadó óra', 'Százszorszép csoport fogadó óra - Hajzer Kitti', NULL, NULL, '13:00:00', '14:30:00', 'Kéthetente', 'Péntek', NULL, 'Előre egyeztetett időpontban.'),
(8, 'Fogadó óra', 'Százszorszép csoport fogadó óra - Farkas Judit', NULL, NULL, '13:00:00', '14:30:00', 'Kéthetente', 'Szerda', NULL, 'Előre egyeztetett időpontban.'),
(9, 'Fogadó óra', 'Szivárvány csoport fogadó óra - Kraft Gabriella', NULL, NULL, '13:00:00', '14:30:00', 'Kéthetente', 'Hétfő', NULL, 'Előre egyeztetett időpontban.'),
(10, 'Fogadó óra', 'Szivárvány csoport fogadó óra - Leib Rolandné', NULL, NULL, '13:00:00', '14:30:00', 'Kéthetente', 'Csütörtök', NULL, 'Előre egyeztetett időpontban.'),
(11, 'Fogadó óra', 'Szivárvány csoport fogadó óra - Pozsgai – Ivánkovics Nóra', NULL, NULL, '13:00:00', '14:30:00', 'Kéthetente', 'Péntek', NULL, 'Előre egyeztetett időpontban.');

-- --------------------------------------------------------

--
-- Table structure for table `szolgaltatasok`
--

DROP TABLE IF EXISTS `szolgaltatasok`;
CREATE TABLE `szolgaltatasok` (
  `id` int UNSIGNED NOT NULL,
  `nev` varchar(200) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `leiras` text COLLATE utf8mb4_hungarian_ci,
  `idopont` varchar(100) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `kep_url` varchar(255) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `szemelyek` varchar(500) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `aktualis` tinyint(1) NOT NULL DEFAULT '1',
  `sorrend` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `szolgaltatasok`
--

INSERT INTO `szolgaltatasok` (`id`, `nev`, `leiras`, `idopont`, `kep_url`, `szemelyek`, `aktualis`, `sorrend`, `created_at`, `updated_at`) VALUES
(1, 'LOGOPÉDIA, GYÓGYPEDAGÓGIA', 'A logopédia segít a beszédhibák, nyelvi zavarok és kommunikációs nehézségek korrekciójában, támogatva a gyermekek megfelelő nyelvi fejlődését. A gyógypedagógus segíti azon gyermekek fejlődését melyek szakértői vélemények alapján fejlettségükben visszamaradottak, vagy egyes részt képességeik elmaradnak az életkoruktól.', 'Kedd délelőtt, Kedd, Szerda délelőtt', '../photos/logopedia.jpg', 'Huttner Réka, Sütő Anikó', 1, 1, '2025-01-27 10:00:00', '2025-01-27 10:00:00'),
(2, 'HITTAN', 'A hittan órák keretében a gyermekek megismerkedhetnek a keresztény értékekkel és erkölcsi tanításokkal, játékos formában.', 'Kedd délután (14:30 – 14:50)', '../photos/hittan.jpg', 'Szabados Zsófia', 1, 2, '2025-01-27 10:00:00', '2025-01-27 10:00:00'),
(3, 'GYÓGYTESTNEVELÉS', 'A gyógytestnevelés célja a mozgásszervi rendellenességek megelőzése és korrigálása, a testtartás javítása, és az egészséges mozgásfejlődés elősegítése egyéni foglalkozások keretében.', 'Csütörtök', '../photos/gyogytestneveles.jpg', 'Jakab Melinda', 1, 3, '2025-01-27 10:00:00', '2025-01-27 10:00:00'),
(4, 'OVIKÉZI', 'Az ovikézi az előző nevelési évben kezdődött intézményünkben a cél , hogy már az óvodás korú gyermekekkel is megismertessék és megszerettessék a kézilabdát, mint sportágat. A Mohácsi Kézilabda Szakosztálynak köszönhetően tud megvalósulni ez a program.', '...', '../photos/ovikezi.png', 'Papp-Eszterrel', 1, 4, '2025-01-27 10:00:00', '2025-01-27 10:00:00'),
(5, 'Angol tehetséggondozás', 'Jelenleg ez a szolgáltatás nem elérhető. Amennyiben a jövőben indul angol tehetséggondozó program, arról tájékoztatást adunk.', NULL, '../photos/angol_tehetseggondozas.jpg', 'jelenleg nincs', 0, 5, '2025-01-27 10:00:00', '2025-01-27 10:00:00'),
(6, 'ÚSZÓ PROGRAM', 'Az úszóprogram során a gyermekek játékos formában ismerkednek meg a vízzel, fejlesztve mozgáskoordinációjukat és magabiztosságukat.', 'Kedd délelőtt', '../photos/uszas.jpg', 'Leib Rolandné', 1, 6, '2025-01-27 10:00:00', '2025-01-27 10:00:00'),
(7, 'BOZSIK PROGRAM', 'A Bozsik Program a labdarúgás alapjainak játékos elsajátítására fókuszál, fejlesztve a gyermekek fizikai képességeit és csapatszellemét.', 'Szerda délelőtt (9:30 – 11:00)', '../photos/bozsik.jpg', 'Leib Rolandné', 1, 7, '2025-01-27 10:00:00', '2025-01-27 10:00:00'),
(8, 'Sajátos Nevelési Igényű Gyermekek Pedagógiája', 'Két óvodapedagógusunk elvégezte a Sajátos Nevelési Igényű Gyermekek Pedagógiáját, mely által segíthetik az arra rászoruló gyermekek fejlődését.', NULL, '../photos/sni-gyermekek.jpg', 'Leib Rolandné, Jakab Melinda', 1, 8, '2025-01-27 10:00:00', '2025-01-27 10:00:00'),
(9, 'Óvoda', 'A Fenntartóval, a Német Önkormányzatokkal és a Szülői Munkaközösséggel közösen sok színes programmal színesítjük a gyermekek mindennapjait (bábszínház, állatbemutatók, gyereknapok, kirándulások stb.).', NULL, '../photos/egyeb-program.jpg', NULL, 1, 9, '2025-01-27 10:00:00', '2025-01-27 10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `napirend`
--

DROP TABLE IF EXISTS `napirend`;
CREATE TABLE `napirend` (
  `napirend_id` int NOT NULL,
  `aktivitas_nev` varchar(255) NOT NULL,
  `kezdet_ido` time NOT NULL,
  `veg_ido` time DEFAULT NULL,
  `tipus` varchar(50) NOT NULL,
  `reszletes_leiras` text,
  `napi_ritmus_sorrend` int NOT NULL,
  `megjegyzes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `napirend`
--

INSERT INTO `napirend` (`napirend_id`, `aktivitas_nev`, `kezdet_ido`, `veg_ido`, `tipus`, `reszletes_leiras`, `napi_ritmus_sorrend`, `megjegyzes`) VALUES
(1, 'Óvoda nyitva tartása', '06:30:00', '16:30:00', 'Fix időpont', 'Hétfőtől-péntekig', 0, NULL),
(2, 'Gyermekek érkezése', '06:30:00', '08:30:00', 'Időintervallum', 'Gyermekek fogadása, szabad játék, egyéni gondozás.', 10, NULL),
(3, 'Tízórai', '08:00:00', NULL, 'Étkezés', NULL, 20, 'Folyamatos tízóraiztatás'),
(4, 'Aktuális téma feldolgozása', '09:00:00', NULL, 'Játék', 'irányított és szabad játék a csoportszobában.', 30, NULL),
(5, 'Játék a szabadban', '10:30:00', NULL, 'Játék', 'udvari mozgás és felfedezés.', 40, NULL),
(6, 'Ebéd', '11:45:00', NULL, 'Étkezés', 'Tisztálkodási teendők, ebéd.', 50, NULL),
(7, 'Felkészülés a pihenésre', '12:15:00', NULL, 'Pihenés', 'Tisztálkodási teendők, mesehallgatás.', 60, NULL),
(8, 'Délutáni alvás/pihenés', '12:30:00', NULL, 'Pihenés', NULL, 70, NULL),
(9, 'Ébresztés', '14:30:00', NULL, 'Pihenés', 'A gyermekek ébresztése, felkelés.', 80, NULL),
(10, 'Uzsonna', '14:45:00', NULL, 'Étkezés', NULL, 90, NULL),
(11, 'Gyermekek hazavitele', '15:00:00', '16:30:00', 'Időintervallum', 'szabad játék a csoportban, jó idő esetén az udvaron.', 100, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `personnel`
--

DROP TABLE IF EXISTS `personnel`;
CREATE TABLE `personnel` (
  `id` int NOT NULL,
  `role` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `order_number` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `personnel`
--

INSERT INTO `personnel` (`id`, `role`, `name`, `is_deleted`, `order_number`) VALUES
(1, 'Élelmezésvezető', 'Kismándor Csaba', 0, 1),
(2, 'Szakács', 'Pfaff Petra', 0, 2),
(3, 'Konyhai kisegítő', 'Verner Józsefné', 0, 3),
(4, 'Konyhai kisegítő', 'Szabó Adrienn', 0, 4),
(5, 'Konyhai kisegítő', 'Vargyas Ágnes', 0, 5);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `slug` varchar(150) COLLATE utf8mb4_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`) VALUES
(1, 'Német nemzetiségi óvodapedagógusok', 'nemet-ovodapedagogusok'),
(2, 'Óvodapedagógusok', 'ovodapedagogusok'),
(3, 'Pedagógiai asszisztens', 'pedagogiai-asszisztens'),
(4, 'Dajkák', 'dajkak'),
(5, 'Konyhai kisegítő', 'konyhai-kisegito'),
(6, 'Családi bölcsődei szolgáltatást nyújtó személyek', 'csaladi-bolcsode');

-- --------------------------------------------------------

--
-- Table structure for table `staff_category`
--

DROP TABLE IF EXISTS `staff_category`;
CREATE TABLE `staff_category` (
  `id` int UNSIGNED NOT NULL,
  `slug` varchar(64) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `description` text COLLATE utf8mb4_hungarian_ci,
  `display_order` int UNSIGNED NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `staff_category`
--

INSERT INTO `staff_category` (`id`, `slug`, `name`, `description`, `display_order`, `active`, `created_at`, `updated_at`) VALUES
(1, 'ovonok', 'Óvónőink', NULL, 1, 1, '2025-09-18 20:14:37', '2025-09-18 20:14:37'),
(2, 'dajkak', 'Dajkák', NULL, 2, 1, '2025-09-18 20:14:37', '2025-09-18 20:14:37'),
(3, 'asszisztensek', 'Pedagógiai Asszisztensek', NULL, 3, 1, '2025-09-18 20:14:37', '2025-09-18 20:14:37'),
(4, 'bolcsode', 'Bölcsődei Munkatársaink', NULL, 4, 1, '2025-09-18 20:14:37', '2025-09-18 20:14:37');

-- --------------------------------------------------------

--
-- Table structure for table `staff_member`
--

DROP TABLE IF EXISTS `staff_member`;
CREATE TABLE `staff_member` (
  `id` int UNSIGNED NOT NULL,
  `category_id` int UNSIGNED NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `role_title` varchar(150) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `bio` text COLLATE utf8mb4_hungarian_ci,
  `photo_url` varchar(255) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `csoport_id` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `staff_member`
--

INSERT INTO `staff_member` (`id`, `category_id`, `name`, `role_title`, `bio`, `photo_url`, `email`, `phone`, `is_featured`, `sort_order`, `active`, `created_at`, `updated_at`, `csoport_id`) VALUES
(14, 1, 'Leib Rolandné', 'Óvodavezető, Óvónő', '2010-től dolgozom óvónőként, 2020-tól pedig igazgatóként Himesházán. Célom, hogy óvodánk arculatát folyamatosan bővítsem új programokkal, kollégáimnak biztosítsam a fejlődésükhöz szükséges feltételeket. Erősségeim a sport tevékenységek: Úszó Program, Bozsik Program. Terveim közt szerepel az Ovikézi megvalósítása. Elvégeztem egy SNI-s módszertanos továbbképzést, illetve részt vettem a Diamentor Programban, melynek köszönhetően 1-es típusú diabéteszes gyermekeket láthatok el. Jelenleg is végzem a német nemzetiségi óvodapedagógus tanulmányaimat. Egy gyermekközpontú, családias, jól együttműködő, stabil kapcsolatrendszereken alapuló óvoda irányítása a legfontosabb feladatom.', 'Leib.png', NULL, NULL, 0, 1, 1, '2025-09-19 07:13:13', '2025-09-19 07:13:13', 3),
(15, 1, 'Jakab Melinda', 'Német nemzetiségi óvodapedagógus, igazgatóhelyettes', 'Jakab Melindának hívnak, himesházi lakos vagyok. 2019-ben érkeztem ebbe az óvodába, mint pedagógiai asszisztens. 2021-ben szereztem meg a diplomámat, azóta német nemzetiségi óvodapedagógusként dolgozom. A 2023/2024-es nevelési évtől kezdve az intézmény igazgatóhelyettese vagyok. Munkám során fontosnak tartom az egészséges életmód kialakítását, az integrált nevelés megvalósítását. Az elmúlt három évben több továbbképzésen is részt vettem: elsajátítottam a gyógytestnevelés módszertanát, bepillantást nyertem a sajátos nevelési igényű gyermekekkel való foglalkozásba, és részt vettem a Bozsik tanfolyamon is. Szeretném, ha a mindennapokban a gyermekek egy biztonságos, családias és szeretetteljes közegben, életkorukhoz mérten a legmegfelelőbb nevelésben és fejlesztésben részesülnének, illetve, ha sok új élménnyel és pozitív emlékkel gazdagodnának.', 'Jakab.png', NULL, NULL, 0, 2, 1, '2025-09-19 07:13:13', '2025-09-27 07:49:30', 1),
(16, 1, 'Ivanics Andrea', 'Német nemzetiségi óvónő', 'Ivanics Andreának hívnak és Erdősmárokon élek. 1996 óta óvónőként dolgozom, ebből 2010 óta vagyok ebben az óvodában, mint német nemzetiségi óvónő. Szívesen barkácsolok a gyermekekkel, az ének, tánc és a természetben való mozgás is közel áll hozzám. Ezen területeket összefoglalva, kolléganőmmel közösen tehetséggondozást végzünk óvodánkban.', 'Ivanics.png', NULL, NULL, 0, 3, 1, '2025-09-19 07:13:13', '2025-09-27 07:49:30', 1),
(17, 1, 'Schnell Márta', 'Óvónő', 'Schnell Mártának hívnak, Himesházán élek a családommal. Német nemzetiségi óvodapedagógusi végzettségemet 2008-ban szereztem Baján, az Eötvös József Főiskolán. 2020 decemberétől dolgozom az intézményben. Célom a gyermekekkel megszerettetni a német nyelvet, valamint fontosnak tartom a néphagyományok ápolását és a sváb táncok megismertetését. Arra törekszem, hogy mindenre nyitott, érdeklődő és talpraesett gyerekeket neveljek, akik megállják a helyüket az iskolában és az élet minden területén.', 'schnell.png', NULL, NULL, 0, 4, 1, '2025-09-19 07:13:13', '2025-09-27 07:49:30', 2),
(18, 1, 'Hajzer Kitti', 'Óvónő', 'Hajzer Kittinek hívnak és Mohácson élek. 2024-ben végeztem a Pécsi Tudományegyetem Kultúratudományi, Pedagógusképző és Vidékfejlesztési Karán, mint óvodapedagógus. Számomra legközelebb a vizuális és az ének-zenei foglalkozások állnak. Jövőbeli terveim közt szerepel olyan továbbképzéseken való részvétel, melyek során betekintést nyerhetek az integrált nevelés módszertanába, illetve ezzel segítem az arra rászoruló gyermekek fejlődését.', 'Hajzer.png', NULL, NULL, 0, 5, 1, '2025-09-19 07:13:13', '2025-09-27 07:49:30', 2),
(19, 1, 'Kraft Gabriella', 'Német nemzetiségi óvónő', 'Kraft Gabriella vagyok, Himesházán élek, 3 gyermekem és 2 unokám is van. 7 éve dolgozom a Himesházi Óvoda, Családi Bölcsőde és Konyhán. Az első 4 évben, mint pedagógiai asszisztens voltam foglalkoztatva, ez idő alatt elvégeztem Baján a német nemzetiségi óvónői szakot, ahol 2022. júniusában szereztem meg a diplomámat. Ettől kezdve az óvodában, mint német nemzetiségi óvónő dolgozom. Munkámban fontosnak vélem, hogy a gyermekek jól érezzék magukat az intézményünkben és, mint egy nagy család működjünk együtt. Az egymás iránti tiszteletre, figyelemre, az együttélés szabályaira és a gyermekek önállóságára való törekvését fontos szempontoknak tekintem a mindennapi munkám során. Hangsúlyt fektetek a gyermekek német nyelvtudásának fejlesztésére, és arra, hogy megértsék a német nyelven elhangzó instrukciókat. Én még ismerem és beszélem a helyi nyelvjárást, így ezzel a tudással is tudom gazdagítani a közös munkánkat.', 'Kraft.png', NULL, NULL, 0, 6, 1, '2025-09-19 07:13:13', '2025-09-27 07:49:30', 3),
(20, 1, 'Bartosné Krizák Mónika', 'Óvónő', 'Bartosné Krizák Mónikának hívnak. Marázán nőttem fel, majd 2011-ben Ausztriába költöztünk a férjemmel, ahol 11 évig éltünk, emiatt is közel áll hozzám a német nyelv. Kislányom születése után 2021-ben vissza költöztünk Marázára. Azóta dolgozom itt óvónőként, kislányommal itt kezdtük együtt az óvodát. Szívesen énekelek, táncolok, sok zenét hallgatok és barkácsolok a csoportban a gyermekekkel. Kolléganőmmel, Andival közösen tehetséggondozást tartunk az óvodában.', 'Krizak.png', NULL, NULL, 0, 7, 1, '2025-09-19 07:13:13', '2025-09-27 07:49:30', NULL),
(21, 2, 'Farkas Judit', 'Dajka', 'Farkas Judit', 'Judit.png', NULL, NULL, 0, 8, 1, '2025-09-19 07:13:13', '2025-09-19 07:13:13', 2),
(22, 2, 'Pozsgai Andrea', 'Dajka', 'Pozsgai Andrea', 'Pozsgai.png', NULL, NULL, 0, 9, 1, '2025-09-19 07:13:13', '2025-09-19 07:13:13', 1),
(23, 2, 'Pozsgai – Ivánkovics Nóra', 'Dajka', 'Pozsgai – Ivánkovics Nóra', 'nora.png', NULL, NULL, 0, 10, 1, '2025-09-19 07:13:13', '2025-09-19 07:13:13', 3),
(24, 3, 'Katona Beatrix', 'Pedagógiai asszisztens', 'Katona Beatrixnak hívnak; 2017 óta a családommal Szűrben élek és van egy 5 éves kisfiam. 2005-ben végeztem a Comenius Szakközépiskolában Pécsett, mint gyermek és ifjúságvédelmi asszisztens, gyermekfelügyelő. 2020-ban gyógypedagógiai asszisztensként végeztem, 2022 óta pedig ebben az intézményben vagyok pedagógiai asszisztens. Főbb feladataim: óvodapedagógus társam segítése a mindennapokban, nevelési feladatok ellátása. Mellette próbálom a nehezebben kezelhető gyermekek feladattudatát gyógypedikiai végzettségemnek köszönhetően erősíteni. Mindezek mellett részt veszek a dajkák napi teendőinek elvégzésében, ha szükséges. Fontos számomra, hogy a gyerekek jól érezzék magukat mellettünk a mindennapokban.', 'Beatrix.png', NULL, NULL, 0, 11, 1, '2025-09-19 07:13:13', '2025-09-19 07:13:13', NULL),
(25, 4, 'Szegedi-Csetót Emese', 'Családi bölcsődei szolgáltatást nyújtó személy', 'Emese kreatív játékokkal és fejlesztő tevékenységekkel segíti a bölcsődés korú gyermekek fejlődését, mindig mosollyal az arcán.', 'csetot.png', NULL, NULL, 0, 12, 1, '2025-09-19 07:13:13', '2025-09-19 07:13:13', NULL),
(26, 4, 'Tamásné Stang Erika', 'Családi bölcsődei szolgáltatást nyújtó személy', 'Tamásné Erika a legkisebbekre figyel a bölcsődében, gondoskodó és odaadó munkájával biztonságot nyújt a piciknek.', 'Stang.png', NULL, NULL, 0, 13, 1, '2025-09-19 07:13:13', '2025-09-19 07:13:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `uzenetek`
--

DROP TABLE IF EXISTS `uzenetek`;
CREATE TABLE `uzenetek` (
  `uzenet_id` int NOT NULL,
  `nev` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `targy` varchar(255) DEFAULT NULL,
  `uzenet` text,
  `kuldes_datum` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `olvasva` tinyint(1) NOT NULL DEFAULT '0',
  `ip_cim` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `uzenetek`
--

INSERT INTO `uzenetek` (`uzenet_id`, `nev`, `email`, `targy`, `uzenet`, `kuldes_datum`, `olvasva`, `ip_cim`) VALUES
(5, 'Hajas Nana', 'hajasNana@gmail.com', 'Önéletrajz', '', '2025-07-30 12:18:52', 0, '::1'),
(6, 'Hajas Nana', 'hajasNana@gmail.com', 'ujra', '', '2025-07-30 12:23:39', 0, '::1'),
(7, 'Leib Jázmin', 'leibjazmin20@gmail.com', 'Önéletrajz', '', '2025-07-30 12:26:40', 0, '::1'),
(8, 'Leib Bea', 'leibbea81@gmail.com', 'Játék?', 'Minden gyerek mosson kezet!', '2025-07-30 12:33:54', 0, '::1'),
(9, 'bethlen gábor', 'bethlengabor@gmail.com', 'Játék?', 'asdwagesdawd', '2025-07-30 12:37:30', 0, '::1'),
(10, 'APA CUKA', 'fundaluka@gmail.com', 'adawd', 'awdawd', '2025-07-30 12:39:11', 0, '::1'),
(11, 'Kis János', 'kiss@gmail.com', 'Önéletrajz', 'Szeretném beadni az önéletrajzomat.', '2025-07-30 16:09:22', 0, '::1'),
(12, 'Timár Dávid', 'timar.david1974@gmail.com', 'Weboldal', 'Egy olyan kérdésem lenne, hogy a dokumentumokhoz megvan-e esetleg már a többi adat, + a napi hírek oldalt szeretnéd-e még + ha vannak videók meg képek akkor küld el őket és akkor berakom őket.', '2025-08-04 12:59:14', 0, '::1');

-- --------------------------------------------------------

--
-- Table structure for table `videok`
--

DROP TABLE IF EXISTS `videok`;
CREATE TABLE `videok` (
  `video_id` int NOT NULL,
  `cim` varchar(255) NOT NULL,
  `leiras` text,
  `youtube_link` varchar(255) DEFAULT NULL,
  `feltoltes_datum` datetime DEFAULT CURRENT_TIMESTAMP,
  `esemeny_tipus` varchar(100) DEFAULT NULL,
  `kapcsolodo_esemeny_id` int DEFAULT NULL,
  `lathato_a_weblapon` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `annual_program_items`
--
ALTER TABLE `annual_program_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_year_month` (`school_year`,`month`,`sort_order`,`starts_on`),
  ADD KEY `idx_year_section` (`school_year`,`section_title`);

--
-- Indexes for table `csoportok`
--
ALTER TABLE `csoportok`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_csoport_nev` (`nev`);

--
-- Indexes for table `enrollment_info`
--
ALTER TABLE `enrollment_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `school_year` (`school_year`);

--
-- Indexes for table `fotok`
--
ALTER TABLE `fotok`
  ADD PRIMARY KEY (`foto_id`),
  ADD UNIQUE KEY `fajlnev` (`fajlnev`);

--
-- Indexes for table `hirek`
--
ALTER TABLE `hirek`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kapcsolattartas`
--
ALTER TABLE `kapcsolattartas`
  ADD PRIMARY KEY (`kapcsolattartas_id`);

--
-- Indexes for table `szolgaltatasok`
--
ALTER TABLE `szolgaltatasok`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_aktualis_sorrend` (`aktualis`,`sorrend`);

--
-- Indexes for table `napirend`
--
ALTER TABLE `napirend`
  ADD PRIMARY KEY (`napirend_id`);

--
-- Indexes for table `personnel`
--
ALTER TABLE `personnel`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `staff_category`
--
ALTER TABLE `staff_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `staff_member`
--
ALTER TABLE `staff_member`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_staff_category` (`category_id`,`active`,`sort_order`),
  ADD KEY `idx_staff_active` (`active`),
  ADD KEY `fk_staff_member_csoport` (`csoport_id`);

--
-- Indexes for table `uzenetek`
--
ALTER TABLE `uzenetek`
  ADD PRIMARY KEY (`uzenet_id`);

--
-- Indexes for table `videok`
--
ALTER TABLE `videok`
  ADD PRIMARY KEY (`video_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `annual_program_items`
--
ALTER TABLE `annual_program_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `csoportok`
--
ALTER TABLE `csoportok`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `enrollment_info`
--
ALTER TABLE `enrollment_info`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fotok`
--
ALTER TABLE `fotok`
  MODIFY `foto_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hirek`
--
ALTER TABLE `hirek`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kapcsolattartas`
--
ALTER TABLE `kapcsolattartas`
  MODIFY `kapcsolattartas_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `szolgaltatasok`
--
ALTER TABLE `szolgaltatasok`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `napirend`
--
ALTER TABLE `napirend`
  MODIFY `napirend_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `personnel`
--
ALTER TABLE `personnel`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `staff_category`
--
ALTER TABLE `staff_category`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `staff_member`
--
ALTER TABLE `staff_member`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `uzenetek`
--
ALTER TABLE `uzenetek`
  MODIFY `uzenet_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `videok`
--
ALTER TABLE `videok`
  MODIFY `video_id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `staff_member`
--
ALTER TABLE `staff_member`
  ADD CONSTRAINT `fk_staff_member_category` FOREIGN KEY (`category_id`) REFERENCES `staff_category` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_staff_member_csoport` FOREIGN KEY (`csoport_id`) REFERENCES `csoportok` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
