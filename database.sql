-- Practice Exam Simulator Database Schema
-- Created: February 2026

CREATE DATABASE IF NOT EXISTS exam_simulator;
USE exam_simulator;

-- Table for storing exam questions
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_text TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_answer ENUM('A', 'B', 'C', 'D') NOT NULL,
    category VARCHAR(100) DEFAULT 'General',
    difficulty ENUM('Easy', 'Medium', 'Hard') DEFAULT 'Medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for storing exam results
CREATE TABLE IF NOT EXISTS exam_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    total_questions INT NOT NULL,
    correct_answers INT NOT NULL,
    score_percentage DECIMAL(5,2) NOT NULL,
    exam_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for storing detailed answers
CREATE TABLE IF NOT EXISTS exam_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    result_id INT NOT NULL,
    question_id INT NOT NULL,
    user_answer CHAR(1),
    is_correct BOOLEAN,
    FOREIGN KEY (result_id) REFERENCES exam_results(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

-- Table for storing category metadata
CREATE TABLE IF NOT EXISTS category_metadata (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) UNIQUE NOT NULL,
    category_type ENUM('IT', 'Academic', 'Science', 'Language', 'Other') DEFAULT 'Academic',
    description TEXT,
    icon VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default category metadata
INSERT INTO category_metadata (category_name, category_type, icon) VALUES
('PHP Basics', 'IT', '🔵'),
('Database', 'IT', '🗄️'),
('Functions', 'IT', '⚙️'),
('Forms', 'IT', '📝'),
('Operators', 'Academic', '➗'),
('String Functions', 'IT', '📄'),
('Arrays', 'IT', '📊'),
('OOP', 'IT', '🎯'),
('Security', 'IT', '🔒'),
('General', 'Academic', '📖'),
('KCSA', 'IT', '☁️'),
('Maths', 'Academic', '🔢'),
('Python', 'IT', '🐍')
ON DUPLICATE KEY UPDATE category_type=VALUES(category_type), icon=VALUES(icon);

-- Insert sample questions
INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_answer, category, difficulty) VALUES
('What does PHP stand for?', 'Personal Home Page', 'Hypertext Preprocessor', 'Private Home Page', 'Public Hypertext Processor', 'B', 'PHP Basics', 'Easy'),
('Which symbol is used to declare a variable in PHP?', '@', '#', '$', '&', 'C', 'PHP Basics', 'Easy'),
('What is the correct way to end a PHP statement?', '.', ';', ':', ',', 'B', 'PHP Basics', 'Easy'),
('Which function is used to connect to MySQL database in PHP?', 'mysql_connect()', 'mysqli_connect()', 'db_connect()', 'connect_mysql()', 'B', 'Database', 'Medium'),
('What is the correct way to create a function in PHP?', 'function myFunction()', 'create myFunction()', 'def myFunction()', 'new function myFunction()', 'A', 'Functions', 'Easy'),
('Which superglobal is used to collect form data in PHP?', '$_GET', '$_POST', '$_REQUEST', 'All of the above', 'D', 'Forms', 'Medium'),
('What does SQL stand for?', 'Structured Query Language', 'Simple Query Language', 'Strong Question Language', 'Standard Query Language', 'A', 'Database', 'Easy'),
('Which operator is used for concatenation in PHP?', '+', '&', '.', '*', 'C', 'Operators', 'Easy'),
('What is the correct way to include a file in PHP?', 'include "file.php"', 'import "file.php"', 'require "file.php"', 'Both A and C', 'D', 'PHP Basics', 'Medium'),
('Which function is used to get the length of a string in PHP?', 'length()', 'strlen()', 'str_length()', 'size()', 'B', 'String Functions', 'Easy');
