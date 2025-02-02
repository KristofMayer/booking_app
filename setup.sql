-- setup.sql

-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS booking_app
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

-- Select the database
USE booking_app;

-- Create the staff table
CREATE TABLE IF NOT EXISTS staff (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create the services table
CREATE TABLE IF NOT EXISTS services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  service_date DATE NOT NULL,
  service_type ENUM('breakfast','lunch','dinner') NOT NULL,
  available_seats INT NOT NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY service_unique (service_date, service_type)
);

-- Create the bookings table
CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_date DATE NOT NULL,
  service_type ENUM('breakfast','lunch','dinner') NOT NULL,
  party_size TINYINT NOT NULL,
  customer_name VARCHAR(100),
  customer_phone VARCHAR(20),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
