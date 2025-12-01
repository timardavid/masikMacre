-- Tennis Court Booking System - Database Schema (MySQL 8.0+)
-- Charset: utf8mb4, Engine: InnoDB
-- Safety: Uses FKs, indexes, and triggers to prevent overlapping bookings

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET sql_mode = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- Create database (optional)
CREATE DATABASE IF NOT EXISTS `palyafoglalo` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `palyafoglalo`;

-- ------------------------------------------------------------
-- Utility
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
  `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  CONSTRAINT `pk_roles` PRIMARY KEY (`id`),
  CONSTRAINT `uq_roles_name` UNIQUE (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(191) NOT NULL,
  `password_hash` VARCHAR(191) NOT NULL,
  `full_name` VARCHAR(120) NOT NULL,
  `phone` VARCHAR(30) NULL,
  `role_id` TINYINT UNSIGNED NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `pk_users` PRIMARY KEY (`id`),
  CONSTRAINT `uq_users_email` UNIQUE (`email`),
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`)
    ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Courts and configuration
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `surfaces` (
  `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  CONSTRAINT `pk_surfaces` PRIMARY KEY (`id`),
  CONSTRAINT `uq_surfaces_name` UNIQUE (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `courts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(80) NOT NULL,
  `surface_id` TINYINT UNSIGNED NOT NULL,
  `is_indoor` TINYINT(1) NOT NULL DEFAULT 0,
  `has_lighting` TINYINT(1) NOT NULL DEFAULT 0,
  `notes` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `pk_courts` PRIMARY KEY (`id`),
  CONSTRAINT `uq_courts_name` UNIQUE (`name`),
  CONSTRAINT `fk_courts_surface` FOREIGN KEY (`surface_id`) REFERENCES `surfaces`(`id`)
    ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Opening hours per court and weekday (0 = Sunday .. 6 = Saturday)
CREATE TABLE IF NOT EXISTS `court_opening_hours` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `court_id` INT UNSIGNED NOT NULL,
  `weekday` TINYINT UNSIGNED NOT NULL,
  `is_closed` TINYINT(1) NOT NULL DEFAULT 0,
  `open_time` TIME NULL,
  `close_time` TIME NULL,
  CONSTRAINT `pk_court_opening_hours` PRIMARY KEY (`id`),
  CONSTRAINT `fk_opening_hours_court` FOREIGN KEY (`court_id`) REFERENCES `courts`(`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `ck_weekday_range` CHECK (`weekday` BETWEEN 0 AND 6),
  CONSTRAINT `ck_open_close_time` CHECK ((`is_closed` = 1 AND `open_time` IS NULL AND `close_time` IS NULL)
                                      OR (`is_closed` = 0 AND `open_time` IS NOT NULL AND `close_time` IS NOT NULL AND `open_time` < `close_time`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blackout intervals (maintenance, events, holidays). If court_id NULL => applies to all courts
CREATE TABLE IF NOT EXISTS `blackout_intervals` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `court_id` INT UNSIGNED NULL,
  `start_datetime` DATETIME NOT NULL,
  `end_datetime` DATETIME NOT NULL,
  `reason` VARCHAR(255) NULL,
  CONSTRAINT `pk_blackout_intervals` PRIMARY KEY (`id`),
  CONSTRAINT `fk_blackout_court` FOREIGN KEY (`court_id`) REFERENCES `courts`(`id`)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT `ck_blackout_time_order` CHECK (`start_datetime` < `end_datetime`),
  INDEX `idx_blackout_court_time` (`court_id`, `start_datetime`, `end_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Pricing System
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pricing_rules` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `court_id` INT UNSIGNED NULL, -- NULL = applies to all courts
  `surface_id` TINYINT UNSIGNED NULL, -- NULL = applies to all surfaces
  `is_indoor` TINYINT(1) NULL, -- NULL = both, 0 = outdoor only, 1 = indoor only
  `weekday` TINYINT UNSIGNED NULL, -- NULL = all days, 0-6 = specific weekday
  `is_weekend` TINYINT(1) NULL, -- NULL = both, 0 = weekday only, 1 = weekend only
  `start_time` TIME NULL, -- NULL = all day, otherwise start of time range
  `end_time` TIME NULL, -- NULL = all day, otherwise end of time range
  `price_per_hour_cents` INT UNSIGNED NOT NULL,
  `currency` CHAR(3) NOT NULL DEFAULT 'HUF',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `priority` TINYINT UNSIGNED NOT NULL DEFAULT 0, -- Higher priority = applied first
  `valid_from` DATE NULL, -- NULL = no start limit
  `valid_until` DATE NULL, -- NULL = no end limit
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `pk_pricing_rules` PRIMARY KEY (`id`),
  CONSTRAINT `fk_pricing_court` FOREIGN KEY (`court_id`) REFERENCES `courts`(`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_pricing_surface` FOREIGN KEY (`surface_id`) REFERENCES `surfaces`(`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `ck_weekday_range_pricing` CHECK (`weekday` IS NULL OR `weekday` BETWEEN 0 AND 6),
  CONSTRAINT `ck_time_range_pricing` CHECK ((`start_time` IS NULL AND `end_time` IS NULL)
                                          OR (`start_time` IS NOT NULL AND `end_time` IS NOT NULL AND `start_time` < `end_time`)),
  CONSTRAINT `ck_valid_date_range` CHECK (`valid_from` IS NULL OR `valid_until` IS NULL OR `valid_from` <= `valid_until`),
  INDEX `idx_pricing_active` (`is_active`, `priority` DESC, `court_id`),
  INDEX `idx_pricing_validity` (`valid_from`, `valid_until`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Booking Rules and Constraints
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `booking_rules` (
  `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key_name` VARCHAR(50) NOT NULL,
  `value` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `updated_by_user_id` BIGINT UNSIGNED NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `pk_booking_rules` PRIMARY KEY (`id`),
  CONSTRAINT `uq_booking_rules_key` UNIQUE (`key_name`),
  CONSTRAINT `fk_booking_rules_updated_by` FOREIGN KEY (`updated_by_user_id`) REFERENCES `users`(`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Bookings
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `court_id` INT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NULL, -- who created the booking (staff or registered user)
  `customer_name` VARCHAR(120) NOT NULL,
  `customer_phone` VARCHAR(30) NULL,
  `customer_email` VARCHAR(191) NULL,
  `start_datetime` DATETIME NOT NULL,
  `end_datetime` DATETIME NOT NULL,
  `status` ENUM('pending','confirmed','cancelled','completed','no_show') NOT NULL DEFAULT 'pending',
  `price_cents` INT UNSIGNED NULL,
  `currency` CHAR(3) NOT NULL DEFAULT 'HUF',
  `payment_status` ENUM('unpaid','paid','refunded') NOT NULL DEFAULT 'unpaid',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `pk_bookings` PRIMARY KEY (`id`),
  CONSTRAINT `fk_bookings_court` FOREIGN KEY (`court_id`) REFERENCES `courts`(`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT `ck_booking_time_order` CHECK (`start_datetime` < `end_datetime`),
  INDEX `idx_bookings_court_time` (`court_id`, `start_datetime`, `end_datetime`),
  INDEX `idx_bookings_status_time` (`status`, `start_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `booking_notes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `booking_id` BIGINT UNSIGNED NOT NULL,
  `author_user_id` BIGINT UNSIGNED NULL,
  `note` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `pk_booking_notes` PRIMARY KEY (`id`),
  CONSTRAINT `fk_booking_notes_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_booking_notes_author` FOREIGN KEY (`author_user_id`) REFERENCES `users`(`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Payment Transactions
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `payment_transactions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `booking_id` BIGINT UNSIGNED NOT NULL,
  `transaction_type` ENUM('payment','refund','partial_refund') NOT NULL,
  `amount_cents` INT NOT NULL, -- Can be negative for refunds
  `currency` CHAR(3) NOT NULL DEFAULT 'HUF',
  `payment_method` ENUM('cash','card','bank_transfer','online','voucher') NOT NULL,
  `payment_provider` VARCHAR(50) NULL, -- e.g., 'stripe', 'paypal', 'otp_bank'
  `external_transaction_id` VARCHAR(255) NULL, -- ID from payment provider
  `status` ENUM('pending','completed','failed','cancelled') NOT NULL DEFAULT 'pending',
  `processed_at` DATETIME NULL,
  `processed_by_user_id` BIGINT UNSIGNED NULL,
  `notes` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `pk_payment_transactions` PRIMARY KEY (`id`),
  CONSTRAINT `fk_payment_transactions_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_payment_transactions_processed_by` FOREIGN KEY (`processed_by_user_id`) REFERENCES `users`(`id`)
    ON UPDATE CASCADE ON DELETE SET NULL,
  INDEX `idx_payment_booking` (`booking_id`, `status`),
  INDEX `idx_payment_external_id` (`external_transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Audit Log (tracks all important changes)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `audit_log` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `table_name` VARCHAR(64) NOT NULL,
  `record_id` BIGINT UNSIGNED NOT NULL,
  `action` ENUM('INSERT','UPDATE','DELETE') NOT NULL,
  `user_id` BIGINT UNSIGNED NULL, -- Who made the change
  `old_values` JSON NULL, -- Previous state (for UPDATE/DELETE)
  `new_values` JSON NULL, -- New state (for INSERT/UPDATE)
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `pk_audit_log` PRIMARY KEY (`id`),
  CONSTRAINT `fk_audit_log_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
    ON UPDATE CASCADE ON DELETE SET NULL,
  INDEX `idx_audit_table_record` (`table_name`, `record_id`),
  INDEX `idx_audit_user_time` (`user_id`, `created_at` DESC),
  INDEX `idx_audit_action_time` (`action`, `created_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Notifications Log (track email/SMS notifications)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `booking_id` BIGINT UNSIGNED NULL, -- Can be NULL for system notifications
  `user_id` BIGINT UNSIGNED NULL,
  `recipient_email` VARCHAR(191) NULL,
  `recipient_phone` VARCHAR(30) NULL,
  `notification_type` ENUM('email','sms','push') NOT NULL,
  `subject` VARCHAR(255) NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('pending','sent','failed','bounced') NOT NULL DEFAULT 'pending',
  `sent_at` DATETIME NULL,
  `error_message` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `pk_notifications` PRIMARY KEY (`id`),
  CONSTRAINT `fk_notifications_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
    ON UPDATE CASCADE ON DELETE SET NULL,
  INDEX `idx_notifications_booking` (`booking_id`),
  INDEX `idx_notifications_status_created` (`status`, `created_at`),
  INDEX `idx_notifications_recipient` (`recipient_email`, `recipient_phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Overlap prevention triggers for bookings (per court)
-- Prevents overlapping with non-cancelled bookings and with blackout intervals
-- ------------------------------------------------------------
DROP TRIGGER IF EXISTS `trg_bookings_before_insert`;
DELIMITER $$
CREATE TRIGGER `trg_bookings_before_insert` BEFORE INSERT ON `bookings`
FOR EACH ROW
BEGIN
  -- Overlap with existing bookings (exclude cancelled)
  IF EXISTS (
      SELECT 1 FROM `bookings` b
      WHERE b.`court_id` = NEW.`court_id`
        AND b.`status` <> 'cancelled'
        AND b.`start_datetime` < NEW.`end_datetime`
        AND b.`end_datetime` > NEW.`start_datetime`
  ) THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Overlapping booking exists for this court.';
  END IF;

  -- Overlap with blackout intervals (both court-specific and global)
  IF EXISTS (
      SELECT 1 FROM `blackout_intervals` bi
      WHERE (bi.`court_id` = NEW.`court_id` OR bi.`court_id` IS NULL)
        AND bi.`start_datetime` < NEW.`end_datetime`
        AND bi.`end_datetime` > NEW.`start_datetime`
  ) THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Requested time falls within a blackout interval.';
  END IF;
END $$
DELIMITER ;

DROP TRIGGER IF EXISTS `trg_bookings_before_update`;
DELIMITER $$
CREATE TRIGGER `trg_bookings_before_update` BEFORE UPDATE ON `bookings`
FOR EACH ROW
BEGIN
  -- Only check overlaps if court/time/status change meaningfully affects occupancy
  IF (NEW.`court_id` <> OLD.`court_id`
      OR NEW.`start_datetime` <> OLD.`start_datetime`
      OR NEW.`end_datetime` <> OLD.`end_datetime`
      OR (OLD.`status` = 'cancelled' AND NEW.`status` <> 'cancelled')
      OR (OLD.`status` <> 'cancelled' AND NEW.`status` = 'cancelled')) THEN

    IF NEW.`start_datetime` >= NEW.`end_datetime` THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid time range: start must be before end.';
    END IF;

    -- Overlap with other bookings (exclude self, exclude cancelled)
    IF EXISTS (
        SELECT 1 FROM `bookings` b
        WHERE b.`id` <> OLD.`id`
          AND b.`court_id` = NEW.`court_id`
          AND b.`status` <> 'cancelled'
          AND b.`start_datetime` < NEW.`end_datetime`
          AND b.`end_datetime` > NEW.`start_datetime`
    ) THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Overlapping booking exists for this court.';
    END IF;

    -- Overlap with blackout
    IF EXISTS (
        SELECT 1 FROM `blackout_intervals` bi
        WHERE (bi.`court_id` = NEW.`court_id` OR bi.`court_id` IS NULL)
          AND bi.`start_datetime` < NEW.`end_datetime`
          AND bi.`end_datetime` > NEW.`start_datetime`
    ) THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Requested time falls within a blackout interval.';
    END IF;
  END IF;
END $$
DELIMITER ;

-- ------------------------------------------------------------
-- Convenience views
-- ------------------------------------------------------------
CREATE OR REPLACE VIEW `v_courts_active` AS
SELECT c.`id`, c.`name`, s.`name` AS `surface`, c.`is_indoor`, c.`has_lighting`, c.`is_active`
FROM `courts` c
JOIN `surfaces` s ON s.`id` = c.`surface_id`
WHERE c.`is_active` = 1;

CREATE OR REPLACE VIEW `v_bookings_public` AS
SELECT b.`id`, b.`court_id`, c.`name` AS `court_name`, b.`start_datetime`, b.`end_datetime`, b.`status`
FROM `bookings` b
JOIN `courts` c ON c.`id` = b.`court_id`;

-- ------------------------------------------------------------
-- Helpful function: check availability (returns 1 if available, 0 if not)
-- Note: deterministic within transaction isolation; for application-side validation
-- ------------------------------------------------------------
DROP FUNCTION IF EXISTS `fn_is_court_available`;
DELIMITER $$
CREATE FUNCTION `fn_is_court_available`(
  in_court_id INT UNSIGNED,
  in_start DATETIME,
  in_end DATETIME
) RETURNS TINYINT
DETERMINISTIC
READS SQL DATA
BEGIN
  DECLARE conflict_count INT DEFAULT 0;
  IF in_start >= in_end THEN
    RETURN 0;
  END IF;

  SELECT COUNT(*) INTO conflict_count
  FROM `bookings` b
  WHERE b.`court_id` = in_court_id
    AND b.`status` <> 'cancelled'
    AND b.`start_datetime` < in_end
    AND b.`end_datetime` > in_start;

  IF conflict_count > 0 THEN
    RETURN 0;
  END IF;

  SELECT COUNT(*) INTO conflict_count
  FROM `blackout_intervals` bi
  WHERE (bi.`court_id` = in_court_id OR bi.`court_id` IS NULL)
    AND bi.`start_datetime` < in_end
    AND bi.`end_datetime` > in_start;

  RETURN IF(conflict_count = 0, 1, 0);
END $$
DELIMITER ;


