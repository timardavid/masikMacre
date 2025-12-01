-- Naptár alkalmazás adatbázis struktúra

CREATE DATABASE IF NOT EXISTS naptar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE naptar;

CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_date DATE NOT NULL,
    task_text TEXT NOT NULL,
    completed TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_task_date (task_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


