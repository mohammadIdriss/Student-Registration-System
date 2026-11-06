-- Campus Registry database
-- Import this file in phpMyAdmin or with the MySQL command-line client.

CREATE DATABASE IF NOT EXISTS student_registration_system
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE student_registration_system;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    address TEXT NOT NULL,
    profile_picture VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_students_name (name),
    INDEX idx_students_email (email),
    INDEX idx_students_phone (phone)
) ENGINE=InnoDB;
