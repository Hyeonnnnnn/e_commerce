-- Create database if not exists
CREATE DATABASE IF NOT EXISTS computer_shop_db;
USE computer_shop_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT NOT NULL,
    tax_rate DECIMAL(5,2) DEFAULT 12.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Transactions table
CREATE TABLE IF NOT EXISTS transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) NOT NULL,
    tender_amount DECIMAL(10,2) NOT NULL,
    change_amount DECIMAL(10,2) NOT NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Transaction details table
CREATE TABLE IF NOT EXISTS transaction_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    transaction_id INT,
    product_id INT,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Audit log table
CREATE TABLE IF NOT EXISTS audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action_type VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert default admin user if not exists
INSERT INTO users (username, email, password, role)
SELECT 'admin', 'admin@shop.com', 'admin123', 'admin'
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE username = 'admin'
);


-- Insert comprehensive product list
INSERT INTO products (id, name, category, description, price, stock_quantity, tax_rate) VALUES 
-- CPUs
(1, 'Intel Core i9-13900K', 'CPU', '24-Core (8P+16E) Desktop Processor, Up to 5.8 GHz', 34999.00, 5, 12.00),
(2, 'AMD Ryzen 9 7950X', 'CPU', '16-Core, 32-Thread Processor, Up to 5.7 GHz', 32999.00, 8, 12.00),
(3, 'Intel Core i7-13700K', 'CPU', '16-Core (8P+8E) Desktop Processor, Up to 5.4 GHz', 27999.00, 10, 12.00),
(4, 'AMD Ryzen 7 7800X3D', 'CPU', '8-Core, 16-Thread Processor with 3D V-Cache', 25999.00, 12, 12.00),

-- GPUs
(5, 'NVIDIA RTX 4090', 'GPU', 'NVIDIA GeForce RTX 4090 24GB GDDR6X', 89999.00, 3, 12.00),
(6, 'AMD RX 7900 XTX', 'GPU', 'AMD Radeon RX 7900 XTX 24GB GDDR6', 59999.00, 4, 12.00),
(7, 'NVIDIA RTX 4080', 'GPU', 'NVIDIA GeForce RTX 4080 16GB GDDR6X', 69999.00, 5, 12.00),
(8, 'NVIDIA RTX 4070 Ti', 'GPU', 'NVIDIA GeForce RTX 4070 Ti 12GB GDDR6X', 49999.00, 6, 12.00),

-- RAM
(9, 'Corsair Dominator 32GB', 'RAM', 'Corsair Dominator Platinum RGB DDR5 6000MHz (2x16GB)', 12999.00, 15, 12.00),
(10, 'G.Skill Trident Z5 32GB', 'RAM', 'G.Skill Trident Z5 RGB DDR5 6400MHz (2x16GB)', 13999.00, 10, 12.00),
(11, 'Crucial 64GB', 'RAM', 'Crucial DDR5 5600MHz (2x32GB)', 15999.00, 8, 12.00),
(12, 'Kingston Fury 32GB', 'RAM', 'Kingston Fury Beast RGB DDR5 6000MHz (2x16GB)', 11999.00, 20, 12.00),

-- Storage
(13, 'Samsung 990 Pro 2TB', 'Storage', 'Samsung 990 Pro NVMe PCIe 4.0 M.2 SSD', 14999.00, 10, 12.00),
(14, 'WD Black SN850X 2TB', 'Storage', 'Western Digital Black SN850X NVMe PCIe 4.0', 13999.00, 12, 12.00),
(15, 'Seagate FireCuda 4TB', 'Storage', 'Seagate FireCuda 530 NVMe PCIe 4.0', 24999.00, 6, 12.00),
(16, 'Crucial P5 Plus 2TB', 'Storage', 'Crucial P5 Plus NVMe PCIe 4.0', 11999.00, 15, 12.00),

-- Motherboards
(17, 'ASUS ROG Maximus Z790', 'Motherboard', 'ASUS ROG Maximus Z790 Hero DDR5 ATX', 29999.00, 5, 12.00),
(18, 'MSI MEG X670E', 'Motherboard', 'MSI MEG X670E ACE AM5 DDR5 ATX', 32999.00, 4, 12.00),
(19, 'Gigabyte X670E Aorus', 'Motherboard', 'Gigabyte X670E Aorus Master AM5 DDR5', 27999.00, 6, 12.00),
(20, 'ASRock Z790 Taichi', 'Motherboard', 'ASRock Z790 Taichi DDR5 ATX', 26999.00, 7, 12.00),

-- Power Supplies
(21, 'Corsair HX1200i', 'PSU', 'Corsair HX1200i Platinum 1200W Fully Modular', 15999.00, 8, 12.00),
(22, 'be quiet! Dark Power 13', 'PSU', 'be quiet! Dark Power 13 1000W Titanium', 14999.00, 6, 12.00),
(23, 'Seasonic Prime TX-1300', 'PSU', 'Seasonic Prime TX-1300 Titanium 1300W', 16999.00, 5, 12.00),
(24, 'EVGA SuperNOVA 1000', 'PSU', 'EVGA SuperNOVA 1000 G6 Gold 1000W', 12999.00, 10, 12.00),

-- Cases
(25, 'Lian Li O11 Dynamic', 'Case', 'Lian Li O11 Dynamic EVO Full Tower', 8999.00, 10, 12.00),
(26, 'Fractal Design Torrent', 'Case', 'Fractal Design Torrent RGB Black E-ATX', 9999.00, 8, 12.00),
(27, 'Phanteks Evolv X', 'Case', 'Phanteks Evolv X Digital RGB E-ATX', 11999.00, 6, 12.00),
(28, 'be quiet! Dark Base Pro 900', 'Case', 'be quiet! Dark Base Pro 900 Black Rev. 2', 13999.00, 5, 12.00),

-- Peripherals
(29, 'Logitech G Pro X', 'Peripheral', 'Logitech G Pro X Superlight Wireless Mouse', 7999.00, 15, 12.00),
(30, 'Razer Huntsman V2', 'Peripheral', 'Razer Huntsman V2 Analog Optical Gaming Keyboard', 9999.00, 12, 12.00),
(31, 'Samsung Odyssey G7', 'Peripheral', 'Samsung Odyssey G7 32" 240Hz Gaming Monitor', 34999.00, 5, 12.00),
(32, 'SteelSeries Arctis Pro', 'Peripheral', 'SteelSeries Arctis Pro Wireless Gaming Headset', 12999.00, 10, 12.00);