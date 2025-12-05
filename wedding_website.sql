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
-- Database: `wedding_website`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_info`
--

DROP TABLE IF EXISTS `contact_info`;
CREATE TABLE `contact_info` (
  `id` int NOT NULL,
  `bride_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `groom_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rsvp_deadline` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_info`
--

INSERT INTO `contact_info` (`id`, `bride_phone`, `groom_phone`, `email`, `rsvp_deadline`, `created_at`) VALUES
(1, '+1 555 123 4567', '+1 555 765 4321', 'john.jane.wedding@gmail.com', '2026-05-15', '2025-10-22 18:35:15');

-- --------------------------------------------------------

--
-- Table structure for table `couples`
--

DROP TABLE IF EXISTS `couples`;
CREATE TABLE `couples` (
  `id` int NOT NULL,
  `bride_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `groom_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `wedding_date` date NOT NULL,
  `wedding_time` time NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `couples`
--

INSERT INTO `couples` (`id`, `bride_name`, `groom_name`, `wedding_date`, `wedding_time`, `created_at`, `updated_at`) VALUES
(1, 'Jane', 'John', '2026-06-16', '14:00:00', '2025-10-22 18:35:15', '2025-10-22 18:35:15');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` int NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `event_time` time NOT NULL,
  `location` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_time`, `location`, `address`, `icon`, `sort_order`, `created_at`) VALUES
(1, 'Ceremony', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.', '14:00:00', 'Main Church', 'Downtown, Main Square 1', 'fas fa-church', 1, '2025-10-22 18:35:15'),
(2, 'Photography', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.', '15:30:00', 'Central Park', 'Downtown, Central District', 'fas fa-camera', 2, '2025-10-22 18:35:15'),
(3, 'Dinner & Reception', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.', '18:00:00', 'Grand Hotel Restaurant', 'Downtown, Main Street 2', 'fas fa-utensils', 3, '2025-10-22 18:35:15');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_images`
--

DROP TABLE IF EXISTS `gallery_images`;
CREATE TABLE `gallery_images` (
  `id` int NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery_images`
--

INSERT INTO `gallery_images` (`id`, `filename`, `alt_text`, `category`, `title`, `description`, `sort_order`, `created_at`) VALUES
(1, 'gallery1.jpg', 'Proposal moment', 'engagement', 'Proposal', 'August 2023 - Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 1, '2025-10-22 18:35:15'),
(2, 'gallery2.jpg', 'Travel memories', 'travel', 'Travel', 'Paris, 2022 - Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 2, '2025-10-22 18:35:15'),
(3, 'gallery3.jpg', 'Daily life moments', 'daily', 'Daily Life', 'Home moments - Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 3, '2025-10-22 18:35:15'),
(4, 'gallery4.jpg', 'Engagement ceremony', 'engagement', 'Engagement', 'Ring ceremony - Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 4, '2025-10-22 18:35:15'),
(5, 'gallery5.jpg', 'Holiday memories', 'travel', 'Holiday', 'Greece, 2023 - Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 5, '2025-10-22 18:35:15'),
(6, 'gallery6.jpg', 'Shared moments', 'daily', 'Shared Moments', 'Everyday happiness - Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 6, '2025-10-22 18:35:15');

-- --------------------------------------------------------

--
-- Table structure for table `rsvp_responses`
--

DROP TABLE IF EXISTS `rsvp_responses`;
CREATE TABLE `rsvp_responses` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attendance` enum('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL,
  `guest_count` int DEFAULT '1',
  `message` text COLLATE utf8mb4_unicode_ci,
  `submitted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE `site_settings` (
  `id` int NOT NULL,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'site_title', 'John & Jane - Wedding Invitation', '2025-10-22 18:35:15', '2025-10-22 18:35:15'),
(2, 'site_description', 'Join us as we celebrate our love and begin our journey together as husband and wife!', '2025-10-22 18:35:15', '2025-10-22 18:35:15'),
(3, 'hero_subtitle', 'Together we begin the most beautiful chapter of our lives', '2025-10-22 18:35:15', '2025-10-22 18:35:15'),
(4, 'rsvp_message', 'We can\'t wait to celebrate with you! Please let us know by May 15th if you\'ll be able to join us for our special day.', '2025-10-22 18:35:15', '2025-10-22 18:35:15'),
(5, 'footer_message', 'Join us on our special day as we begin our journey together as husband and wife.', '2025-10-22 18:35:15', '2025-10-22 18:35:15'),
(6, 'countdown_message', 'The big day has arrived!', '2025-10-22 18:35:15', '2025-10-22 18:35:15'),
(7, 'gallery_title', 'Gallery', '2025-10-22 18:35:15', '2025-10-22 18:35:15'),
(8, 'story_title', 'Our Story', '2025-10-22 18:35:15', '2025-10-22 18:35:15'),
(9, 'events_title', 'Events', '2025-10-22 18:35:15', '2025-10-22 18:35:15'),
(10, 'rsvp_title', 'RSVP', '2025-10-22 18:35:15', '2025-10-22 18:35:15'),
(11, 'countdown_title', 'Countdown', '2025-10-22 18:35:15', '2025-10-22 18:35:15');

-- --------------------------------------------------------

--
-- Table structure for table `story_timeline`
--

DROP TABLE IF EXISTS `story_timeline`;
CREATE TABLE `story_timeline` (
  `id` int NOT NULL,
  `year` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `story_timeline`
--

INSERT INTO `story_timeline` (`id`, `year`, `title`, `description`, `sort_order`, `created_at`) VALUES
(1, '2020', 'First Meeting', 'We met at a summer festival where music and dance brought us together. John was playing guitar, and I was dancing to the music. Since then, we dance together through life every day.', 1, '2025-10-22 18:35:15'),
(2, '2022', 'First Love', 'On a beautiful spring evening, under the stars, John told me for the first time that he loves me. That moment changed everything, and our love story truly began.', 2, '2025-10-22 18:35:15'),
(3, '2024', 'The Proposal', 'During a romantic weekend in the mountains, when the sunset painted the landscape in gold, John got down on one knee and asked for my hand in marriage.', 3, '2025-10-22 18:35:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_info`
--
ALTER TABLE `contact_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `couples`
--
ALTER TABLE `couples`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_events_sort` (`sort_order`);

--
-- Indexes for table `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_gallery_sort` (`sort_order`),
  ADD KEY `idx_gallery_category` (`category`);

--
-- Indexes for table `rsvp_responses`
--
ALTER TABLE `rsvp_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rsvp_submitted` (`submitted_at`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `story_timeline`
--
ALTER TABLE `story_timeline`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_story_sort` (`sort_order`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_info`
--
ALTER TABLE `contact_info`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `couples`
--
ALTER TABLE `couples`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `gallery_images`
--
ALTER TABLE `gallery_images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rsvp_responses`
--
ALTER TABLE `rsvp_responses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `story_timeline`
--
ALTER TABLE `story_timeline`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
