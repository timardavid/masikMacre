-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Dec 05, 2025 at 11:21 AM
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
-- Database: `tdwebdesign`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `icon` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `icon`, `created_at`) VALUES
(1, 'Corporate Website', 'Professzionális vállalati weboldalak', 'fas fa-building', '2025-10-06 14:02:05'),
(2, 'E-commerce', 'Online áruházak és webshopok', 'fas fa-shopping-cart', '2025-10-06 14:02:05'),
(3, 'Portfolio', 'Személyes és szakmai portfólió oldalak', 'fas fa-user', '2025-10-06 14:02:05'),
(4, 'Landing Page', 'Marketing célú landing page-ek', 'fas fa-rocket', '2025-10-06 14:02:05'),
(5, 'Blog', 'Blog és tartalomkezelő rendszerek', 'fas fa-blog', '2025-10-06 14:02:05'),
(6, 'Custom Design', 'Egyedi, személyre szabott weboldalak', 'fas fa-palette', '2025-10-06 14:02:05');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied') DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `webdesign_id` int DEFAULT NULL,
  `order_number` varchar(50) NOT NULL,
  `status` enum('pending','confirmed','in_progress','completed','cancelled') DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL,
  `special_requirements` text,
  `delivery_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_images`
--

DROP TABLE IF EXISTS `portfolio_images`;
CREATE TABLE `portfolio_images` (
  `id` int NOT NULL,
  `webdesign_id` int DEFAULT NULL,
  `image_url` varchar(500) NOT NULL,
  `alt_text` varchar(200) DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `webdesign_id` int DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `comment` text,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password_hash`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@tdwebdesign.hu', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-10-06 14:02:05', '2025-10-06 14:02:05'),
(2, 'Timár Dávid', 'timardavid1974@gmail.com', '06208038081', '$2y$10$ci6jwSFap4jmFuehVni9bOe08lspVbef3CmEYSxngO4kRl00Y6mhW', 'customer', '2025-10-06 14:10:33', '2025-10-06 14:10:33');

-- --------------------------------------------------------

--
-- Table structure for table `webdesigns`
--

DROP TABLE IF EXISTS `webdesigns`;
CREATE TABLE `webdesigns` (
  `id` int NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `category_id` int DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `features` json DEFAULT NULL,
  `delivery_time` int DEFAULT '7',
  `status` enum('active','inactive','sold') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `webdesigns`
--

INSERT INTO `webdesigns` (`id`, `title`, `description`, `category_id`, `price`, `image_url`, `features`, `delivery_time`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Modern Corporate Website', 'Professzionális vállalati weboldal modern designnal és teljes funkcionalitással', 1, 150000.00, '/assets/images/corporate1.jpg', '[\"Responsive Design\", \"CMS Integration\", \"SEO Optimization\", \"Contact Forms\", \"Analytics\"]', 14, 'active', '2025-10-06 14:02:05', '2025-10-06 14:02:05'),
(2, 'E-commerce Store', 'Teljes funkcionalitású online áruház PayPal és bankkártyás fizetéssel', 2, 250000.00, '/assets/images/ecommerce1.jpg', '[\"Product Catalog\", \"Shopping Cart\", \"Payment Gateway\", \"Order Management\", \"Inventory System\"]', 21, 'active', '2025-10-06 14:02:05', '2025-10-06 14:02:05'),
(3, 'Creative Portfolio', 'Egyedi portfólió oldal kreatív szakmák számára', 3, 120000.00, '/assets/images/portfolio1.jpg', '[\"Gallery System\", \"Project Showcase\", \"Contact Integration\", \"Social Media Links\", \"Custom Animations\"]', 10, 'active', '2025-10-06 14:02:05', '2025-10-06 14:02:05'),
(4, 'Marketing Landing Page', 'Magas konverziós arányú landing page marketing kampányokhoz', 4, 80000.00, '/assets/images/landing1.jpg', '[\"A/B Testing\", \"Lead Capture\", \"Analytics Integration\", \"Mobile Optimized\", \"Fast Loading\"]', 7, 'active', '2025-10-06 14:02:05', '2025-10-06 14:02:05'),
(5, 'Professional Blog', 'Tartalomkezelő blog rendszer SEO optimalizálással', 5, 100000.00, '/assets/images/blog1.jpg', '[\"Content Management\", \"SEO Tools\", \"Comment System\", \"Social Sharing\", \"Newsletter Integration\"]', 12, 'active', '2025-10-06 14:02:05', '2025-10-06 14:02:05'),
(6, 'Custom Business Site', 'Teljesen egyedi, személyre szabott üzleti weboldal', 6, 200000.00, '/assets/images/custom1.jpg', '[\"Unique Design\", \"Custom Features\", \"Advanced Functionality\", \"Brand Integration\", \"Performance Optimization\"]', 18, 'active', '2025-10-06 14:02:05', '2025-10-06 14:02:05'),
(7, 'Startup Landing Page', 'Modern, konverziós landing page startup vállalkozások számára. Responsive design, gyors betöltés és A/B tesztelési lehetőségek.', 4, 95000.00, '/assets/images/startup-landing.jpg', '[\"Responsive Design\", \"A/B Testing\", \"Lead Capture\", \"Analytics\", \"Fast Loading\", \"SEO Optimized\"]', 10, 'active', '2025-10-06 14:13:27', '2025-10-06 14:13:27'),
(8, 'Restaurant Website', 'Szép étterem weboldal étlap kezeléssel, online foglalási rendszerrel és képek galériával. Mobilbarát és gyors.', 1, 180000.00, '/assets/images/restaurant.jpg', '[\"Online Menu\", \"Reservation System\", \"Photo Gallery\", \"Contact Forms\", \"Social Media Integration\", \"Mobile Optimized\"]', 16, 'active', '2025-10-06 14:13:27', '2025-10-06 14:13:27'),
(9, 'Fitness Studio Website', 'Edzőterem weboldal órarend kezeléssel, edző profilokkal és online jelentkezési rendszerrel.', 1, 160000.00, '/assets/images/fitness.jpg', '[\"Class Schedule\", \"Trainer Profiles\", \"Online Booking\", \"Membership Plans\", \"Progress Tracking\", \"Payment Integration\"]', 14, 'active', '2025-10-06 14:13:27', '2025-10-06 14:13:27'),
(10, 'Online Course Platform', 'E-learning platform kurzusokkal, videó lejátszással, feladatokkal és tanulói követéssel.', 2, 350000.00, '/assets/images/e-learning.jpg', '[\"Video Streaming\", \"Course Management\", \"Student Dashboard\", \"Progress Tracking\", \"Payment Gateway\", \"Certificate System\"]', 25, 'active', '2025-10-06 14:13:27', '2025-10-06 14:13:27'),
(11, 'Photography Portfolio', 'Fotós portfólió oldal képek galériával, kategóriákkal és kapcsolatfelvételi formmal.', 3, 110000.00, '/assets/images/photography.jpg', '[\"Photo Gallery\", \"Category Filtering\", \"Lightbox View\", \"Contact Forms\", \"Social Sharing\", \"Blog Integration\"]', 12, 'active', '2025-10-06 14:13:27', '2025-10-06 14:13:27'),
(12, 'Real Estate Website', 'Ingatlan weboldal ingatlan kereséssel, térképpel, virtuális túrával és kapcsolatfelvételi rendszerrel.', 1, 220000.00, '/assets/images/real-estate.jpg', '[\"Property Search\", \"Map Integration\", \"Virtual Tours\", \"Contact Forms\", \"Property Management\", \"Mobile App\"]', 20, 'active', '2025-10-06 14:13:27', '2025-10-06 14:13:27'),
(13, 'Tech Blog', 'Technológiai blog tartalomkezelő rendszerrel, komment rendszerrel és hírlevél integrációval.', 5, 120000.00, '/assets/images/tech-blog.jpg', '[\"Content Management\", \"Comment System\", \"Newsletter\", \"Social Sharing\", \"SEO Tools\", \"Analytics\"]', 15, 'active', '2025-10-06 14:13:27', '2025-10-06 14:13:27'),
(14, 'Wedding Planner Site', 'Esküvői szervező weboldal szolgáltatásokkal, portfólióval és kapcsolatfelvételi rendszerrel.', 1, 140000.00, '/assets/images/wedding.jpg', '[\"Service Portfolio\", \"Photo Gallery\", \"Contact Forms\", \"Testimonials\", \"Social Media\", \"Mobile Optimized\"]', 13, 'active', '2025-10-06 14:13:27', '2025-10-06 14:13:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `webdesign_id` (`webdesign_id`);

--
-- Indexes for table `portfolio_images`
--
ALTER TABLE `portfolio_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `webdesign_id` (`webdesign_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `webdesign_id` (`webdesign_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `webdesigns`
--
ALTER TABLE `webdesigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `portfolio_images`
--
ALTER TABLE `portfolio_images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `webdesigns`
--
ALTER TABLE `webdesigns`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`webdesign_id`) REFERENCES `webdesigns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `portfolio_images`
--
ALTER TABLE `portfolio_images`
  ADD CONSTRAINT `portfolio_images_ibfk_1` FOREIGN KEY (`webdesign_id`) REFERENCES `webdesigns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`webdesign_id`) REFERENCES `webdesigns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `webdesigns`
--
ALTER TABLE `webdesigns`
  ADD CONSTRAINT `webdesigns_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
