  CREATE DATABASE mini;
use mini;

CREATE TABLE users(
user_id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100) NOT NULL,
email VARCHAR(100) UNIQUE NOT NULL,
password VARCHAR (255) NOT NULL,
phone VARCHAR (20),
role ENUM('customer','staff','admin') NOT NULL DEFAULT 'customer',
created_at DATETIME DEFAULT CURRENT_TIMESTAMP
 
);


CREATE TABLE categories(
categories_id INT AUTO_INCREMENT PRIMARY KEY,
categories_name VARCHAR(100) NOT NULL,
description TEXT 
);

SELECT * FROM categories;



CREATE TABLE IF NOT EXISTS vehicles(
vehicle_id INT AUTO_INCREMENT PRIMARY KEY,
categories_id INT,
brand VARCHAR(100),
model VARCHAR(100),
year INT,
price_per_day DECIMAL(10,2),
status ENUM ('available','rented','maintenance') DEFAULT 'available',
image VARCHAR (200),
FOREIGN KEY (categories_id) REFERENCES categories(categories_id) 
);



CREATE TABLE IF NOT EXISTS rentals(
rental_id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT,
vehicle_id INT,
start_date DATE,
total_price DECIMAL(10,2),
status ENUM ('pending','confirmed','cancelled') DEFAULT 'pending',
created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(user_id) ,
FOREIGN KEY (vehicle_id) REFERENCES vehicles(vehicle_id) 
);

SHOW TABLES;

