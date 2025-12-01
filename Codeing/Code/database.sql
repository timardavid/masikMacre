-- Fitness Studio Database Schema
-- Created for dynamic content management

CREATE DATABASE IF NOT EXISTS fitness_studio;
USE fitness_studio;

-- Services table
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    icon VARCHAR(50) NOT NULL,
    features JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Classes table
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    schedule VARCHAR(255) NOT NULL,
    category ENUM('morning', 'evening', 'weekend') NOT NULL,
    icon VARCHAR(50) NOT NULL,
    max_participants INT DEFAULT 20,
    price DECIMAL(10,2),
    instructor VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- About section content
CREATE TABLE about_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    icon VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contact information
CREATE TABLE contact_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('phone', 'email', 'address', 'hours') NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    icon VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contact form submissions
CREATE TABLE contact_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    service VARCHAR(100),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Site settings
CREATE TABLE site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample data for services
INSERT INTO services (title, description, icon, features) VALUES
('Personal Training', 'One-on-one training sessions tailored to your specific goals and fitness level', '💪', '["Custom workout plans", "Technique improvement", "Motivational coaching", "Progress tracking"]'),
('Yoga & Pilates', 'Relaxing and strengthening classes for all levels', '🧘', '["Hatha yoga", "Vinyasa flow", "Pilates basics", "Meditation"]'),
('Group Classes', 'Energetic group sessions with motivating community', '🏃', '["HIIT training", "CrossFit", "Spinning", "Zumba"]');

-- Insert sample data for classes
INSERT INTO classes (title, description, schedule, category, icon, max_participants, price, instructor) VALUES
('Morning Yoga', 'Start your day with peaceful yoga practice', 'Monday, Wednesday, Friday 7:00-8:00 AM', 'morning', '🌅', 15, 25.00, 'Sarah Johnson'),
('HIIT Training', 'High-intensity interval training for maximum results', 'Tuesday, Thursday 6:00-7:00 PM', 'evening', '💪', 20, 30.00, 'Mike Chen'),
('Pilates', 'Core strengthening and flexibility improvement', 'Wednesday, Friday 8:00-9:00 AM', 'morning', '🧘', 12, 28.00, 'Emma Davis'),
('Spinning', 'High-energy cycling workout', 'Monday, Wednesday 7:00-8:00 PM', 'evening', '🏃', 25, 22.00, 'Alex Rodriguez'),
('Zumba', 'Dance fitness with Latin rhythms', 'Saturday 10:00-11:00 AM', 'weekend', '🤸', 30, 20.00, 'Maria Garcia'),
('CrossFit', 'Functional fitness and strength training', 'Sunday 9:00-10:00 AM', 'weekend', '🏋️', 18, 35.00, 'David Wilson');

-- Insert sample data for about content
INSERT INTO about_content (title, description, icon) VALUES
('Professional Trainers', 'Our experienced, certified trainers create personalized programs to help you reach your goals safely and effectively', '👨‍💼'),
('Flexible Schedule', 'Our classes run from 6 AM to 10 PM, so you can find the perfect time to fit fitness into your busy schedule', '⏰'),
('Holistic Approach', 'We focus not just on exercise, but also on healthy lifestyle, nutrition, and mental well-being', '🌱');

-- Insert sample data for contact info
INSERT INTO contact_info (type, title, content, icon) VALUES
('phone', 'Phone', '+1 (555) 123-4567', '📞'),
('email', 'Email', 'info@fitnessstudio.com', '📧'),
('address', 'Address', '123 Fitness Street<br>Health City, HC 12345', '📍'),
('hours', 'Hours', 'Mon-Fri: 6:00 AM - 10:00 PM<br>Sat-Sun: 8:00 AM - 8:00 PM', '🕒');

-- Insert sample data for site settings
INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_name', 'FitLife Studio'),
('site_tagline', 'Your Health Journey Starts Here'),
('site_description', 'Modern fitness and wellness center. Personal training, yoga classes, group workouts, and healthy lifestyle consulting with professional trainers.'),
('hero_title_line1', 'FitLife'),
('hero_title_line2', 'Studio'),
('hero_title_line3', 'Your Health Journey'),
('hero_description', 'Modern fitness and wellness center. Personal training, yoga classes, group workouts, and healthy lifestyle consulting with professional trainers.'),
('primary_color', '#22c55e'),
('secondary_color', '#16a34a'),
('accent_color', '#4ade80');
