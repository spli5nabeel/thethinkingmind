-- User Management System Schema
-- Add users table and update existing tables

USE exam_simulator;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'student') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
);

-- Add user_id to exam_results table
ALTER TABLE exam_results 
ADD COLUMN user_id INT NULL AFTER id,
ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;

-- Create default admin user (password: admin123)
INSERT INTO users (username, email, password_hash, full_name, role) 
VALUES ('admin', 'admin@example.com', '$2y$10$YourHashedPasswordHere', 'Administrator', 'admin')
ON DUPLICATE KEY UPDATE username=username;

-- Create a sample student user (password: student123)
INSERT INTO users (username, email, password_hash, full_name, role) 
VALUES ('student', 'student@example.com', '$2y$10$YourHashedPasswordHere', 'Sample Student', 'student')
ON DUPLICATE KEY UPDATE username=username;

-- User sessions table for better session management
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- User activity log
CREATE TABLE IF NOT EXISTS user_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity_type ENUM('login', 'logout', 'exam_start', 'exam_complete', 'profile_update') NOT NULL,
    activity_details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
