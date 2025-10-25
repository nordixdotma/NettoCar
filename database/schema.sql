-- NETTOCAR Database Schema
CREATE DATABASE IF NOT EXISTS nettocar;
USE nettocar;

-- Packs Table
CREATE TABLE packs (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL UNIQUE,
  reservation_limit_per_day INT,
  statistics_enabled BOOLEAN DEFAULT FALSE,
  csv_export_enabled BOOLEAN DEFAULT FALSE
);

-- Users Table
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin', 'agency', 'client') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Agencies Table
CREATE TABLE agencies (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  address VARCHAR(255),
  opening_hours VARCHAR(100),
  pack_id INT NOT NULL,
  owner_user_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (pack_id) REFERENCES packs(id),
  FOREIGN KEY (owner_user_id) REFERENCES users(id)
);

-- Services Table
CREATE TABLE services (
  id INT PRIMARY KEY AUTO_INCREMENT,
  agency_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  price DECIMAL(10, 2) NOT NULL,
  estimated_duration_minutes INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (agency_id) REFERENCES agencies(id) ON DELETE CASCADE
);

-- Reservations Table
CREATE TABLE reservations (
  id INT PRIMARY KEY AUTO_INCREMENT,
  agency_id INT NOT NULL,
  service_id INT NOT NULL,
  client_user_id INT NOT NULL,
  datetime DATETIME NOT NULL,
  status ENUM('waiting', 'in_progress', 'finished') DEFAULT 'waiting',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (agency_id) REFERENCES agencies(id),
  FOREIGN KEY (service_id) REFERENCES services(id),
  FOREIGN KEY (client_user_id) REFERENCES users(id)
);

-- Payments Table
CREATE TABLE payments (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  pack_id INT NOT NULL,
  amount DECIMAL(10, 2) NOT NULL,
  date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  method VARCHAR(50),
  status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (pack_id) REFERENCES packs(id)
);

-- Insert Default Packs
INSERT INTO packs (name, reservation_limit_per_day, statistics_enabled, csv_export_enabled) VALUES
('Basic', 10, FALSE, FALSE),
('Standard', NULL, TRUE, FALSE),
('Premium', NULL, TRUE, TRUE);
