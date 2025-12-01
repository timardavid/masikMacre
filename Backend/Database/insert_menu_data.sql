-- Étlap adatok beszúrása a képen látható információkból
-- Hét: 2025.11.03 - 2025.11.07

-- Először a heti adatok
INSERT INTO `menu_weeks` (`start_date`, `end_date`, `group_type`, `location`) VALUES
('2025-11-03', '2025-11-07', 'Óvodás (4-6 év)', 'Himesháza');

SET @week_id = LAST_INSERT_ID();

-- Hétfő (2025.11.03)
INSERT INTO `menu_days` (
    `menu_week_id`, `day_name`, `day_date`, `day_order`,
    `tizorai_items`, `tizorai_allergens`, `tizorai_nutrition`,
    `ebed_items`, `ebed_allergens`, `ebed_nutrition`,
    `uzsonna_items`, `uzsonna_allergens`, `uzsonna_nutrition`,
    `total_nutrition`
) VALUES (
    @week_id, 'Hétfő', '2025-11-03', 1,
    JSON_ARRAY('Tej, karamellás', 'Sertés párizsi', 'Vajkrém', 'TK Fehérkenyér', 'Paradicsom'),
    'L, G, L, T, O, S, K',
    JSON_OBJECT('S', 0.9, 'E', 303.0, 'ZS', 11.8, 'T', 6.2, 'F', 10.3, 'CH', 36.9, 'C', 5.3, 'Ca', 139.5),
    JSON_ARRAY('Csibejó leves', 'Nudli morzsás'),
    'G, L, T, Z, M, G, T, L, O, S, K',
    JSON_OBJECT('S', 0.2, 'E', 452.0, 'ZS', 6.5, 'T', 0.1, 'F', 18.2, 'CH', 77.0, 'C', 23.7, 'Ca', 44.5),
    JSON_ARRAY('Fatörzs kifli', 'Alma'),
    'G, L, T',
    JSON_OBJECT('S', 0.6, 'E', 198.0, 'ZS', 3.9, 'T', 1.2, 'F', 4.7, 'CH', 27.8, 'C', 3.2, 'Ca', 11.0),
    JSON_OBJECT('S', 1.7, 'E', 953.0, 'ZS', 22.1, 'T', 7.5, 'F', 33.3, 'CH', 141.8, 'C', 32.3, 'Ca', 195.0)
);

-- Kedd (2025.11.04)
INSERT INTO `menu_days` (
    `menu_week_id`, `day_name`, `day_date`, `day_order`,
    `tizorai_items`, `tizorai_allergens`, `tizorai_nutrition`,
    `ebed_items`, `ebed_allergens`, `ebed_nutrition`,
    `uzsonna_items`, `uzsonna_allergens`, `uzsonna_nutrition`,
    `total_nutrition`
) VALUES (
    @week_id, 'Kedd', '2025-11-04', 2,
    JSON_ARRAY('Tea citromos', 'Tojáskrém', 'Fehér kenyér', 'Kígyóuborka'),
    'T, M, L, G, L, T, O, S, K',
    JSON_OBJECT('S', 0.5, 'E', 264.0, 'ZS', 4.1, 'T', 0.4, 'F', 7.5, 'CH', 47.3, 'C', 0.2, 'Ca', 60.4),
    JSON_ARRAY('Tarhonyaleves', 'Sertés aprópecsenye', 'Rizibizi'),
    'G, T, L, Z, M, G, L, T, Z, M',
    JSON_OBJECT('S', 0.1, 'E', 527.0, 'ZS', 23.3, 'T', 0.3, 'F', 23.4, 'CH', 55.0, 'C', 0.5, 'Ca', 32.2),
    JSON_ARRAY('Kocka sajt', 'TK Bagett'),
    'L, G, L',
    JSON_OBJECT('S', 0.6, 'E', 301.0, 'ZS', 14.7, 'T', 4.3, 'F', 13.4, 'CH', 28.1, 'C', 3.8, 'Ca', 495.0),
    JSON_OBJECT('S', 1.1, 'E', 1092.0, 'ZS', 42.2, 'T', 5.0, 'F', 44.2, 'CH', 130.4, 'C', 4.5, 'Ca', 587.6)
);

-- Szerda (2025.11.05)
INSERT INTO `menu_days` (
    `menu_week_id`, `day_name`, `day_date`, `day_order`,
    `tizorai_items`, `tizorai_allergens`, `tizorai_nutrition`,
    `ebed_items`, `ebed_allergens`, `ebed_nutrition`,
    `uzsonna_items`, `uzsonna_allergens`, `uzsonna_nutrition`,
    `total_nutrition`
) VALUES (
    @week_id, 'Szerda', '2025-11-05', 3,
    JSON_ARRAY('Tej', 'Vajas-Sajtos Kenyér', 'Paradicsom'),
    'L, G, L, T, O, S, K',
    JSON_OBJECT('S', 0.6, 'E', 241.0, 'ZS', 9.1, 'T', 4.1, 'F', 10.4, 'CH', 27.3, 'C', 4.9, 'Ca', 217.8),
    JSON_ARRAY('Zeller krémleves', 'Húsos tészta'),
    'G, L, T, Z, M, G, T, L, Z, M, O',
    JSON_OBJECT('S', 0.4, 'E', 599.0, 'ZS', 14.5, 'T', 1.5, 'F', 12.4, 'CH', 55.1, 'C', 6.4, 'Ca', 125.5),
    JSON_ARRAY('Csirkemell sonka', 'Delma Multivitaminos', 'TK Rozskenyér', 'Paprika'),
    'T, G, L, T, O, S, K',
    JSON_OBJECT('S', 0.7, 'E', 150.0, 'ZS', 5.8, 'T', 1.6, 'F', 6.5, 'CH', 16.2, 'C', 0.4, 'Ca', 16.0),
    JSON_OBJECT('S', 1.7, 'E', 990.0, 'ZS', 29.4, 'T', 7.2, 'F', 29.4, 'CH', 98.7, 'C', 11.7, 'Ca', 359.3)
);

-- Csütörtök (2025.11.06)
INSERT INTO `menu_days` (
    `menu_week_id`, `day_name`, `day_date`, `day_order`,
    `tizorai_items`, `tizorai_allergens`, `tizorai_nutrition`,
    `ebed_items`, `ebed_allergens`, `ebed_nutrition`,
    `uzsonna_items`, `uzsonna_allergens`, `uzsonna_nutrition`,
    `total_nutrition`
) VALUES (
    @week_id, 'Csütörtök', '2025-11-06', 4,
    JSON_ARRAY('Tea', 'Tonhalkrém', 'Fehér kenyér', 'Kígyóuborka'),
    'G, L, T, Z, H, O, D, G, L, T, O, S, K',
    JSON_OBJECT('S', 0.6, 'E', 262.0, 'ZS', 5.4, 'T', 0.6, 'F', 14.1, 'CH', 46.9, 'C', 0.1, 'Ca', 13.6),
    JSON_ARRAY('Gyümölcsleves', 'Vagdaltpogácsa sertéshúsból', 'Felesborsó főzelék'),
    'G, L, G, L, T, O, S, K, Z, M, G, L, T, Z, M',
    JSON_OBJECT('S', 0.1, 'E', 481.0, 'ZS', 5.5, 'T', 0.6, 'F', 6.2, 'CH', 51.1, 'C', 16.7, 'Ca', 45.7),
    JSON_ARRAY('Joghurt, gyümölcsös', 'TK Kifli'),
    'L, G, L',
    JSON_OBJECT('S', 0.5, 'E', 168.0, 'ZS', 0.8, 'T', 0.5, 'F', 5.9, 'CH', 26.9, 'C', 5.4, 'Ca', 85.0),
    JSON_OBJECT('S', 1.3, 'E', 911.0, 'ZS', 11.7, 'T', 1.6, 'F', 26.2, 'CH', 124.9, 'C', 22.2, 'Ca', 144.3)
);

-- Péntek (2025.11.07)
INSERT INTO `menu_days` (
    `menu_week_id`, `day_name`, `day_date`, `day_order`,
    `tizorai_items`, `tizorai_allergens`, `tizorai_nutrition`,
    `ebed_items`, `ebed_allergens`, `ebed_nutrition`,
    `uzsonna_items`, `uzsonna_allergens`, `uzsonna_nutrition`,
    `total_nutrition`
) VALUES (
    @week_id, 'Péntek', '2025-11-07', 5,
    JSON_ARRAY('Tea gyümölcsös', 'Vajas-Lekváros Kenyér'),
    'G, L, T, O, S, K',
    JSON_OBJECT('S', 0.5, 'E', 289.0, 'ZS', 5.0, 'T', 1.3, 'F', 4.5, 'CH', 53.8, 'C', 15.9, 'Ca', 13.0),
    JSON_ARRAY('Frankfurti leves', 'Rántott sajt', 'Zöldséges bulgur'),
    'T, O, L, G, Z, M, L, T, G',
    JSON_OBJECT('S', 0.7, 'E', 497.0, 'ZS', 18.5, 'T', 5.9, 'F', 20.0, 'CH', 60.5, 'C', 3.5, 'Ca', 312.0),
    JSON_ARRAY('Ausztria szalámi', 'Delma Multivitaminos', 'Vágott Zsemle', 'Paprika'),
    'G, L, T, O, S, K',
    JSON_OBJECT('S', 0.6, 'E', 234.0, 'ZS', 13.2, 'T', 4.5, 'F', 8.3, 'CH', 28.6, 'C', 0.2, 'Ca', 9.0),
    JSON_OBJECT('S', 1.8, 'E', 1020.0, 'ZS', 36.7, 'T', 11.7, 'F', 32.8, 'CH', 143.0, 'C', 19.6, 'Ca', 334.0)
);

