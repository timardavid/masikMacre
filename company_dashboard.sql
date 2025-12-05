-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Dec 05, 2025 at 11:19 AM
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
-- Database: `company_dashboard`
--

-- --------------------------------------------------------

--
-- Table structure for table `monthly_summary`
--

DROP TABLE IF EXISTS `monthly_summary`;
CREATE TABLE `monthly_summary` (
  `id` int NOT NULL,
  `month` varchar(7) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `total_revenue` decimal(12,2) DEFAULT '0.00',
  `total_expenses` decimal(12,2) DEFAULT '0.00',
  `total_salary` decimal(12,2) DEFAULT '0.00',
  `profit` decimal(12,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `monthly_summary`
--

INSERT INTO `monthly_summary` (`id`, `month`, `total_revenue`, `total_expenses`, `total_salary`, `profit`, `created_at`) VALUES
(1, '2025-10', 110050000.00, 0.00, 214900000.00, -104850000.00, '2025-10-26 08:42:51');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_hungarian_ci,
  `status` enum('active','inactive') COLLATE utf8mb4_hungarian_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `category`, `description`, `status`, `created_at`) VALUES
(1, 'Könyvvizsgálati Szolgáltatás', 500000.00, 'Pénzügy', 'Teljes éves könyvvizsgálás', 'active', '2025-10-26 08:42:51'),
(2, 'Adótanácsadás', 150000.00, 'Pénzügy', 'Éves adóoptimalizálási tanácsadás', 'active', '2025-10-26 08:42:51'),
(3, 'Pénzügyi Terv Készítés', 300000.00, 'Pénzügy', 'Részletes pénzügyi elemzés és javaslatok', 'active', '2025-10-26 08:42:51'),
(4, 'Számviteli Szolgáltatás', 250000.00, 'Pénzügy', 'Havi számviteli végelemzés', 'active', '2025-10-26 08:42:51'),
(5, 'Vállalkozói Tanácsadás', 400000.00, 'Tanácsadás', 'Teljes üzleti elemzés és stratégia', 'active', '2025-10-26 08:42:51'),
(6, 'IT Támogatás', 200000.00, 'IT', 'Havi technikai támogatás', 'active', '2025-10-26 08:42:51'),
(7, 'Webfejlesztés', 800000.00, 'IT', 'Egyedi weboldal fejlesztés', 'active', '2025-10-26 08:42:51'),
(8, 'Adatbázis Karbantartás', 150000.00, 'IT', 'Havi adatbázis mentés és karbantartás', 'active', '2025-10-26 08:42:51');

-- --------------------------------------------------------

--
-- Table structure for table `revenue`
--

DROP TABLE IF EXISTS `revenue`;
CREATE TABLE `revenue` (
  `id` int NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `date` date NOT NULL,
  `source` varchar(200) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_hungarian_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `revenue`
--

INSERT INTO `revenue` (`id`, `amount`, `date`, `source`, `notes`, `created_at`) VALUES
(1, 500000.00, '2025-10-26', 'ABC Kft - Könyvvizsgálat', 'Éves könyvvizsgálati szolgáltatás', '2025-10-26 08:42:51'),
(2, 300000.00, '2025-10-23', 'XYZ Zrt - Pénzügyi Terv', 'Éves pénzügyi elemzés', '2025-10-26 08:42:51'),
(3, 150000.00, '2025-10-19', 'LMN Kft - Adótanácsadás', 'Adóoptimalizálási terv', '2025-10-26 08:42:51'),
(4, 800000.00, '2025-10-16', 'PQR Bt - Webfejlesztés', 'Egyedi weboldal projekt', '2025-10-26 08:42:51'),
(5, 200000.00, '2025-10-21', 'DEF Kft - IT Támogatás', 'Havi támogatás', '2025-10-26 08:42:51'),
(6, 250000.00, '2025-10-14', 'GHI Zrt - Számvitel', 'Havi számviteli végelemzés', '2025-10-26 08:42:51'),
(7, 400000.00, '2025-10-18', 'JKL Bt - Tanácsadás', 'Üzleti stratégiai tanácsadás', '2025-10-26 08:42:51'),
(8, 150000.00, '2025-10-25', 'MNO Kft - Adatbázis', 'Havi adatbázis karbantartás', '2025-10-26 08:42:51'),
(9, 500000.00, '2025-10-12', 'PQR Zrt - Könyvvizsgálat', 'Éves könyvvizsgálat projekt', '2025-10-26 08:42:51'),
(10, 300000.00, '2025-10-17', 'STU Bt - Pénzügyi Terv', 'Éves pénzügyi elemzés', '2025-10-26 08:42:51');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `client_name` varchar(100) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `task_title` varchar(200) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `description` text COLLATE utf8mb4_hungarian_ci,
  `priority` enum('Critical','Very Urgent','Urgent','Not Urgent') COLLATE utf8mb4_hungarian_ci DEFAULT 'Not Urgent',
  `status` enum('pending','in_progress','completed') COLLATE utf8mb4_hungarian_ci DEFAULT 'pending',
  `deadline` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `client_name`, `task_title`, `description`, `priority`, `status`, `deadline`, `created_at`) VALUES
(1, 2, 'ABC Kft', 'Webszerver javítás', 'Apache konfiguráció frissítése', 'Critical', 'in_progress', '2025-10-28 09:42:51', '2025-10-26 08:42:51'),
(2, 3, 'XYZ Zrt', 'Új dolgozó felvétel', 'IT állás betöltése dokumentáció', 'Urgent', 'pending', '2025-10-31 09:42:51', '2025-10-26 08:42:51'),
(3, 4, 'LMN Kft', 'Havi zárás', 'Januári pénzügyi zárás', 'Very Urgent', 'in_progress', '2025-10-27 09:42:51', '2025-10-26 08:42:51'),
(4, 2, 'PQR Bt', 'Adatbázis backup', 'Heti adatbázis mentés tesztelése', 'Not Urgent', 'pending', '2025-11-02 09:42:51', '2025-10-26 08:42:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `role` enum('Admin','IT','HR','Finance','CEO','Accountant','Financial Advisor','Cleaner','Receptionist','Secretary') COLLATE utf8mb4_hungarian_ci NOT NULL,
  `department` varchar(50) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','inactive') COLLATE utf8mb4_hungarian_ci DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `department`, `phone`, `salary`, `created_at`, `status`) VALUES
(1, 'Admin Felhasználó', 'admin@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'IT', '1234567890', 800000.00, '2025-10-26 08:42:51', 'active'),
(2, 'IT Szakember', 'it@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'IT', 'IT', '0987654321', 650000.00, '2025-10-26 08:42:51', 'active'),
(3, 'HR Menedzser', 'hr@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'HR', 'HR', '1122334455', 700000.00, '2025-10-26 08:42:51', 'active'),
(4, 'Pénzügyes', 'finance@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Finance', 'Finance', '5566778899', 750000.00, '2025-10-26 08:42:51', 'active'),
(5, 'Ügyvezető', 'ceo@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'CEO', 'Management', '9988776655', 1500000.00, '2025-10-26 08:42:51', 'active'),
(6, 'Dolgozó Péter', 'peter@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'IT', 'IT', '5544332211', 600000.00, '2025-10-26 08:42:51', 'active'),
(7, 'Kovács Anna', 'kovacs.anna@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Financial Advisor', 'Pénzügyi Tanácsadás', '0612345678', 850000.00, '2025-10-26 08:42:51', 'active'),
(8, 'Nagy István', 'nagy.istvan@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Financial Advisor', 'Pénzügyi Tanácsadás', '0623456789', 820000.00, '2025-10-26 08:42:51', 'active'),
(9, 'Szabó Mária', 'szabo.maria@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Financial Advisor', 'Pénzügyi Tanácsadás', '0634567890', 800000.00, '2025-10-26 08:42:51', 'active'),
(10, 'Horváth Ferenc', 'horvath.ferenc@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Accountant', 'Pénzügy', '0645678901', 750000.00, '2025-10-26 08:42:51', 'active'),
(11, 'Tóth Eszter', 'toth.eszter@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Accountant', 'Pénzügy', '0656789012', 720000.00, '2025-10-26 08:42:51', 'active'),
(12, 'Varga Péter', 'varga.peter@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'IT', 'IT', '0667890123', 620000.00, '2025-10-26 08:42:51', 'active'),
(13, 'Kiss Andrea', 'kiss.andrea@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'HR', 'HR', '0678901234', 580000.00, '2025-10-26 08:42:51', 'active'),
(14, 'Molnár Gábor', 'molnar.gabor@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Financial Advisor', 'Pénzügyi Tanácsadás', '0689012345', 830000.00, '2025-10-26 08:42:51', 'active'),
(15, 'Farkas Emese', 'farkas.emese@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Secretary', 'Adminisztráció', '0690123456', 480000.00, '2025-10-26 08:42:51', 'active'),
(16, 'Váradi Zoltán', 'varadi.zoltan@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'IT', 'IT', '0610123456', 630000.00, '2025-10-26 08:42:51', 'active'),
(17, 'Balogh Csilla', 'balogh.csilla@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Accountant', 'Pénzügy', '0621234567', 700000.00, '2025-10-26 08:42:51', 'active'),
(18, 'Takács Márton', 'takacs.marton@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Financial Advisor', 'Pénzügyi Tanácsadás', '0632345678', 840000.00, '2025-10-26 08:42:51', 'active'),
(19, 'Németh Anita', 'nemeth.anita@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'HR', 'HR', '0643456789', 590000.00, '2025-10-26 08:42:51', 'active'),
(20, 'Papp Róbert', 'papp.robert@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Financial Advisor', 'Pénzügyi Tanácsadás', '0654567890', 810000.00, '2025-10-26 08:42:51', 'active'),
(21, 'László Orsolya', 'laszlo.orsolya@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Accountant', 'Pénzügy', '0665678901', 680000.00, '2025-10-26 08:42:51', 'active'),
(22, 'Gál Tamás', 'gal.tamas@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'IT', 'IT', '0676789012', 610000.00, '2025-10-26 08:42:51', 'active'),
(23, 'Márton Katalin', 'marton.katalin@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cleaner', 'Karbantartás', '0687890123', 380000.00, '2025-10-26 08:42:51', 'active'),
(24, 'Simon Dániel', 'simon.daniel@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Financial Advisor', 'Pénzügyi Tanácsadás', '0698901234', 825000.00, '2025-10-26 08:42:51', 'active'),
(25, 'Kovács Barbara', 'kovacs.barbara@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Receptionist', 'Recepció', '0619012345', 420000.00, '2025-10-26 08:42:51', 'active'),
(26, 'Nagy Mihály', 'nagy.mihaly@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'IT', 'IT', '0620123456', 640000.00, '2025-10-26 08:42:51', 'active'),
(27, 'Szabó Gabriella', 'szabo.gabriella@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'HR', 'HR', '0631234567', 570000.00, '2025-10-26 08:42:51', 'active'),
(28, 'Horváth Ádám', 'horvath.adam@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Accountant', 'Pénzügy', '0642345678', 690000.00, '2025-10-26 08:42:51', 'active'),
(29, 'Tóth Réka', 'toth.reka@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Financial Advisor', 'Pénzügyi Tanácsadás', '0653456789', 835000.00, '2025-10-26 08:42:51', 'active'),
(30, 'Varga Benedek', 'varga.benedek@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cleaner', 'Karbantartás', '0664567890', 370000.00, '2025-10-26 08:42:51', 'active'),
(31, 'Kiss Bálint', 'kiss.balint@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Secretary', 'Adminisztráció', '0675678901', 450000.00, '2025-10-26 08:42:51', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `work_hours`
--

DROP TABLE IF EXISTS `work_hours`;
CREATE TABLE `work_hours` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `date` date NOT NULL,
  `hours_worked` decimal(4,2) DEFAULT '0.00',
  `break_hours` decimal(4,2) DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_hungarian_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `work_status`
--

DROP TABLE IF EXISTS `work_status`;
CREATE TABLE `work_status` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `status` enum('working','break','vacation','sick_leave','no_work') COLLATE utf8mb4_hungarian_ci DEFAULT 'no_work',
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `notes` text COLLATE utf8mb4_hungarian_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `work_status`
--

INSERT INTO `work_status` (`id`, `user_id`, `status`, `start_time`, `end_time`, `notes`, `created_at`) VALUES
(1, 2, 'working', '2025-10-26 09:42:51', NULL, NULL, '2025-10-26 08:42:51'),
(2, 3, 'working', '2025-10-26 09:42:51', NULL, NULL, '2025-10-26 08:42:51'),
(3, 4, 'break', '2025-10-26 09:42:51', NULL, NULL, '2025-10-26 08:42:51'),
(4, 5, 'vacation', '2025-10-25 09:42:51', NULL, NULL, '2025-10-26 08:42:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `monthly_summary`
--
ALTER TABLE `monthly_summary`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_month` (`month`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `revenue`
--
ALTER TABLE `revenue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `work_hours`
--
ALTER TABLE `work_hours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_date` (`user_id`,`date`);

--
-- Indexes for table `work_status`
--
ALTER TABLE `work_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `monthly_summary`
--
ALTER TABLE `monthly_summary`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `revenue`
--
ALTER TABLE `revenue`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `work_hours`
--
ALTER TABLE `work_hours`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `work_status`
--
ALTER TABLE `work_status`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `work_hours`
--
ALTER TABLE `work_hours`
  ADD CONSTRAINT `work_hours_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `work_status`
--
ALTER TABLE `work_status`
  ADD CONSTRAINT `work_status_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
