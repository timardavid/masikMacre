-- Migration: Add images and reviews to courts
-- Add image fields to courts table and create reviews table

USE `palyafoglalo`;

-- Add image fields to courts table
ALTER TABLE `courts` 
ADD COLUMN `main_image_url` VARCHAR(255) NULL AFTER `notes`,
ADD COLUMN `description` TEXT NULL AFTER `main_image_url`;

-- Create court_images table for multiple images per court
CREATE TABLE IF NOT EXISTS `court_images` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `court_id` INT UNSIGNED NOT NULL,
  `image_url` VARCHAR(255) NOT NULL,
  `image_path` VARCHAR(500) NULL COMMENT 'Local file path',
  `alt_text` VARCHAR(200) NULL,
  `display_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `pk_court_images` PRIMARY KEY (`id`),
  CONSTRAINT `fk_court_images_court` FOREIGN KEY (`court_id`) REFERENCES `courts`(`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  INDEX `idx_court_images_court_order` (`court_id`, `display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create court_reviews table for user reviews/ratings
CREATE TABLE IF NOT EXISTS `court_reviews` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `court_id` INT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `rating` TINYINT UNSIGNED NOT NULL COMMENT '1-5 stars',
  `title` VARCHAR(200) NULL,
  `review_text` TEXT NULL,
  `is_verified` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Verified booking required',
  `is_approved` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Admin approval',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `pk_court_reviews` PRIMARY KEY (`id`),
  CONSTRAINT `fk_court_reviews_court` FOREIGN KEY (`court_id`) REFERENCES `courts`(`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_court_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `ck_rating_range` CHECK (`rating` BETWEEN 1 AND 5),
  INDEX `idx_court_reviews_court` (`court_id`, `is_active`, `is_approved`, `created_at` DESC),
  INDEX `idx_court_reviews_user` (`user_id`, `created_at` DESC),
  UNIQUE KEY `uq_court_reviews_user_court` (`court_id`, `user_id`) COMMENT 'One review per user per court'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add additional court details
ALTER TABLE `courts`
ADD COLUMN `capacity` INT UNSIGNED NULL COMMENT 'Maximum players' AFTER `description`,
ADD COLUMN `dimensions` VARCHAR(100) NULL COMMENT 'Court dimensions (e.g., "23.77m x 10.97m")' AFTER `capacity`,
ADD COLUMN `facilities` TEXT NULL COMMENT 'JSON array of facilities' AFTER `dimensions`,
ADD COLUMN `parking_available` TINYINT(1) NOT NULL DEFAULT 0 AFTER `facilities`,
ADD COLUMN `changing_rooms` TINYINT(1) NOT NULL DEFAULT 0 AFTER `parking_available`,
ADD COLUMN `pro_shop` TINYINT(1) NOT NULL DEFAULT 0 AFTER `changing_rooms`,
ADD COLUMN `average_rating` DECIMAL(3,2) NULL COMMENT 'Calculated average rating' AFTER `pro_shop`,
ADD COLUMN `total_reviews` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `average_rating`;

