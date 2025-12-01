-- Add phone and address fields to users table
USE palyafoglalo;

ALTER TABLE `users` 
ADD COLUMN `address` VARCHAR(255) NULL AFTER `phone`,
MODIFY COLUMN `phone` VARCHAR(30) NULL;

-- Add password reset fields
ALTER TABLE `users`
ADD COLUMN `password_reset_token` VARCHAR(64) NULL AFTER `address`,
ADD COLUMN `password_reset_expires` DATETIME NULL AFTER `password_reset_token`,
ADD INDEX `idx_users_reset_token` (`password_reset_token`);

