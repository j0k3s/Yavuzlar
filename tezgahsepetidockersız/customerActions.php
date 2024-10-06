<?php
session_start();
require 'db.php'; 


if (isset($_POST['add_to_cart'])) {
    $food_id = $_POST['food_id'];
    $quantity = $_POST['quantity'];
    $note = $_POST['note'] ?? '';
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO basket (user_id, food_id, note, quantity) VALUES (:user_id, :food_id, :note, :quantity)");
    $stmt->execute([
        ':user_id' => $user_id,
        ':food_id' => $food_id,
        ':note' => $note,
        ':quantity' => $quantity
    ]);

    echo "Yemek sepete eklendi.";
    header("Location: customerPage.php");
exit;
}

if (isset($_POST['submit_comment'])) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error_message'] = "Yorum yapmak için giriş yapmalısınız.";
        header("Location: login.php");
        exit;
    }

    $restaurant_id = $_POST['restaurant_id'];
    $comment_title = $_POST['comment_title'];
    $comment_description = $_POST['comment_description'];
    $rating = $_POST['rating'];
    $user_id = $_SESSION['user_id'];

    try {
       
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, restaurant_id, title, description, score) VALUES (:user_id, :restaurant_id, :title, :description, :score)");
        $result = $stmt->execute([
            ':user_id' => $user_id,
            ':restaurant_id' => $restaurant_id,
            ':title' => $comment_title,
            ':description' => $comment_description,
            ':score' => $rating
        ]);

        if ($result) {
            $_SESSION['success_message'] = "Yorum ve puan başarıyla eklendi.";
        } else {
            $_SESSION['error_message'] = "Yorum eklenirken bir hata oluştu.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Veritabanı hatası: " . $e->getMessage();
    }
    
 
    header("Location: customerPage.php?id=" . $user_id);
    exit;
}


if (isset($_GET['search_restaurant_button'])) {
    $search_term = $_GET['search_restaurant'];
    
   
    $stmt = $pdo->prepare("SELECT * FROM restaurant WHERE name LIKE :search_term");
    $stmt->execute([
        ':search_term' => "%$search_term%"
    ]);
    $restaurants = $stmt->fetchAll();

    echo "<h2>Arama Sonuçları:</h2>";
    foreach ($restaurants as $restaurant) {
        echo "Restoran Adı: " . $restaurant['name'] . "<br>";
        echo "Açıklama: " . $restaurant['description'] . "<br><hr>";
    }
    
}

if (isset($_POST['apply_coupon'])) {
    $coupon_code = $_POST['coupon_code'];
    
   
    $stmt = $pdo->prepare("SELECT * FROM coupon WHERE name = :coupon_code");
    $stmt->execute([
        ':coupon_code' => $coupon_code
    ]);
    $coupon = $stmt->fetch();

    if ($coupon) {
        echo "Kupon uygulandı. İndirim: " . $coupon['discount'] . "%";
    } else {
        echo "Geçersiz kupon kodu.";
    }
}


if (isset($_GET['view_profile'])) {
    $user_id = $_SESSION['user_id'];

  
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $profile = $stmt->fetch();

    if ($profile) {
        echo "Ad: " . $profile['name'] . "<br>";
        echo "Soyad: " . $profile['surname'] . "<br>";
        echo "Kullanıcı Adı: " . $profile['username'] . "<br>";
        echo "Bakiye: " . $profile['balance'] . "<br>";
     
    }
    
}


if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $user = $stmt->fetch();

    if (password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_new_password) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = :new_password WHERE id = :user_id");
            $stmt->execute([':new_password' => $new_password_hash, ':user_id' => $user_id]);
            echo "Şifre başarıyla değiştirildi.";
        } else {
            echo "Yeni şifreler eşleşmiyor.";
        }
    } else {
        echo "Mevcut şifre yanlış.";
    }
    header("Location: customerPage.php");
exit;
}

if (isset($_POST['add_balance'])) {
    $balance_amount = $_POST['balance_amount'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("UPDATE users SET balance = balance + :balance_amount WHERE id = :user_id");
    $stmt->execute([
        ':balance_amount' => $balance_amount,
        ':user_id' => $user_id
    ]);

    echo "Bakiye başarıyla yüklendi.";
    header("Location: customerPage.php");
exit;
}

if (isset($_GET['view_active_orders'])) {
    $user_id = $_SESSION['user_id'];

   
    $stmt = $pdo->prepare("SELECT * FROM `order` WHERE user_id = :user_id AND order_status = 'active'");
    $stmt->execute([':user_id' => $user_id]);
    $active_orders = $stmt->fetchAll();

    echo "<h2>Aktif Siparişler:</h2>";
    foreach ($active_orders as $order) {
        echo "Sipariş ID: " . $order['id'] . "<br>";
        echo "Toplam Fiyat: " . $order['total_price'] . "<br><hr>";
    }
}

if (isset($_GET['view_past_orders'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM `order` WHERE user_id = :user_id AND order_status = 'completed'");
    $stmt->execute([':user_id' => $user_id]);
    $past_orders = $stmt->fetchAll();

    echo "<h2>Geçmiş Siparişler:</h2>";
    foreach ($past_orders as $order) {

        echo "Sipariş ID: " . $order['id'] . "<br>";

        echo "Toplam Fiyat: " . $order['total_price'] . "<br><hr>";
    }
}


if (isset($_GET['list_restaurants'])) {
  
    header("Location: RestaurantListPage.php");
    exit;
}
?>

