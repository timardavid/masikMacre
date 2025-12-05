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
-- Database: `jdw_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text,
  `icon` varchar(50) DEFAULT NULL,
  `display_order` int DEFAULT '0',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `display_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Landing Page', 'landing-page', 'Egyoldalas bemutatkozó oldalak', NULL, 1, 'active', '2025-10-18 08:48:20', '2025-10-18 08:48:20'),
(2, 'OnePage', 'onepage', 'OnePage weboldalak kódolva', NULL, 2, 'active', '2025-10-18 08:48:20', '2025-10-18 08:48:20'),
(3, 'Portfólió', 'portfolio', 'Portfólió és bemutatkozó weboldalak', NULL, 3, 'active', '2025-10-18 08:48:20', '2025-10-18 08:48:20'),
(4, 'Vállalati', 'vallalati', 'Vállalati és céges weboldalak', NULL, 4, 'active', '2025-10-18 08:48:20', '2025-10-18 08:48:20'),
(5, 'Blog', 'blog', 'Blog és tartalomközpontú oldalak', NULL, 5, 'active', '2025-10-18 08:48:20', '2025-10-18 08:48:20');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied','archived') DEFAULT 'new',
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

DROP TABLE IF EXISTS `faq`;
CREATE TABLE `faq` (
  `id` int NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `display_order` int DEFAULT '0',
  `status` enum('active','inactive') DEFAULT 'active',
  `views` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscribers`
--

DROP TABLE IF EXISTS `newsletter_subscribers`;
CREATE TABLE `newsletter_subscribers` (
  `id` int NOT NULL,
  `email` varchar(100) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `status` enum('active','unsubscribed') DEFAULT 'active',
  `verification_token` varchar(100) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT '0',
  `subscribed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `unsubscribed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `user_id` int DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'HUF',
  `payment_method` enum('card','transfer','paypal','cash') DEFAULT 'transfer',
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `order_status` enum('new','processing','completed','cancelled') DEFAULT 'new',
  `billing_address` text,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `product_name` varchar(200) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int DEFAULT '1',
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `portfolio`
--

DROP TABLE IF EXISTS `portfolio`;
CREATE TABLE `portfolio` (
  `id` int NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `category_id` int DEFAULT NULL,
  `client_name` varchar(100) DEFAULT NULL,
  `description` text,
  `project_url` varchar(255) DEFAULT NULL,
  `thumbnail_image` varchar(255) DEFAULT NULL,
  `gallery_images` text,
  `technologies` text,
  `completion_date` date DEFAULT NULL,
  `display_order` int DEFAULT '0',
  `is_featured` tinyint(1) DEFAULT '0',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `portfolio`
--

INSERT INTO `portfolio` (`id`, `title`, `slug`, `category_id`, `client_name`, `description`, `project_url`, `thumbnail_image`, `gallery_images`, `technologies`, `completion_date`, `display_order`, `is_featured`, `status`, `created_at`, `updated_at`) VALUES
(1, 'OnEvent Rendezvényszervező', 'onevent', 4, 'OnEvent', 'Professzionális rendezvényszervező weboldal Figmában tervezve. Modern, letisztult dizájn amely tökéletesen mutatja be a rendezvényszervező szolgáltatásait.', 'https://onevent.hu/', 'assets/photos/OnEvent.png', '[\"assets/photos/OnEvent.png\"]', '[\"Figma\", \"UI/UX Design\", \"Branding\"]', NULL, 1, 1, 'active', '2025-10-18 08:48:32', '2025-10-18 08:48:32'),
(2, 'Borsóház Pécs', 'borsohaz-pecs', 4, 'Borsóház', 'Modern vállalati weboldal design Figmában tervezve. Reszponzív, gyors és felhasználóbarát megoldás.', 'https://new.borsohazpecs.hu', 'assets/photos/borsohaz.png', '[\"assets/photos/borsohaz.png\"]', '[\"Figma\", \"UI/UX Design\", \"Responsive Design\"]', NULL, 2, 1, 'active', '2025-10-18 08:48:32', '2025-10-18 08:48:32'),
(3, 'Veréb Gépészet', 'vereb-gepeszet', 4, 'Veréb Gépészet Kft.', 'Professzionális gépészeti vállalkozás bemutatkozó oldal design Figmában. Reszponzív, modern megjelenés.', 'https://verebgepesz.hu', 'assets/photos/verebgepesz.png', '[\"assets/photos/verebgepesz.png\"]', '[\"Figma\", \"UI/UX Design\", \"Branding\"]', NULL, 3, 1, 'active', '2025-10-18 08:48:32', '2025-10-18 08:48:32');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `category_id` int DEFAULT NULL,
  `description` text,
  `detailed_description` longtext,
  `price` decimal(10,2) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT 'HUF',
  `features` text,
  `demo_url` varchar(255) DEFAULT NULL,
  `preview_image` varchar(255) DEFAULT NULL,
  `gallery_images` text,
  `downloads` int DEFAULT '0',
  `views` int DEFAULT '0',
  `rating` decimal(3,2) DEFAULT '0.00',
  `reviews_count` int DEFAULT '0',
  `is_featured` tinyint(1) DEFAULT '0',
  `is_bestseller` tinyint(1) DEFAULT '0',
  `status` enum('active','inactive','coming_soon') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `category_id`, `description`, `detailed_description`, `price`, `old_price`, `currency`, `features`, `demo_url`, `preview_image`, `gallery_images`, `downloads`, `views`, `rating`, `reviews_count`, `is_featured`, `is_bestseller`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Kezdő Csomag', 'kezdo-csomag', 1, 'Tökéletes kisvállalkozásoknak és induló vállalkozásoknak', 'A Kezdő csomag ideális választás azoknak, akik most indítanak vállalkozást vagy egyszerű online jelenlétre van szükségük. Egy professzionálisan megtervezett egyoldalas weboldal designnal hatékonyan tudod bemutatni vállalkozásodat.', 149000.00, NULL, 'HUF', '[\"1 oldalas modern weboldal design (Figma)\", \"Teljesen reszponzív dizájn (mobil, tablet, desktop)\", \"Professzionális kapcsolati űrlap design\", \"Képgaléria / Portfólió szekció design\", \"Hírlevél feliratkozás funkció design\", \"Egyedi grafikai elemek és ikonok\", \"Design rendszer és style guide\", \"Interaktív Figma prototípus\", \"Kódolásra kész export\", \"Design dokumentáció\", \"3 hónap ingyenes design támogatás\", \"2 felülvizsgálati kör a tervezés során\", \"+Opcionális: HTML/CSS/JS kódolt változat\"]', NULL, NULL, NULL, 0, 0, 0.00, 0, 0, 0, 'active', '2025-10-18 08:48:32', '2025-10-18 08:48:32'),
(2, 'Professzionális Csomag', 'professzionalis-csomag', 4, 'A legtöbb vállalkozás választása - teljes körű design megoldás', 'A Professzionális csomag komplett weboldal design megoldás középvállalkozásoknak, akik komolyabb online jelenlétre vágynak. Több aloldal design, admin panel design és blog funkció designnal bővítheted vállalkozásod kommunikációját.', 299000.00, NULL, 'HUF', '[\"Korlátlan számú aloldal design (Figma)\", \"Teljesen reszponzív és modern dizájn\", \"Egyedi grafikai elemek és ikonok\", \"Adminisztrációs felület design (CMS)\", \"Blog rendszer design cikk publikáláshoz\", \"Kapcsolati űrlap design email értesítéssel\", \"Hírlevél feliratkozás funkció design\", \"Haladó design elemek\", \"Social media integráció design\", \"Képgaléria / Portfólió szekció design\", \"GYIK (Gyakran Ismételt Kérdések) oldal design\", \"6 hónap ingyenes design támogatás\", \"3 felülvizsgálati kör a tervezés során\"]', NULL, NULL, NULL, 0, 0, 0.00, 0, 1, 0, 'active', '2025-10-18 08:48:32', '2025-10-18 08:48:32'),
(3, 'Prémium Csomag', 'premium-csomag', 4, 'Teljes körű prémium design megoldás egyedi igényekre', 'A Prémium csomag a legtöbbet nyújtó design megoldás azoknak, akik komplex funkciókat, korlátlan számú oldalt és prémium design szolgáltatásokat igényelnek. Egyedi modulokkal és prémium design támogatással.', 549000.00, NULL, 'HUF', '[\"Korlátlan számú aloldal design (Figma)\", \"Prémium egyedi dizájn\", \"Ügyfél fiók és bejelentkezés rendszer design\", \"Teljes admin panel design minden funkcióval\", \"Blog és hírlevél rendszer design\", \"Professzionális design audit és optimalizálás\", \"Haladó design elemek és animációk\", \"Biztonsági és adatvédelmi oldal design\", \"Social media teljes integráció design\", \"Többnyelvűség opció design\", \"Egyedi funkciók és modulok design\", \"Cookie bar és adatvédelmi oldal design\", \"12 hónap prémium design támogatás\", \"Korlátlan felülvizsgálati kör\"]', NULL, NULL, NULL, 0, 0, 0.00, 0, 0, 0, 'active', '2025-10-18 08:48:32', '2025-10-18 08:48:32');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `rating` int NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `comment` text,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `setting_type` varchar(50) DEFAULT 'text',
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `updated_at`) VALUES
(1, 'site_name', 'JDW', 'text', 'Weboldal neve', '2025-10-18 08:48:20'),
(2, 'site_email', 'info@jdw.hu', 'email', 'Kapcsolati email cím', '2025-10-18 08:48:20'),
(3, 'site_phone', '+36 XX XXX XXXX', 'text', 'Telefonszám', '2025-10-18 08:48:20'),
(4, 'currency', 'HUF', 'text', 'Alapértelmezett pénznem', '2025-10-18 08:48:20'),
(5, 'tax_rate', '27', 'number', 'ÁFA százalék', '2025-10-18 08:48:20'),
(6, 'maintenance_mode', '0', 'boolean', 'Karbantartási mód', '2025-10-18 08:48:20'),
(7, 'projects_completed', '0', 'number', 'Elkészült projektek száma', '2025-10-18 08:48:32'),
(8, 'happy_clients', '0', 'number', 'Elégedett ügyfelek száma', '2025-10-18 08:48:32'),
(9, 'years_experience', '0', 'number', 'Évek tapasztalat', '2025-10-18 08:48:32'),
(10, 'custom_solutions', '100', 'number', 'Egyedi megoldások százaléka', '2025-10-18 08:48:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `phone`, `created_at`, `updated_at`, `last_login`, `status`) VALUES
(1, 'admin', 'admin@jdw.hu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Adminisztrátor', 'admin', NULL, '2025-10-18 08:48:20', '2025-10-18 08:48:20', NULL, 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `idx_order_number` (`order_number`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`order_status`),
  ADD KEY `idx_payment_status` (`payment_status`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_product` (`product_id`);

--
-- Indexes for table `portfolio`
--
ALTER TABLE `portfolio`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_featured` (`is_featured`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_featured` (`is_featured`),
  ADD KEY `idx_price` (`price`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_rating` (`rating`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `portfolio`
--
ALTER TABLE `portfolio`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `portfolio`
--
ALTER TABLE `portfolio`
  ADD CONSTRAINT `portfolio_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
