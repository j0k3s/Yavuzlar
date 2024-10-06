<?php
session_start();
require 'db.php'; 


if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php"); 
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['add_restaurant'])) {
        $name = $_POST['restoran_name'];
        $description = $_POST['restoran_description'];

        $stmt = $pdo->prepare("INSERT INTO restaurant (name, description) VALUES (?, ?)");
        $stmt->execute([$name, $description]);

        echo "Restoran başarıyla eklendi!";
        header("Location: adminPage.php");
        exit;
    }

    if (isset($_POST['update_restaurant'])) {
        $id = $_POST['restoran_id'];
        $name = $_POST['restoran_name'];
        $description = $_POST['restoran_description'];

        $stmt = $pdo->prepare("UPDATE restaurant SET name = ?, description = ? WHERE id = ?");
        $stmt->execute([$name, $description, $id]);

        echo "Restoran başarıyla güncellendi!";
        header("Location: adminPage.php");
        exit;
    }

    if (isset($_POST['soft_delete_restaurant'])) {
        $id = $_POST['restoran_id'];

        $stmt = $pdo->prepare("UPDATE restaurant SET deleted_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);

        echo "Restoran başarıyla soft silindi!";
        header("Location: adminPage.php");
        exit;
    }

    if (isset($_POST['add_customer'])) {
        $name = $_POST['customer_name'];
        $surname = $_POST['customer_surname'];
        $username = $_POST['customer_username'];
        $password = password_hash($_POST['customer_password'], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (name, surname, username, password, role) VALUES (?, ?, ?, ?, 'customer')");
        $stmt->execute([$name, $surname, $username, $password]);

        echo "Müşteri başarıyla eklendi!";
        header("Location: adminPage.php");
        exit;
    }

    if (isset($_POST['update_customer'])) {
        $id = $_POST['customer_id'];
        $name = $_POST['customer_name'];
        $surname = $_POST['customer_surname'];

        $stmt = $pdo->prepare("UPDATE users SET name = ?, surname = ? WHERE id = ?");
        $stmt->execute([$name, $surname, $id]);

        echo "Müşteri başarıyla güncellendi!";
        header("Location: adminPage.php");
        exit;
    }

    if (isset($_POST['soft_delete_customer'])) {
        $id = $_POST['customer_id'];

        $stmt = $pdo->prepare("UPDATE users SET deleted_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);

        echo "Müşteri başarıyla soft silindi!";
        header("Location: adminPage.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['search_restaurant_action'])) {
        $search = $_GET['search_restaurant'];

        $stmt = $pdo->prepare("SELECT * FROM restaurant WHERE name LIKE ? AND deleted_at IS NULL");
        $stmt->execute(["%$search%"]);
        $restaurants = $stmt->fetchAll();

        echo "<h3>Arama Sonuçları</h3>";
        foreach ($restaurants as $restaurant) {
            echo "Restoran ID: " . $restaurant['id'] . " - İsim: " . $restaurant['name'] . "<br>";
        }
    }

    if (isset($_GET['list_restaurants'])) {
        $stmt = $pdo->query("SELECT * FROM restaurant WHERE deleted_at IS NULL");
        $restaurants = $stmt->fetchAll();

        echo "<h3>Restoran Listesi</h3>";
        foreach ($restaurants as $restaurant) {
            echo "Restoran ID: " . $restaurant['id'] . " - İsim: " . $restaurant['name'] . "<br>";
        }
    }

    if (isset($_GET['search_customer_action'])) {
        $search = $_GET['search_customer'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE name LIKE ? AND role = 'customer' AND deleted_at IS NULL");
        $stmt->execute(["%$search%"]);
        $customers = $stmt->fetchAll();

        echo "<h3>Arama Sonuçları</h3>";
        foreach ($customers as $customer) {
            echo "Müşteri ID: " . $customer['id'] . " - İsim: " . $customer['name'] . " " . $customer['surname'] . "<br>";
        }
    }

    if (isset($_GET['list_customers'])) {
        $filter = $_GET['filter_customers'];

        if ($filter == 'active') {
            $stmt = $pdo->query("SELECT * FROM users WHERE role = 'customer' AND deleted_at IS NULL");
        } elseif ($filter == 'deleted') {
            $stmt = $pdo->query("SELECT * FROM users WHERE role = 'customer' AND deleted_at IS NOT NULL");
        } else {
            $stmt = $pdo->query("SELECT * FROM users WHERE role = 'customer'");
        }

        $customers = $stmt->fetchAll();

        echo "<h3>Müşteri Listesi</h3>";
        foreach ($customers as $customer) {
            echo "Müşteri ID: " . $customer['id'] . " - İsim: " . $customer['name'] . " " . $customer['surname'];
            if ($customer['deleted_at']) {
                echo " (Silinmiş)";
            }
            echo "<br>";
        }
    }

    if (isset($_GET['view_customer_orders'])) {
        $customer_id = $_GET['customer_id'];

        $stmt = $pdo->prepare("SELECT o.id, o.total_price, o.order_status FROM `order` o WHERE o.user_id = ?");
        $stmt->execute([$customer_id]);
        $orders = $stmt->fetchAll();

        echo "<h3>Müşteri Siparişleri</h3>";
        foreach ($orders as $order) {
            echo "Sipariş ID: " . $order['id'] . " - Toplam Fiyat: " . $order['total_price'] . " - Durum: " . $order['order_status'] . "<br>"; } 
            }
             } ?>
