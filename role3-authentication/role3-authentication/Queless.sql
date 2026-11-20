CREATE DATABASE IF NOT EXISTS queueless_ghana_db;
USE queueless_ghana_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    phone VARCHAR(25) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'stylist', 'admin') NOT NULL DEFAULT 'customer',
    shop_name VARCHAR(150) DEFAULT NULL,
    shop_type ENUM('Barbershop', 'Salon', 'Unisex Salon') DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);