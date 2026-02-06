-- Moon Cafeteria Management System Database
-- Created: 2026-01-18
-- Description: Complete database schema with sample data

-- Create database
CREATE DATABASE IF NOT EXISTS moon_cafeteria;
USE moon_cafeteria;

-- Drop tables if they exist (to avoid errors on re-import)
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;
DROP VIEW IF EXISTS daily_sales;
DROP VIEW IF EXISTS product_sales;

-- ============================================
-- Table: roles
-- Description: User roles (Admin, Cashier)
-- ============================================
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Table: users
-- Description: System users with authentication
-- ============================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role_id INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- ============================================
-- Table: categories
-- Description: Product categories
-- ============================================
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Table: products
-- Description: Cafeteria products (food & drinks)
-- ============================================
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    stock INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- ============================================
-- Table: orders
-- Description: Customer orders
-- ============================================
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('cash', 'card', 'mobile') DEFAULT 'cash',
    status ENUM('completed', 'cancelled') DEFAULT 'completed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ============================================
-- Table: order_items
-- Description: Items in each order
-- ============================================
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- ============================================
-- INSERT SAMPLE DATA
-- ============================================

-- Insert roles
INSERT INTO roles (role_name) VALUES 
('Admin'),
('Cashier');

-- Insert users (passwords are hashed using password_hash())
-- Admin: username=admin, password=admin123
-- Cashier: username=cashier, password=cashier123
INSERT INTO users (username, password, full_name, role_id, status) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 1, 'active'),
('cashier', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'John Cashier', 2, 'active'),
('sarah', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Sarah Williams', 2, 'active');

-- Insert categories
INSERT INTO categories (category_name, description, status) VALUES
('Drinks', 'Hot and cold beverages', 'active'),
('Food', 'Main meals and dishes', 'active'),
('Snacks', 'Light snacks and appetizers', 'active'),
('Desserts', 'Sweet treats and desserts', 'active');

-- Insert products
INSERT INTO products (category_id, product_name, description, price, image, stock, status) VALUES
-- Drinks
(1, 'Espresso', 'Strong Italian coffee', 2.50, 'espresso.jpg', 100, 'active'),
(1, 'Cappuccino', 'Espresso with steamed milk foam', 3.50, 'cappuccino.jpg', 100, 'active'),
(1, 'Latte', 'Espresso with steamed milk', 3.75, 'latte.jpg', 100, 'active'),
(1, 'Iced Coffee', 'Cold brewed coffee with ice', 3.00, 'iced_coffee.jpg', 100, 'active'),
(1, 'Orange Juice', 'Freshly squeezed orange juice', 2.75, 'orange_juice.jpg', 80, 'active'),
(1, 'Mineral Water', 'Still mineral water', 1.50, 'water.jpg', 150, 'active'),
(1, 'Green Tea', 'Organic green tea', 2.25, 'green_tea.jpg', 100, 'active'),
(1, 'Smoothie', 'Mixed fruit smoothie', 4.50, 'smoothie.jpg', 60, 'active'),

-- Food
(2, 'Chicken Sandwich', 'Grilled chicken with vegetables', 6.50, 'chicken_sandwich.jpg', 50, 'active'),
(2, 'Beef Burger', 'Classic beef burger with cheese', 7.50, 'beef_burger.jpg', 45, 'active'),
(2, 'Caesar Salad', 'Fresh romaine with caesar dressing', 5.75, 'caesar_salad.jpg', 40, 'active'),
(2, 'Pasta Carbonara', 'Creamy pasta with bacon', 8.50, 'pasta_carbonara.jpg', 35, 'active'),
(2, 'Pizza Margherita', 'Classic tomato and mozzarella pizza', 9.00, 'pizza.jpg', 30, 'active'),
(2, 'Fish & Chips', 'Fried fish with french fries', 8.75, 'fish_chips.jpg', 25, 'active'),
(2, 'Vegetable Wrap', 'Healthy vegetable wrap', 5.50, 'veg_wrap.jpg', 40, 'active'),

-- Snacks
(3, 'French Fries', 'Crispy golden fries', 3.25, 'fries.jpg', 80, 'active'),
(3, 'Onion Rings', 'Breaded onion rings', 3.50, 'onion_rings.jpg', 60, 'active'),
(3, 'Chicken Wings', '6 pieces spicy wings', 6.00, 'wings.jpg', 50, 'active'),
(3, 'Nachos', 'Tortilla chips with cheese', 4.75, 'nachos.jpg', 55, 'active'),
(3, 'Spring Rolls', '4 pieces vegetable spring rolls', 4.25, 'spring_rolls.jpg', 45, 'active'),

-- Desserts
(4, 'Chocolate Cake', 'Rich chocolate layer cake', 4.50, 'chocolate_cake.jpg', 30, 'active'),
(4, 'Cheesecake', 'New York style cheesecake', 5.00, 'cheesecake.jpg', 25, 'active'),
(4, 'Ice Cream', 'Vanilla ice cream scoop', 3.00, 'ice_cream.jpg', 60, 'active'),
(4, 'Apple Pie', 'Homemade apple pie slice', 3.75, 'apple_pie.jpg', 35, 'active'),
(4, 'Brownie', 'Chocolate brownie with nuts', 3.25, 'brownie.jpg', 40, 'active');

-- Insert sample orders (for demonstration)
INSERT INTO orders (user_id, order_date, total_amount, payment_method, status) VALUES
(2, '2026-01-18 09:15:00', 15.25, 'cash', 'completed'),
(2, '2026-01-18 09:45:00', 22.50, 'card', 'completed'),
(3, '2026-01-18 10:20:00', 18.75, 'cash', 'completed'),
(2, '2026-01-18 10:55:00', 31.00, 'mobile', 'completed'),
(3, '2026-01-18 11:10:00', 12.50, 'cash', 'completed'),
(2, '2026-01-17 14:30:00', 45.75, 'card', 'completed'),
(3, '2026-01-17 15:20:00', 28.50, 'cash', 'completed'),
(2, '2026-01-16 12:45:00', 52.25, 'card', 'completed');

-- Insert order items for the sample orders
INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) VALUES
-- Order 1
(1, 2, 2, 3.50, 7.00),
(1, 9, 1, 6.50, 6.50),
(1, 6, 1, 1.50, 1.50),

-- Order 2
(2, 10, 1, 7.50, 7.50),
(2, 15, 2, 3.25, 6.50),
(2, 1, 2, 2.50, 5.00),
(2, 22, 1, 3.50, 3.50),

-- Order 3
(3, 11, 1, 5.75, 5.75),
(3, 3, 1, 3.75, 3.75),
(3, 12, 1, 8.50, 8.50),
(3, 6, 1, 1.50, 1.50),

-- Order 4
(4, 13, 2, 9.00, 18.00),
(4, 4, 2, 3.00, 6.00),
(4, 23, 1, 4.50, 4.50),
(4, 24, 1, 5.00, 5.00),

-- Order 5
(5, 8, 2, 4.50, 9.00),
(5, 18, 1, 3.50, 3.50),

-- Order 6
(6, 14, 2, 8.75, 17.50),
(6, 10, 2, 7.50, 15.00),
(6, 18, 2, 6.00, 12.00),
(6, 25, 1, 3.25, 3.25),

-- Order 7
(7, 12, 2, 8.50, 17.00),
(7, 7, 2, 2.25, 4.50),
(7, 23, 1, 4.50, 4.50),
(7, 22, 1, 5.00, 5.00),

-- Order 8
(8, 13, 3, 9.00, 27.00),
(8, 2, 4, 3.50, 14.00),
(8, 22, 2, 3.50, 7.00),
(8, 24, 1, 3.75, 3.75);

-- ============================================
-- VIEWS FOR REPORTING
-- ============================================

-- View: Daily sales summary
CREATE VIEW daily_sales AS
SELECT 
    DATE(order_date) as sale_date,
    COUNT(*) as total_orders,
    SUM(total_amount) as total_sales
FROM orders
WHERE status = 'completed'
GROUP BY DATE(order_date)
ORDER BY sale_date DESC;

-- View: Product sales summary
CREATE VIEW product_sales AS
SELECT 
    p.id,
    p.product_name,
    c.category_name,
    COUNT(oi.id) as times_ordered,
    SUM(oi.quantity) as total_quantity,
    SUM(oi.subtotal) as total_revenue
FROM products p
LEFT JOIN order_items oi ON p.id = oi.product_id
LEFT JOIN categories c ON p.category_id = c.id
GROUP BY p.id, p.product_name, c.category_name
ORDER BY total_revenue DESC;

-- ============================================
-- INDEXES FOR PERFORMANCE
-- ============================================

CREATE INDEX idx_orders_date ON orders(order_date);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_order_items_product ON order_items(product_id);

-- ============================================
-- DATABASE SETUP COMPLETE
-- ============================================
