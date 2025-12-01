-- Company Admin Dashboard Database
CREATE DATABASE IF NOT EXISTS company_dashboard CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci;
USE company_dashboard;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'IT', 'HR', 'Finance', 'CEO', 'Accountant', 'Financial Advisor', 'Cleaner', 'Receptionist', 'Secretary') NOT NULL,
    department VARCHAR(50),
    phone VARCHAR(20),
    salary DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Work status table
CREATE TABLE work_status (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    status ENUM('working', 'break', 'vacation', 'sick_leave', 'no_work') DEFAULT 'no_work',
    start_time DATETIME,
    end_time DATETIME,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tasks table
CREATE TABLE tasks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    client_name VARCHAR(100),
    task_title VARCHAR(200) NOT NULL,
    description TEXT,
    priority ENUM('Critical', 'Very Urgent', 'Urgent', 'Not Urgent') DEFAULT 'Not Urgent',
    status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
    deadline DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Work hours tracking
CREATE TABLE work_hours (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    date DATE NOT NULL,
    hours_worked DECIMAL(4,2) DEFAULT 0,
    break_hours DECIMAL(4,2) DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_date (user_id, date)
);

-- Company financial data
CREATE TABLE revenue (
    id INT PRIMARY KEY AUTO_INCREMENT,
    amount DECIMAL(12,2) NOT NULL,
    date DATE NOT NULL,
    source VARCHAR(200),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products/Services
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(100),
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Monthly financial summary
CREATE TABLE monthly_summary (
    id INT PRIMARY KEY AUTO_INCREMENT,
    month VARCHAR(7) NOT NULL,
    total_revenue DECIMAL(12,2) DEFAULT 0,
    total_expenses DECIMAL(12,2) DEFAULT 0,
    total_salary DECIMAL(12,2) DEFAULT 0,
    profit DECIMAL(12,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_month (month)
);

-- Insert sample data
INSERT INTO users (name, email, password, role, department, phone, salary) VALUES
('Admin Felhasználó', 'admin@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'IT', '1234567890', 800000),
('IT Szakember', 'it@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'IT', 'IT', '0987654321', 650000),
('HR Menedzser', 'hr@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'HR', 'HR', '1122334455', 700000),
('Pénzügyes', 'finance@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Finance', 'Finance', '5566778899', 750000),
('Ügyvezető', 'ceo@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'CEO', 'Management', '9988776655', 1500000),
('Dolgozó Péter', 'peter@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'IT', 'IT', '5544332211', 600000),
('Kovács Anna', 'kovacs.anna@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Financial Advisor', 'Pénzügyi Tanácsadás', '0612345678', 850000),
('Nagy István', 'nagy.istvan@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Financial Advisor', 'Pénzügyi Tanácsadás', '0623456789', 820000),
('Szabó Mária', 'szabo.maria@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Financial Advisor', 'Pénzügyi Tanácsadás', '0634567890', 800000),
('Horváth Ferenc', 'horvath.ferenc@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Accountant', 'Pénzügy', '0645678901', 750000),
('Tóth Eszter', 'toth.eszter@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Accountant', 'Pénzügy', '0656789012', 720000),
('Varga Péter', 'varga.peter@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'IT', 'IT', '0667890123', 620000),
('Kiss Andrea', 'kiss.andrea@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'HR', 'HR', '0678901234', 580000),
('Molnár Gábor', 'molnar.gabor@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Financial Advisor', 'Pénzügyi Tanácsadás', '0689012345', 830000),
('Farkas Emese', 'farkas.emese@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Secretary', 'Adminisztráció', '0690123456', 480000),
('Váradi Zoltán', 'varadi.zoltan@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'IT', 'IT', '0610123456', 630000),
('Balogh Csilla', 'balogh.csilla@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Accountant', 'Pénzügy', '0621234567', 700000),
('Takács Márton', 'takacs.marton@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Financial Advisor', 'Pénzügyi Tanácsadás', '0632345678', 840000),
('Németh Anita', 'nemeth.anita@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'HR', 'HR', '0643456789', 590000),
('Papp Róbert', 'papp.robert@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Financial Advisor', 'Pénzügyi Tanácsadás', '0654567890', 810000),
('László Orsolya', 'laszlo.orsolya@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Accountant', 'Pénzügy', '0665678901', 680000),
('Gál Tamás', 'gal.tamas@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'IT', 'IT', '0676789012', 610000),
('Márton Katalin', 'marton.katalin@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cleaner', 'Karbantartás', '0687890123', 380000),
('Simon Dániel', 'simon.daniel@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Financial Advisor', 'Pénzügyi Tanácsadás', '0698901234', 825000),
('Kovács Barbara', 'kovacs.barbara@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Receptionist', 'Recepció', '0619012345', 420000),
('Nagy Mihály', 'nagy.mihaly@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'IT', 'IT', '0620123456', 640000),
('Szabó Gabriella', 'szabo.gabriella@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'HR', 'HR', '0631234567', 570000),
('Horváth Ádám', 'horvath.adam@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Accountant', 'Pénzügy', '0642345678', 690000),
('Tóth Réka', 'toth.reka@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Financial Advisor', 'Pénzügyi Tanácsadás', '0653456789', 835000),
('Varga Benedek', 'varga.benedek@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cleaner', 'Karbantartás', '0664567890', 370000),
('Kiss Bálint', 'kiss.balint@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Secretary', 'Adminisztráció', '0675678901', 450000);

-- Insert sample work status
INSERT INTO work_status (user_id, status, start_time) VALUES
(2, 'working', NOW()),
(3, 'working', NOW()),
(4, 'break', NOW()),
(5, 'vacation', DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Insert sample tasks
INSERT INTO tasks (user_id, client_name, task_title, description, priority, status, deadline) VALUES
(2, 'ABC Kft', 'Webszerver javítás', 'Apache konfiguráció frissítése', 'Critical', 'in_progress', DATE_ADD(NOW(), INTERVAL 2 DAY)),
(3, 'XYZ Zrt', 'Új dolgozó felvétel', 'IT állás betöltése dokumentáció', 'Urgent', 'pending', DATE_ADD(NOW(), INTERVAL 5 DAY)),
(4, 'LMN Kft', 'Havi zárás', 'Januári pénzügyi zárás', 'Very Urgent', 'in_progress', DATE_ADD(NOW(), INTERVAL 1 DAY)),
(2, 'PQR Bt', 'Adatbázis backup', 'Heti adatbázis mentés tesztelése', 'Not Urgent', 'pending', DATE_ADD(NOW(), INTERVAL 7 DAY));

-- Insert sample products/services
INSERT INTO products (name, price, category, description, status) VALUES
('Könyvvizsgálati Szolgáltatás', 500000, 'Pénzügy', 'Teljes éves könyvvizsgálás', 'active'),
('Adótanácsadás', 150000, 'Pénzügy', 'Éves adóoptimalizálási tanácsadás', 'active'),
('Pénzügyi Terv Készítés', 300000, 'Pénzügy', 'Részletes pénzügyi elemzés és javaslatok', 'active'),
('Számviteli Szolgáltatás', 250000, 'Pénzügy', 'Havi számviteli végelemzés', 'active'),
('Vállalkozói Tanácsadás', 400000, 'Tanácsadás', 'Teljes üzleti elemzés és stratégia', 'active'),
('IT Támogatás', 200000, 'IT', 'Havi technikai támogatás', 'active'),
('Webfejlesztés', 800000, 'IT', 'Egyedi weboldal fejlesztés', 'active'),
('Adatbázis Karbantartás', 150000, 'IT', 'Havi adatbázis mentés és karbantartás', 'active');

-- Insert sample revenue data
INSERT INTO revenue (amount, date, source, notes) VALUES
(500000, CURDATE(), 'ABC Kft - Könyvvizsgálat', 'Éves könyvvizsgálati szolgáltatás'),
(300000, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'XYZ Zrt - Pénzügyi Terv', 'Éves pénzügyi elemzés'),
(150000, DATE_SUB(CURDATE(), INTERVAL 7 DAY), 'LMN Kft - Adótanácsadás', 'Adóoptimalizálási terv'),
(800000, DATE_SUB(CURDATE(), INTERVAL 10 DAY), 'PQR Bt - Webfejlesztés', 'Egyedi weboldal projekt'),
(200000, DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'DEF Kft - IT Támogatás', 'Havi támogatás'),
(250000, DATE_SUB(CURDATE(), INTERVAL 12 DAY), 'GHI Zrt - Számvitel', 'Havi számviteli végelemzés'),
(400000, DATE_SUB(CURDATE(), INTERVAL 8 DAY), 'JKL Bt - Tanácsadás', 'Üzleti stratégiai tanácsadás'),
(150000, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'MNO Kft - Adatbázis', 'Havi adatbázis karbantartás'),
(500000, DATE_SUB(CURDATE(), INTERVAL 14 DAY), 'PQR Zrt - Könyvvizsgálat', 'Éves könyvvizsgálat projekt'),
(300000, DATE_SUB(CURDATE(), INTERVAL 9 DAY), 'STU Bt - Pénzügyi Terv', 'Éves pénzügyi elemzés');

-- Calculate and insert monthly summary
INSERT INTO monthly_summary (month, total_revenue, total_salary, profit)
SELECT 
    DATE_FORMAT(CURDATE(), '%Y-%m') as month,
    COALESCE(SUM(r.amount), 0) as total_revenue,
    COALESCE(SUM(u.salary), 0) as total_salary,
    COALESCE(SUM(r.amount), 0) - COALESCE(SUM(u.salary), 0) as profit
FROM (SELECT 1 as dummy) d
LEFT JOIN revenue r ON DATE_FORMAT(r.date, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
LEFT JOIN users u ON u.status = 'active'
ON DUPLICATE KEY UPDATE 
    total_revenue = (SELECT COALESCE(SUM(amount), 0) FROM revenue WHERE DATE_FORMAT(date, '%Y-%m') = month),
    total_salary = (SELECT COALESCE(SUM(salary), 0) FROM users WHERE status = 'active'),
    profit = total_revenue - total_salary;
