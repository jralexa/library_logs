-- Database: liblogs_db
CREATE DATABASE IF NOT EXISTS liblogs_db;
USE liblogs_db;

-- Users table (for admin login)
CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Logbook entries table (matched to index.php and dashboard.php)
CREATE TABLE IF NOT EXISTS logbook_entries (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    time_in TIME NOT NULL,
    name VARCHAR(100) NOT NULL,
    client_type VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    district VARCHAR(100) NOT NULL,
    purpose TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_logbook_date (date),
    INDEX idx_logbook_client_type (client_type),
    INDEX idx_logbook_name (name)
);

-- Insert default admin user (username: admin, password: admin123)
-- Note: Using the hash provided in your original file
INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$10$xlIUsUGaB8JbN4eDTwXBnOaOVEofFYeEi4yzewPoOy4TX2F/p/IXK', 'admin');
