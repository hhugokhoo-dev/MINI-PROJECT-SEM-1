-- DRIVORA car rental database (updated to match the browse page)
CREATE DATABASE IF NOT EXISTS mini;
USE mini;

-- If an old version of these tables already exists, drop them first (this deletes their data):
DROP TABLE IF EXISTS rentals;
DROP TABLE IF EXISTS vehicles;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE,                 -- minimum age 21
    ic_passport_no VARCHAR(30),         -- IC / Passport required at pick-up
    license_issue_date DATE,            -- license must be held at least 2 years
    role ENUM('customer','staff','admin') NOT NULL DEFAULT 'customer',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    categories_id INT AUTO_INCREMENT PRIMARY KEY,
    categories_name VARCHAR(100) NOT NULL,
    seats_label VARCHAR(50),            -- e.g. '2-4 seats'
    description TEXT
);

CREATE TABLE IF NOT EXISTS vehicles (
    vehicle_id INT AUTO_INCREMENT PRIMARY KEY,
    categories_id INT NOT NULL,
    brand VARCHAR(100) NOT NULL,
    model VARCHAR(100) NOT NULL,
    engine VARCHAR(150),                -- 1st spec line on the card
    transmission_drive VARCHAR(100),    -- 2nd spec line (gearbox / drivetrain)
    horsepower INT,                     -- hp
    acceleration_0_100 DECIMAL(3,1),    -- 0-100 km/h in seconds
    feature_tag VARCHAR(150),           -- first tag on the card
    slogan VARCHAR(150),                -- second tag on the card
    price_per_day DECIMAL(10,2) NOT NULL,
    status ENUM('available','rented','maintenance') NOT NULL DEFAULT 'available',
    image VARCHAR(500),
    FOREIGN KEY (categories_id) REFERENCES categories(categories_id)
);

CREATE TABLE IF NOT EXISTS rentals (
    rental_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_price DECIMAL(10,2),
    deposit DECIMAL(10,2) DEFAULT 0,    -- refundable security deposit
    status ENUM('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(vehicle_id),
    CHECK (end_date >= start_date)
);

-- Categories
INSERT INTO categories (categories_name, seats_label, description) VALUES
('Coupe', '2-4 seats', 'Sporty coupes and performance cars'),
('Sedan', '4-5 seaters', 'Luxury and executive sedans'),
('MPV', '4, 7 & 8 seats', 'Spacious premium people movers');

-- Coupe (categories_id = 1)
INSERT INTO vehicles (categories_id, brand, model, engine, transmission_drive, horsepower, acceleration_0_100, feature_tag, slogan, price_per_day, image) VALUES
(1, 'Lexus', 'LC500', '3.0L twin-turbo inline-6', '8-speed automatic', 471, 4.4, 'Naturally aspirated V8', 'Elegance in Motion.', 1199, 'https://hips.hearstapps.com/hmg-prod/images/2024-lexus-lc-500-convertible-117-655d765c34a2d.jpg?crop=0.764xw:0.647xh;0.115xw,0.262xh&resize=2048:*'),
(1, 'Mercedes-AMG', 'CLE 53 Coupe', '3.0L inline-6 with 48V mild hybrid', '4MATIC+ all-wheel drive', 443, 4.2, 'Turbo inline-6 hybrid', 'Born to Perform. Designed to Impress.', 1399, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQMohyqrPGthS0Ib0j3jGv4lMdZbWy9AOoZKw0OupzRTQ&s=10'),
(1, 'Porsche', '911 Carrera S', '3.0L twin-turbo flat-6', 'Rear-engine rear-wheel drive', 443, 3.5, 'Rear-engine layout', 'An Icon of Pure Performance.', 1599, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSoFrTiMlaRhvPo7Ow8iPiQ-irmamvNddk9vWmfcPCPbr1Kp_hzOWFQqok&s=10'),
(1, 'BMW', 'M4 CSL', '2.0L turbo inline-4', '8-speed automatic', 543, 3.7, 'Track-focused tuning', 'Uncompromising by Nature.', 1500, 'https://paultan.org/image/2022/05/G82-BMW-M4-CSL-debut-1-630x330.jpg'),
(1, 'Toyota', 'GT86', '2.0L flat-4', 'Rear-wheel drive', 228, 6.3, 'Nimble, lightweight handling', 'Feel Every Curve.', 899, 'https://hips.hearstapps.com/hmg-prod/images/2026-toyota-gr86-yuzu-special-edition-pr-102-67ed5cc10d1ce.jpg?crop=1.00xw:0.847xh;0,0.0695xh&resize=2048:*'),
(1, 'Nissan', 'GT-R', '3.8L twin-turbocharged V6', 'Dual-clutch transmission', 565, 2.9, 'Twin-turbo V6', 'Unleash the Legend.', 2099, 'https://paultan.org/image/2024/03/2025-Nissan-GT-R-Japan-launch-1-850x445.jpg'),
(1, 'Mini', 'Cooper John Cooper Works (JCW) Countryman', '2.0L TwinPower Turbocharged 4-cylinder petrol', 'Compact all-wheel drive', 301, 5.4, 'Turbo hot hatch', 'Big Adventures. Racing Spirit.', 888, 'https://assets.autobuzz.my/wp-content/uploads/2023/11/09124322/MINI-John-Cooper-Works-Countryman-all-new-debut.jpg'),
(1, 'Mercedes-Benz', 'GLE53 Coupe', '3.0L inline-6 with 48V mild hybrid', '4MATIC+ all-wheel drive', 429, 5.2, 'Turbo inline-6 mild hybrid', 'Power Meets Prestige.', 1599, 'https://paultan.org/image/2023/11/Mercedes-Benz-GLE53-4Matic-Coupe-Malaysia-1-1200x639.jpg');

-- Sedan (categories_id = 2)
INSERT INTO vehicles (categories_id, brand, model, engine, transmission_drive, horsepower, acceleration_0_100, feature_tag, slogan, price_per_day, image) VALUES
(2, 'Mercedes-Benz', 'S450', '3.0L turbocharged inline-6 petrol', 'Rear-wheel drive', 380, 5.0, 'Flagship luxury sedan', 'The Standard of Supreme Luxury', 1599, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRY_y4KfEhVkKJqJRoW8AwVjFPq2bHuZU-3aqvGEuyAJw&s=10'),
(2, 'BMW', '740i xDrive', '3.0L turbocharged inline-6 petrol', 'xDrive all-wheel drive', 381, 5.2, 'Full-size flagship sedan', 'The Art of Executive Excellence.', 1599, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSAFkk0fK4ILDzBxTYLKy0t4AJqL4Qo_vNWbZNxVVJCAw&s=10'),
(2, 'Audi', 'A8L 55 TFSI', '3.0L turbocharged V6 petrol', 'Quattro all-wheel drive', 340, 5.7, 'Long-wheelbase executive limousine', 'Elegance in Every Detail.', 1599, 'https://vtpimages.audi.com/carimg2/3728/4581563728.jpg?im=Resize,width=640,height=480'),
(2, 'Porsche', 'Panamera', '2.9L twin-turbo V6 petrol', 'Rear-wheel drive', 353, 5.3, 'Sports car handling in sedan form', 'Where Performance Meets Prestige.', 1999, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcROU_4gWJfcoprbdvVJEZ5NVosE5Fn3_nvAFsjQcBcd5w&s=10'),
(2, 'Lexus', 'LS500', '3.4L twin-turbo V6 petrol', 'Rear-wheel drive', 415, 5.0, 'Handcrafted Takumi interior', 'The Serenity of True Luxury.', 1699, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT3BBFpcYkoyrcTTEIs8B5D0lsfcKWHoNxe6UYbTEvBbg&s=10'),
(2, 'Maserati', 'Ghibli Modena Ultima Q4', '3.0L twin-turbo V6 petrol', 'All-wheel drive', 424, 4.9, 'Italian sports luxury heritage', 'Italian Passion. Unmistakable Presence.', 1500, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR9uK1h0CfaIcnt_l2cXszXmouNTOTu_t3qU4NMuGdPaAFQJh3GAgBvhog&s=10'),
(2, 'Volvo', 'S90 B5 Ultimate', '2.0L turbocharged mild-hybrid petrol', 'Front-wheel drive', 250, 7.1, 'Scandinavian minimalist design', 'Scandinavian Elegance, Redefined.', 1200, 'https://www.ccarprice.com/products/Volvo_S90_2026_3.jpg'),
(2, 'BMW', '540i xDrive M Sport', '3.0L turbocharged inline-6 petrol', 'xDrive all-wheel drive', 381, 4.6, 'Performance executive sedan', 'The Thrill of Refined Power.', 950, 'https://bmw.scene7.com/is/image/BMW/g60-ice-stage-ext-dsk-sl?qlt=80&wid=1024&fmt=webp');

-- MPV (categories_id = 3)
INSERT INTO vehicles (categories_id, brand, model, engine, transmission_drive, horsepower, acceleration_0_100, feature_tag, slogan, price_per_day, image) VALUES
(3, 'Toyota', 'Alphard', '2.5L Dynamic Force 4-cylinder petrol', 'Front-wheel drive', 184, 9.8, 'Chauffeur-driven luxury MPV', 'Arrive in Absolute Comfort.', 999, 'https://www.automachi.com/wp-content/uploads/2024/01/Alphard-Recon-1.jpg'),
(3, 'Toyota', 'Vellfire', '2.5L Dynamic Force 4-cylinder petrol', 'Front-wheel drive', 184, 9.8, 'Premium executive lounge seating', 'Bold Design. First-Class Comfort.', 999, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ5n3q_dntsLVe9u0Ul9BSUippeCEChBaDiAhEu7GJq9g&s=10'),
(3, 'Lexus', 'LM 350h', '2.5L petrol-hybrid 4-cylinder', 'All-wheel drive', 250, 9.6, 'First-class private suite seating', 'Your Private World of Luxury.', 1799, 'https://www.lexus.com.my/content/dam/lexus-v3-blueprint/models/mpv/lm/mlp/my24/teaser/gallery/my24-lm-gallery-ext-02-d.jpg'),
(3, 'Xpeng', 'X9 Ultra', 'Dual-motor all-electric powertrain', 'All-wheel drive', 469, 3.9, 'Rear-wheel steering', 'The Future of First-Class Travel.', 900, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT9LHpgB7mSIUede_SP0tMITHGQMzSu0zuunnd-T0dJ5USoCN4nowYQr5U&s=10'),
(3, 'Zeekr', '009', 'Dual-motor all-electric powertrain', 'All-wheel drive', 536, 4.5, 'Flagship business MPV', 'Electrifying Luxury. Limitless Comfort.', 950, 'https://assets.autobuzz.my/wp-content/uploads/sites/2/2024/07/23194225/zeekr-009-2-696x392-1.jpg'),
(3, 'Kia', 'Carnival Signature', '3.5L V6 petrol', 'Front-wheel drive', 277, 8.2, 'Captain''s chairs with premium trim', 'More Space. More Moments.', 500, 'https://paultan.org/image/2020/08/2020-Kia-Grand-Carnival-1-e1597750446802-1200x676.jpg'),
(3, 'Hyundai', 'Staria Premium', '2.5L turbocharged GDi 4-cylinder petrol', 'Front-wheel drive', 213, 9.5, 'Futuristic spaceship-inspired design', 'A New Dimension of Travel.', 450, 'https://paultan.org/image/2021/03/Hyundai-Staria-1-e1616032407850.jpg'),
(3, 'Denza', 'D9 EV', 'Dual-motor all-electric powertrain', 'All-wheel drive', 435, 5.9, 'BYD e-platform architecture', 'Where Innovation Embraces Luxury.', 850, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRpJox-CIzTMnycUxyUUkiHuAhDIDnoUoFtlBHoUMNH9g&s=10');

-- Check
SHOW TABLES;
SELECT c.categories_name, COUNT(*) AS cars
FROM vehicles v JOIN categories c ON c.categories_id = v.categories_id
GROUP BY c.categories_name;