-- Táblák az étlapokhoz
-- Fő tábla: heti étlapok
DROP TABLE IF EXISTS `menu_weeks`;
CREATE TABLE `menu_weeks` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `group_type` varchar(100) COLLATE utf8mb4_hungarian_ci NOT NULL DEFAULT 'Óvodás (4-6 év)',
  `location` varchar(100) COLLATE utf8mb4_hungarian_ci DEFAULT 'Himesháza',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dates` (`start_date`, `end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- Napok táblája: minden nap adatai
DROP TABLE IF EXISTS `menu_days`;
CREATE TABLE `menu_days` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `menu_week_id` int UNSIGNED NOT NULL,
  `day_name` varchar(20) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `day_date` date NOT NULL,
  `day_order` tinyint UNSIGNED NOT NULL,
  `tizorai_items` text COLLATE utf8mb4_hungarian_ci,
  `tizorai_allergens` varchar(200) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `tizorai_nutrition` text COLLATE utf8mb4_hungarian_ci,
  `ebed_items` text COLLATE utf8mb4_hungarian_ci,
  `ebed_allergens` varchar(200) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `ebed_nutrition` text COLLATE utf8mb4_hungarian_ci,
  `uzsonna_items` text COLLATE utf8mb4_hungarian_ci,
  `uzsonna_allergens` varchar(200) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `uzsonna_nutrition` text COLLATE utf8mb4_hungarian_ci,
  `total_nutrition` text COLLATE utf8mb4_hungarian_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_menu_week` (`menu_week_id`),
  KEY `idx_day_date` (`day_date`),
  CONSTRAINT `fk_menu_days_week` FOREIGN KEY (`menu_week_id`) REFERENCES `menu_weeks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

