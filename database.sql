-- OVARALL E-Commerce Database Schema

CREATE DATABASE IF NOT EXISTS ovarall_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ovarall_db;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    parent_id INT DEFAULT NULL,
    image VARCHAR(255),
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Products Table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    description TEXT,
    short_description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    sale_price DECIMAL(10, 2) DEFAULT NULL,
    sku VARCHAR(50) UNIQUE,
    stock INT DEFAULT 0,
    category_id INT,
    image VARCHAR(255),
    gallery TEXT,
    sizes VARCHAR(255),
    colors VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    is_new BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    rating DECIMAL(2, 1) DEFAULT 5.0,
    review_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Orders Table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    user_id INT,
    total_amount DECIMAL(10, 2) NOT NULL,
    shipping_amount DECIMAL(10, 2) DEFAULT 0,
    discount_amount DECIMAL(10, 2) DEFAULT 0,
    final_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_method ENUM('cod', 'bkash', 'nagad') DEFAULT 'cod',
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    shipping_name VARCHAR(100),
    shipping_email VARCHAR(100),
    shipping_phone VARCHAR(20),
    shipping_address TEXT,
    shipping_city VARCHAR(50),
    shipping_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Order Items Table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(200),
    product_image VARCHAR(255),
    price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    size VARCHAR(20),
    color VARCHAR(20),
    total DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Wishlist Table
CREATE TABLE wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (user_id, product_id)
);

-- Reviews Table
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT,
    user_name VARCHAR(100),
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    is_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert Admin User (password: admin123)
INSERT INTO users (name, email, password, phone, role) VALUES 
('Admin', 'admin@ovarall.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '01981622758', 'admin');

-- Insert Categories
INSERT INTO categories (name, slug, description, parent_id, sort_order) VALUES
('Fashion', 'fashion', 'Latest fashion trends for men and women', NULL, 1),
('Accessories', 'accessories', 'Fashion accessories and more', NULL, 2),
('Electronics', 'electronics', 'Latest electronic gadgets', NULL, 3),
('Health', 'health', 'Health and wellness products', NULL, 4);

-- Insert Subcategories for Electronics
INSERT INTO categories (name, slug, description, parent_id, sort_order) VALUES
('Mobile', 'mobile', 'Smartphones and mobile accessories', 3, 1),
('Computer', 'computer', 'Laptops, desktops and accessories', 3, 2),
('Earphone', 'earphone', 'Headphones and earphones', 3, 3);

-- Insert Sample Products
INSERT INTO products (name, slug, description, short_description, price, sale_price, stock, category_id, image, sizes, colors, is_featured, is_new, rating, review_count) VALUES
('Classic Navy Blazer', 'classic-navy-blazer', 'A timeless navy blazer crafted from premium wool blend. Perfect for both formal and casual occasions.', 'Premium wool blend blazer', 12500.00, 9999.00, 25, 1, 'images/products/blazer.jpg', 'S,M,L,XL,XXL', 'Navy,Black,Charcoal', 1, 1, 4.8, 124),

('Silk Cream Blouse', 'silk-cream-blouse', 'Elegant silk blouse with delicate pleating and a relaxed fit. Perfect addition to your wardrobe.', 'Elegant silk blouse', 8500.00, NULL, 30, 1, 'images/products/blouse.jpg', 'XS,S,M,L,XL', 'Cream,Blush,Black', 1, 0, 4.9, 89),

('Leather Crossbody Bag', 'leather-crossbody-bag', 'Handcrafted from genuine Italian leather with multiple compartments.', 'Genuine leather crossbody bag', 7500.00, 6200.00, 15, 2, 'images/products/bag.jpg', NULL, 'Tan,Black,Brown', 1, 1, 4.7, 156),

('Gold Pendant Necklace', 'gold-pendant-necklace', 'Delicate 18k gold-plated necklace with minimalist pendant.', '18k gold-plated necklace', 3200.00, NULL, 40, 2, 'images/products/necklace.jpg', NULL, 'Gold,Silver,Rose Gold', 1, 0, 4.7, 145),

('iPhone 15 Pro Max', 'iphone-15-pro-max', 'Latest iPhone with A17 Pro chip and titanium design.', 'Apple iPhone 15 Pro Max 256GB', 185000.00, NULL, 20, 5, 'images/products/iphone.jpg', NULL, 'Natural Titanium,Blue Titanium,White Titanium', 1, 1, 4.9, 523),

('Samsung Galaxy S24 Ultra', 'samsung-galaxy-s24-ultra', 'Flagship smartphone with S Pen and AI features.', 'Samsung Galaxy S24 Ultra 512GB', 165000.00, 159999.00, 15, 5, 'images/products/samsung.jpg', NULL, 'Titanium Gray,Titanium Black,Titanium Violet', 1, 1, 4.8, 412),

('MacBook Pro 14"', 'macbook-pro-14', 'Powerful laptop with M3 Pro chip for professionals.', 'Apple MacBook Pro 14" M3 Pro', 245000.00, NULL, 10, 6, 'images/products/macbook.jpg', NULL, 'Space Gray,Silver', 1, 1, 4.9, 234),

('Dell XPS 15', 'dell-xps-15', 'Premium laptop with stunning display and performance.', 'Dell XPS 15 OLED', 195000.00, 185000.00, 12, 6, 'images/products/dell.jpg', NULL, 'Platinum Silver', 1, 0, 4.7, 178),

('Sony WH-1000XM5', 'sony-wh-1000xm5', 'Industry-leading noise canceling headphones.', 'Sony Wireless Noise Canceling Headphones', 38500.00, 34999.00, 30, 7, 'images/products/sony-headphone.jpg', NULL, 'Black,Silver', 1, 1, 4.8, 892),

('AirPods Pro 2', 'airpods-pro-2', 'Active noise cancellation with transparency mode.', 'Apple AirPods Pro 2nd Gen', 32500.00, NULL, 50, 7, 'images/products/airpods.jpg', NULL, 'White', 1, 1, 4.9, 1256),

('Digital Blood Pressure Monitor', 'blood-pressure-monitor', 'Accurate blood pressure monitoring at home.', 'Omron Digital BP Monitor', 8500.00, 7200.00, 25, 4, 'images/products/bp-monitor.jpg', NULL, 'White', 1, 0, 4.6, 234),

('Digital Thermometer', 'digital-thermometer', 'Fast and accurate temperature reading.', 'Infrared Digital Thermometer', 2500.00, 1999.00, 100, 4, 'images/products/thermometer.jpg', NULL, 'White,Blue', 0, 1, 4.5, 567);

-- Create uploads directory structure
-- Note: Run this SQL in your MySQL database
