CREATE DATABASE ecommerce;
USE ecommerce;
CREATE TABLE wallet (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    balance DECIMAL(10,2) NOT NULL DEFAULT 0
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    wallet_balance DECIMAL(10,2) DEFAULT 0.00
);

CREATE TABLE transaction (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    amount DECIMAL(10,2),
    type ENUM('cashback', 'purchase'),
    category VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE category_cashback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(255) NOT NULL UNIQUE,
    cashback_percentage DECIMAL(5,2) NOT NULL
);
INSERT INTO category_cashback (category, cashback_percentage) VALUES
('A', 10.00),
('B', 2.00),
('C', 7.00);


INSERT INTO users (name, email, wallet_balance) VALUES ('Pradnya Desai', 'pradnya@example.com', 100.00);
SELECT * FROM users WHERE id = 1;

SELECT * FROM transactions WHERE user_id = 1;
select * from wallet where id=1;
select * from category_cashback;
SHOW TABLES;
DESCRIBE users;

