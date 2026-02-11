-- Database schema for the library log system.
-- Database: library_logs
CREATE DATABASE IF NOT EXISTS library_logs;
USE library_logs;

-- Users table (for admin login).
CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- District reference table.
CREATE TABLE IF NOT EXISTS districts (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- School reference table linked to district.
CREATE TABLE IF NOT EXISTS schools (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    district_id INT(11) NOT NULL,
    name VARCHAR(150) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_district_school (district_id, name),
    CONSTRAINT fk_schools_district
        FOREIGN KEY (district_id) REFERENCES districts(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

-- Logbook entries table normalized for district/school lookups.
CREATE TABLE IF NOT EXISTS logbook_entries (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    time_in TIME NOT NULL,
    name VARCHAR(100) NOT NULL,
    client_type VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    district_id INT(11) NULL,
    school_id INT(11) NULL,
    organization_name VARCHAR(150) NULL,
    purpose TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_logbook_date (date),
    INDEX idx_logbook_client_type (client_type),
    INDEX idx_logbook_name (name),
    INDEX idx_logbook_district (district_id),
    INDEX idx_logbook_school (school_id),
    CONSTRAINT fk_logbook_district
        FOREIGN KEY (district_id) REFERENCES districts(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_logbook_school
        FOREIGN KEY (school_id) REFERENCES schools(id)
        ON UPDATE CASCADE ON DELETE SET NULL
);

-- Insert default admin user (username: admin, password: admin123).
INSERT INTO users (username, password, role) VALUES
('admin', '$2y$10$xlIUsUGaB8JbN4eDTwXBnOaOVEofFYeEi4yzewPoOy4TX2F/p/IXK', 'admin')
ON DUPLICATE KEY UPDATE username = VALUES(username);

-- Seed districts used by the public form.
INSERT INTO districts (name) VALUES
('Maasin City District'),
('Bontoc I District'),
('Bontoc II District'),
('Hinunangan District'),
('Hinundayan District'),
('Sogod District'),
('Libagon District'),
('Limasawa District'),
('Macrohon District'),
('Malitbog District'),
('Padre Burgos District'),
('Pintuyan District'),
('San Francisco District'),
('San Juan District'),
('Anahawan District'),
('Silago District'),
('St. Bernard District'),
('Tomas Oppus District')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Seed sample schools per district (replace with official master list as needed).
INSERT INTO schools (district_id, name)
SELECT d.id, s.school_name
FROM districts d
JOIN (
    SELECT 'Maasin City District' AS district_name, 'Maasin City Central School' AS school_name UNION ALL
    SELECT 'Maasin City District', 'Maasin City National High School' UNION ALL
    SELECT 'Bontoc I District', 'Bontoc I Central School' UNION ALL
    SELECT 'Bontoc I District', 'Bontoc I National High School' UNION ALL
    SELECT 'Bontoc II District', 'Bontoc II Central School' UNION ALL
    SELECT 'Bontoc II District', 'Bontoc II National High School' UNION ALL
    SELECT 'Hinunangan District', 'Hinunangan Central School' UNION ALL
    SELECT 'Hinunangan District', 'Hinunangan National High School' UNION ALL
    SELECT 'Hinundayan District', 'Hinundayan Central School' UNION ALL
    SELECT 'Hinundayan District', 'Hinundayan National High School' UNION ALL
    SELECT 'Sogod District', 'Sogod Central School' UNION ALL
    SELECT 'Sogod District', 'Sogod National High School' UNION ALL
    SELECT 'Libagon District', 'Libagon Central School' UNION ALL
    SELECT 'Libagon District', 'Libagon National High School' UNION ALL
    SELECT 'Limasawa District', 'Limasawa Central School' UNION ALL
    SELECT 'Limasawa District', 'Limasawa National High School' UNION ALL
    SELECT 'Macrohon District', 'Macrohon Central School' UNION ALL
    SELECT 'Macrohon District', 'Macrohon National High School' UNION ALL
    SELECT 'Malitbog District', 'Malitbog Central School' UNION ALL
    SELECT 'Malitbog District', 'Malitbog National High School' UNION ALL
    SELECT 'Padre Burgos District', 'Padre Burgos Central School' UNION ALL
    SELECT 'Padre Burgos District', 'Padre Burgos National High School' UNION ALL
    SELECT 'Pintuyan District', 'Pintuyan Central School' UNION ALL
    SELECT 'Pintuyan District', 'Pintuyan National High School' UNION ALL
    SELECT 'San Francisco District', 'San Francisco Central School' UNION ALL
    SELECT 'San Francisco District', 'San Francisco National High School' UNION ALL
    SELECT 'San Juan District', 'San Juan Central School' UNION ALL
    SELECT 'San Juan District', 'San Juan National High School' UNION ALL
    SELECT 'Anahawan District', 'Anahawan Central School' UNION ALL
    SELECT 'Anahawan District', 'Anahawan National High School' UNION ALL
    SELECT 'Silago District', 'Silago Central School' UNION ALL
    SELECT 'Silago District', 'Silago National High School' UNION ALL
    SELECT 'St. Bernard District', 'St. Bernard Central School' UNION ALL
    SELECT 'St. Bernard District', 'St. Bernard National High School' UNION ALL
    SELECT 'Tomas Oppus District', 'Tomas Oppus Central School' UNION ALL
    SELECT 'Tomas Oppus District', 'Tomas Oppus National High School'
) s
    ON s.district_name = d.name
ON DUPLICATE KEY UPDATE name = VALUES(name);
