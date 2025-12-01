-- Seed data for Tennis Court Booking System
USE `palyafoglalo`;

-- Roles
INSERT INTO `roles` (`name`) VALUES
  ('admin'),
  ('staff'),
  ('customer')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Surfaces
INSERT INTO `surfaces` (`name`) VALUES
  ('Clay'),
  ('Grass'),
  ('Hard'),
  ('Carpet')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Courts
INSERT INTO `courts` (`name`, `surface_id`, `is_indoor`, `has_lighting`, `notes`, `is_active`)
SELECT * FROM (
  SELECT 'Court 1' AS name, 1 AS surface_id, 0 AS is_indoor, 1 AS has_lighting, NULL AS notes, 1 AS is_active
  UNION ALL SELECT 'Court 2', 3, 0, 1, NULL, 1
  UNION ALL SELECT 'Court 3 (Indoor)', 3, 1, 1, 'Heated hall', 1
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM `courts` c WHERE c.`name` = tmp.`name`);

-- Opening hours: 08:00 - 22:00 every day for all courts
INSERT INTO `court_opening_hours` (`court_id`, `weekday`, `is_closed`, `open_time`, `close_time`)
SELECT c.`id`, d.`weekday`, 0, '08:00:00', '22:00:00'
FROM `courts` c
CROSS JOIN (
  SELECT 0 AS `weekday` UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3
  UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6
) d
WHERE NOT EXISTS (
  SELECT 1 FROM `court_opening_hours` oh WHERE oh.`court_id` = c.`id`
);

-- Admin user (password to be set via backend; store placeholder hash)
INSERT INTO `users` (`email`, `password_hash`, `full_name`, `phone`, `role_id`, `is_active`)
VALUES ('admin@example.com', '$2y$10$PLACEHOLDERHASHPLACEHOLDERHASHPLACEHOLDERHA', 'Admin User', NULL, (SELECT id FROM roles WHERE name='admin'), 1)
ON DUPLICATE KEY UPDATE `email` = VALUES(`email`);

-- Example blackout: New Year maintenance
INSERT INTO `blackout_intervals` (`court_id`, `start_datetime`, `end_datetime`, `reason`)
VALUES (NULL, '2026-01-01 00:00:00', '2026-01-01 12:00:00', 'New Year maintenance')
ON DUPLICATE KEY UPDATE `start_datetime` = VALUES(`start_datetime`);

-- Pricing Rules (examples)
-- Default: weekday pricing 3000 HUF/hour
INSERT INTO `pricing_rules` (`name`, `court_id`, `surface_id`, `is_indoor`, `weekday`, `is_weekend`, `start_time`, `end_time`, `price_per_hour_cents`, `priority`, `is_active`)
VALUES
  ('Weekday Default', NULL, NULL, NULL, NULL, 0, NULL, NULL, 300000, 10, 1),
  ('Weekend Default', NULL, NULL, NULL, NULL, 1, NULL, NULL, 400000, 10, 1),
  ('Peak Hours Weekend', NULL, NULL, NULL, NULL, 1, '17:00:00', '22:00:00', 500000, 20, 1),
  ('Indoor Premium', NULL, NULL, 1, NULL, NULL, NULL, NULL, 350000, 15, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Booking Rules (system constraints)
INSERT INTO `booking_rules` (`key_name`, `value`, `description`) VALUES
  ('min_booking_duration_minutes', '60', 'Minimum booking duration in minutes'),
  ('max_booking_duration_minutes', '240', 'Maximum booking duration in minutes'),
  ('max_days_in_advance', '90', 'Maximum number of days in advance bookings can be made'),
  ('min_hours_before_booking', '2', 'Minimum hours before start time a booking can be made'),
  ('auto_confirm_enabled', '1', 'Whether bookings are automatically confirmed (1) or require manual confirmation (0)'),
  ('cancellation_hours_before', '24', 'Hours before start time when cancellation is still allowed'),
  ('refund_percentage_on_cancel', '50', 'Percentage of payment refunded when cancelled within allowed time')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);


