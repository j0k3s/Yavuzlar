<?php
session_start();
require 'db.php'; 


if ($_SESSION['role'] !== 'shop') {
    header("Location: login.php"); 
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   
    if (isset($_POST['add_food'])) {
        $restaurant_id = $_POST['restaurant_id'];
        $name = $_POST['food_name'];
        $description = $_POST['food_description'];
        $price = $_POST['food_price'];
        $discount = $_POST['food_discount'];

        $stmt = $pdo->prepare("INSERT INTO food (restaurant_id, name, description, price, discount) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$restaurant_id, $name, $description, $price, $discount]);

        echo "Yemek başarıyla eklendi!";
        header("Location: restaurantPage.php");
        exit;
    }

    if (isset($_POST['update_food'])) {
        $id = $_POST['food_id'];
        $name = $_POST['food_name'];
        $description = $_POST['food_description'];
        $price = $_POST['food_price'];
        $discount = $_POST['food_discount'];

        $stmt = $pdo->prepare("UPDATE food SET name = ?, description = ?, price = ?, discount = ? WHERE id = ?");
        $stmt->execute([$name, $description, $price, $discount, $id]);

        echo "Yemek başarıyla güncellendi!";
        header("Location: restaurantPage.php");
        exit;
    }

    if (isset($_POST['soft_delete_food'])) {
        $id = $_POST['food_id'];

        $stmt = $pdo->prepare("UPDATE food SET deleted_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);

        echo "Yemek başarıyla silindi!";
        header("Location: restaurantPage.php");
        exit;
    }

  
    if (isset($_POST['add_coupon'])) {
        $restaurant_id = $_POST['restaurant_id'];
        $name = $_POST['coupon_name'];
        $discount = $_POST['coupon_discount'];

        $stmt = $pdo->prepare("INSERT INTO coupon (restaurant_id, name, discount) VALUES (?, ?, ?)");
        $stmt->execute([$restaurant_id, $name, $discount]);

        echo "Kupon başarıyla eklendi!";
        header("Location: restaurantPage.php");
        exit;
    }

    if (isset($_POST['update_coupon'])) {
        $id = $_POST['coupon_id'];
        $name = $_POST['coupon_name'];
        $discount = $_POST['coupon_discount'];

        $stmt = $pdo->prepare("UPDATE coupon SET name = ?, discount = ? WHERE id = ?");
        $stmt->execute([$name, $discount, $id]);

        echo "Kupon başarıyla güncellendi!";
        header("Location: restaurantPage.php");
        exit;
    }

    if (isset($_POST['soft_delete_coupon'])) {
        $id = $_POST['coupon_id'];

        $stmt = $pdo->prepare("DELETE FROM coupon WHERE id = ?");
        $stmt->execute([$id]);

        echo "Kupon başarıyla silindi!";
        header("Location: restaurantPage.php");
        exit;
    }

    
    if (isset($_POST['update_order_status'])) {
        $order_id = $_POST['order_id'];
        $status = $_POST['order_status'];

        
        if ($status == 'Teslim Edildi') {
            
            $stmt = $pdo->prepare("UPDATE `order` SET order_status = ? WHERE id = ?");
            $stmt->execute([$status, $order_id]);

            echo "Sipariş teslim edildi ve geçmiş siparişlere eklendi.";
        } else {
            $stmt = $pdo->prepare("UPDATE `order` SET order_status = ? WHERE id = ?");
            $stmt->execute([$status, $order_id]);

            echo "Sipariş durumu güncellendi!";
        }

        header("Location: restaurantPage.php");
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {

  
    if (isset($_GET['search_food'])) {
        $search = $_GET['search_food'];

        $stmt = $pdo->prepare("SELECT * FROM food WHERE name LIKE ? AND deleted_at IS NULL");
        $stmt->execute(["%$search%"]);
        $foods = $stmt->fetchAll();

        echo "<h3>Yemek Arama Sonuçları</h3>";
        foreach ($foods as $food) {
            echo "Yemek ID: " . $food['id'] . " - İsim: " . $food['name'] . "<br>";
        }
    }

    if (isset($_GET['list_foods'])) {
        $stmt = $pdo->query("SELECT * FROM food WHERE deleted_at IS NULL");
        $foods = $stmt->fetchAll();

        echo "<h3>Yemek Listesi</h3>";
        foreach ($foods as $food) {
            echo "Yemek ID: " . $food['id'] . " - İsim: " . $food['name'] . "<br>";
        }
    }

    if (isset($_GET['search_coupon'])) {
        $search = $_GET['search_coupon'];

        $stmt = $pdo->prepare("SELECT * FROM coupon WHERE name LIKE ?");
        $stmt->execute(["%$search%"]);
        $coupons = $stmt->fetchAll();

        echo "<h3>Kupon Arama Sonuçları</h3>";
        foreach ($coupons as $coupon) {
            echo "Kupon ID: " . $coupon['id'] . " - İsim: " . $coupon['name'] . "<br>";
        }
    }

    if (isset($_GET['list_coupons'])) {
        $stmt = $pdo->query("SELECT * FROM coupon");
        $coupons = $stmt->fetchAll();

        echo "<h3>Kupon Listesi</h3>";
        foreach ($coupons as $coupon) {
            echo "Kupon ID: " . $coupon['id'] . " - İsim: " . $coupon['name'] . "<br>";
        }
    }

    if (isset($_GET['search_restaurant'])) {
        $search = $_GET['search_restaurant'];

        $stmt = $pdo->prepare("SELECT * FROM restaurant WHERE name LIKE ? AND deleted_at IS NULL");
        $stmt->execute(["%$search%"]);
        $restaurants = $stmt->fetchAll();

        echo "<h3>Restoran Arama Sonuçları</h3>";
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

    if (isset($_GET['view_order_customer'])) {
        $order_id = $_GET['order_id'];

        $stmt = $pdo->prepare("SELECT u.id, u.name, u.surname, u.username, u.balance FROM `order` o
                               JOIN users u ON o.user_id = u.id WHERE o.id = ?");
        $stmt->execute([$order_id]);
        $customer = $stmt->fetch();

        echo "<h3>Müşteri Profili</h3>";
        if ($customer) {
            echo "Müşteri ID: " . htmlspecialchars($customer['id']) . 
                 " - İsim: " . htmlspecialchars($customer['name']) . " " . htmlspecialchars($customer['surname']) . 
                 " - Kullanıcı Adı: " . htmlspecialchars($customer['username']) . 
                 " - Bakiye: " . htmlspecialchars($customer['balance']) . "<br>";
        } else {
            echo "Müşteri bulunamadı veya sipariş bilgisi geçersiz.";
        }
    }
}
?>
