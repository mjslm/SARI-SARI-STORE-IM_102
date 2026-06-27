-- ========================================
-- SARI-SARI STORE DATABASE
-- Database: salomon_im102_final
-- ========================================

CREATE DATABASE IF NOT EXISTS salomon_im102_final;
USE salomon_im102_final;

-- Drop existing tables
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS suppliers;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- CREATE TABLES
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    category_id INT,
    supplier_id INT,
    added_by INT,
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
    FOREIGN KEY (added_by) REFERENCES users(id) ON DELETE SET NULL
);

-- INSERT DATA
INSERT INTO users (username, email, password_hash, role) VALUES
('admin', 'admin@store.com', '$2y$10$92IXUNpkjO0O0RQ5byMi.Ye4oKoEa3Ro9LLC/.og/at2.uheW6/igi', 'admin'),
('staff1', 'staff1@store.com', '$2y$10$92IXUNpkjO0O0RQ5byMi.Ye4oKoEa3Ro9LLC/.og/at2.uheW6/igi', 'staff');

INSERT INTO categories (name) VALUES
('Beverages'), ('Snacks'), ('Canned Goods'), ('Rice & Grains'),
('Cooking Essentials'), ('Household'), ('Personal Care');

INSERT INTO suppliers (name, contact_person, phone, address) VALUES
('Puregold Supplier', 'Juan Dela Cruz', '09123456789', '123 Main St, Manila'),
('SM Mart', 'Maria Santos', '09187654321', '456 SM Ave, Pasay'),
('Local Distributor', 'Pedro Reyes', '09991234567', '789 Local Rd, QC'),
('Best Foods Inc.', 'Ana Garcia', '09234567890', '321 Food St, Makati'),
('Prime Goods', 'Carlos Lopez', '09345678901', '654 Prime Ave, Taguig'),
('Quick Supply', 'Rosa Martinez', '09456789012', '987 Quick St, Mandaluyong'),
('Mega Mart', 'Jose Cruz', '09567890123', '147 Mega Rd, Pasig'),
('Star Trading', 'Luz Fernandez', '09678901234', '258 Star Ave, Paranaque'),
('Golden Harvest', 'Mario Santos', '09789012345', '369 Golden St, Las Pinas'),
('Allied Products', 'Teresa Reyes', '09890123456', '741 Allied Rd, Muntinlupa');

-- Beverages
INSERT INTO products (name, description, price, stock, category_id, supplier_id, added_by, image) VALUES
('Coca-Cola 1.5L', 'Regular Coke 1.5 Liters Bottle', 85.00, 50, 1, 1, 1, 'cocacola.jpg'),
('Pepsi 1.5L', 'Pepsi Cola 1.5 Liters Bottle', 80.00, 45, 1, 1, 1, 'pepsi.jpg'),
('Royal 1.5L', 'Royal Soft Drink 1.5 Liters', 75.00, 40, 1, 1, 1, 'royal.jpg'),
('Sprite 1.5L', 'Sprite Lemon-Lime 1.5 Liters', 82.00, 35, 1, 1, 1, 'sprite.jpg'),
('Bottled Water 500ml', '500ml Bottled Water', 20.00, 120, 1, 6, 1, 'water.jpg'),
('Coffee 3in1', '3-in-1 Coffee Pack', 7.00, 200, 1, 6, 1, 'coffee.jpg'),
('Milk Powder', 'Powdered Milk 200g', 45.00, 80, 1, 2, 1, 'milk.jpg'),
('Juice Drink 1L', 'Mixed Fruit Juice 1L', 55.00, 60, 1, 3, 1, 'juice.jpg');

-- Snacks
INSERT INTO products (name, description, price, stock, category_id, supplier_id, added_by, image) VALUES
('Noodles Instant', 'Instant Noodles Pack', 15.00, 200, 2, 3, 1, 'noodles.jpg'),
('Crackers', 'Cracker Biscuits Pack', 12.00, 150, 2, 5, 1, 'crackers.jpg'),
('Chips', 'Potato Chips Bag', 18.00, 100, 2, 3, 1, 'chips.jpg'),
('Bread Loaf', 'Fresh White Bread Loaf', 45.00, 40, 2, 2, 1, 'bread.jpg'),
('Cookies', 'Chocolate Chip Cookies', 25.00, 80, 2, 5, 1, 'cookies.jpg'),
('Candy Pack', 'Assorted Candy Pack', 10.00, 300, 2, 3, 1, 'candy.jpg');

-- Canned Goods
INSERT INTO products (name, description, price, stock, category_id, supplier_id, added_by, image) VALUES
('Canned Tuna', 'Canned Tuna in Oil', 25.00, 120, 3, 2, 1, 'tuna.jpg'),
('Canned Sardines', 'Sardines in Tomato Sauce', 18.00, 150, 3, 2, 1, 'sardines.jpg'),
('Corned Beef', 'Corned Beef 150g', 35.00, 100, 3, 4, 1, 'cornedbeef.jpg'),
('Meat Loaf', 'Meat Loaf 150g', 30.00, 90, 3, 4, 1, 'meatloaf.jpg');

-- Rice & Grains
INSERT INTO products (name, description, price, stock, category_id, supplier_id, added_by, image) VALUES
('Rice 5kg', '5kg Premium Rice', 250.00, 30, 4, 4, 1, 'rice.jpg'),
('Rice 1kg', '1kg Premium Rice', 55.00, 50, 4, 4, 1, 'rice1kg.jpg');

-- Cooking Essentials
INSERT INTO products (name, description, price, stock, category_id, supplier_id, added_by, image) VALUES
('Sugar 1kg', 'Refined Sugar 1kg', 65.00, 80, 5, 4, 1, 'sugar.jpg'),
('Cooking Oil 1L', 'Vegetable Cooking Oil 1L', 120.00, 40, 5, 4, 1, 'oil.jpg'),
('Salt 1kg', 'Iodized Salt 1kg', 25.00, 100, 5, 4, 1, 'salt.jpg'),
('Vinegar 1L', 'Cane Vinegar 1L', 30.00, 70, 5, 4, 1, 'vinegar.jpg'),
('Soy Sauce 1L', 'Soy Sauce 1L', 35.00, 65, 5, 4, 1, 'soysauce.jpg');

-- Household
INSERT INTO products (name, description, price, stock, category_id, supplier_id, added_by, image) VALUES
('Tide Detergent', 'Laundry Detergent Powder 200g', 45.00, 60, 6, 5, 1, 'tide.jpg'),
('Bleach', 'Liquid Bleach 1L', 40.00, 50, 6, 5, 1, 'bleach.jpg'),
('Dishwashing Liquid', 'Dishwashing Liquid 500ml', 35.00, 70, 6, 5, 1, 'dishwash.jpg');

-- Personal Care
INSERT INTO products (name, description, price, stock, category_id, supplier_id, added_by, image) VALUES
('Bath Soap', 'Bath Soap Bar', 35.00, 100, 7, 5, 1, 'soap.jpg'),
('Shampoo', 'Shampoo Sachet', 12.00, 200, 7, 5, 1, 'shampoo.jpg'),
('Toothpaste', 'Toothpaste 100ml', 45.00, 80, 7, 5, 1, 'toothpaste.jpg'),
('Facial Wash', 'Facial Wash 50ml', 55.00, 60, 7, 5, 1, 'facialwash.jpg');

-- VERIFY
SELECT 'Users' as Table, COUNT(*) as Count FROM users
UNION ALL
SELECT 'Categories', COUNT(*) FROM categories
UNION ALL
SELECT 'Suppliers', COUNT(*) FROM suppliers
UNION ALL
SELECT 'Products', COUNT(*) FROM products;