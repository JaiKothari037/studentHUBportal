CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student'
);

-- Passwords are set using password_hash('password123', PASSWORD_DEFAULT)
INSERT INTO users (username, email, password, role) VALUES
('student_user', 'student@gmail.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe115L9z6S9A.V/jO5w0fQx5yqO1L3Q.m', 'student'),
('admin_user', 'admin@gmail.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe115L9z6S9A.V/jO5w0fQx5yqO1L3Q.m', 'admin')
ON DUPLICATE KEY UPDATE role=VALUES(role);