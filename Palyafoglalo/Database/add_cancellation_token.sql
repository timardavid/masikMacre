-- Add cancellation_token column to bookings table
-- Run this migration to add cancellation token support

USE palyafoglalo;

ALTER TABLE `bookings` 
ADD COLUMN `cancellation_token` VARCHAR(64) NULL AFTER `payment_status`,
ADD INDEX `idx_bookings_cancellation_token` (`cancellation_token`);

