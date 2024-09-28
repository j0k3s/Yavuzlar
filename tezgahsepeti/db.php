<?php
try {
    $pdo = new PDO('mysql:host=db;dbname=tezgah;charset=utf8mb4', 'tezgah_user', 'tezgah_password', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $pdo->exec("CREATE TABLE IF NOT EXISTS company (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        logo_path VARCHAR(255),
        deleted_at TIMESTAMP NULL DEFAULT NULL
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS restaurant (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_id INT,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        image_path VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (company_id) REFERENCES company(id)
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_id INT,
        role VARCHAR(50),
        name VARCHAR(255),
        surname VARCHAR(255),
        username VARCHAR(255) UNIQUE,
        password VARCHAR(255),
         image_path VARCHAR(255),
        balance DECIMAL(10, 2) DEFAULT 5000,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP NULL DEFAULT NULL,
        FOREIGN KEY (company_id) REFERENCES company(id)
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS food (
        id INT AUTO_INCREMENT PRIMARY KEY,
        restaurant_id INT,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        image_path VARCHAR(255),
        price DECIMAL(10, 2),
        discount DECIMAL(5, 2),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP NULL DEFAULT NULL,
        FOREIGN KEY (restaurant_id) REFERENCES restaurant(id)
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS basket (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        food_id INT,
        note TEXT,
        quantity INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (food_id) REFERENCES food(id)
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `order` (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        order_status VARCHAR(50),
        total_price DECIMAL(10, 2),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        food_id INT,
        order_id INT,
        quantity INT,
        price DECIMAL(10, 2),
        FOREIGN KEY (food_id) REFERENCES food(id),
        FOREIGN KEY (order_id) REFERENCES `order`(id)
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        restaurant_id INT,
        surname VARCHAR(255),
        title VARCHAR(255),
        description TEXT,
        score INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (restaurant_id) REFERENCES restaurant(id)
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS coupon (
        id INT AUTO_INCREMENT PRIMARY KEY,
        restaurant_id INT,
        name VARCHAR(255),
        discount DECIMAL(5, 2),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (restaurant_id) REFERENCES restaurant(id)
    )");

    $adminPassword = password_hash('admin', PASSWORD_DEFAULT);
    $dukkanPassword = password_hash('dukkan1', PASSWORD_DEFAULT);
    $musteriPassword = password_hash('musteri1', PASSWORD_DEFAULT);

    $pdo->exec("INSERT INTO users (company_id, role, name, surname, username, password, balance) 
                VALUES (NULL, 'admin', 'Admin', 'User', 'admin', '$adminPassword', 5000)
                ON DUPLICATE KEY UPDATE username = username");

    $pdo->exec("INSERT INTO users (company_id, role, name, surname, username, password, balance) 
                VALUES (NULL, 'shop', 'Dukkan', 'Bir', 'dukkan1', '$dukkanPassword', 5000)
                ON DUPLICATE KEY UPDATE username = username");

    $pdo->exec("INSERT INTO users (company_id, role, name, surname, username, password, balance) 
                VALUES (NULL, 'customer', 'Musteri', 'Bir', 'musteri1', '$musteriPassword', 5000)
                ON DUPLICATE KEY UPDATE username = username");

    echo "Users added successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
