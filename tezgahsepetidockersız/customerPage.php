<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php"); 
    exit;
}
?>

<?php


if (isset($_POST['place_order'])) {
    
    $cart_items = $_SESSION['cart'] ?? [];
    $total_price = 0;

    foreach ($cart_items as $item) {
        $total_price += $item['total_price'];
    }

    
    $sql = "INSERT INTO `order` (user_id, order_status, total_price, created_at) VALUES (:user_id, :order_status, :total_price, :created_at)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'order_status' => 'active',
        'total_price' => $total_price,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    if ($result) {
        
        unset($_SESSION['cart']);
        echo "Sipariş başarıyla oluşturuldu! Sipariş ID: " . $pdo->lastInsertId();
    } else {
        echo "Sipariş oluşturulurken bir hata oluştu: ";
        print_r($stmt->errorInfo());
    }
}


?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müşteri Sayfası</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="customer-page">
    <h1>Müşteri İşlemleri</h1>

    <img src="images/yavuzlarlogo.jpg" alt="yavuzlar" style="width:200px;height:auto;">


    <h2>Yemek Sepete Ekleme</h2>
    <form action="customerActions.php" method="POST">
        <label for="food_id">Yemek ID:</label>
        <input type="number" id="food_id" name="food_id" required>

        <label for="quantity">Adet:</label>
        <input type="number" id="quantity" name="quantity" required>

        <label for="note">Not:</label>
        <textarea id="note" name="note"></textarea>

        <button type="submit" name="add_to_cart">Sepete Ekle</button>
    </form>

    
    <h2>Restorana Yorum Yapma ve Puan Verme</h2>
    <form action="customerActions.php" method="POST">
        <label for="restaurant_id">Restoran ID:</label>
        <input type="number" id="restaurant_id" name="restaurant_id" required>

        <label for="comment_title">Yorum Başlığı:</label>
        <input type="text" id="comment_title" name="comment_title" required>

        <label for="comment_description">Yorum:</label>
        <textarea id="comment_description" name="comment_description" required></textarea>

        <label for="rating">Puan (0-10):</label>
        <input type="number" id="rating" name="rating" min="0" max="10" required>

        <button type="submit" name="submit_comment">Yorum Yap ve Puan Ver</button>
    </form>

   
    <h2>Restoran Arama</h2>
    <form action="customerActions.php" method="GET">
        <label for="search_restaurant">Restoran Ara:</label>
        <input type="text" id="search_restaurant" name="search_restaurant">
        <button type="submit" name="search_restaurant_button">Ara</button>
    </form>

    
    <h2>Kupon Kullanma</h2>
    <form action="customerActions.php" method="POST">
        <label for="coupon_code">Kupon Kodu:</label>
        <input type="text" id="coupon_code" name="coupon_code" required>
        <button type="submit" name="apply_coupon">Kuponu Kullan</button>
    </form>

    
    <h2>Profil İşlemleri</h2>
<form action="profilePage.php" method="GET">
    <button type="submit" name="view_profile">Profil Görüntüle</button>
</form>

<form action="basketPage.php" method="GET">
    <button type="submit" name="view_basket">sepeti Görüntüle</button>
</form>

    <h3>Şifre Değiştirme</h3>
    <form action="customerActions.php" method="POST">
        <label for="current_password">Mevcut Şifre:</label>
        <input type="password" id="current_password" name="current_password" required>

        <label for="new_password">Yeni Şifre:</label>
        <input type="password" id="new_password" name="new_password" required>

        <label for="confirm_new_password">Yeni Şifre (Tekrar):</label>
        <input type="password" id="confirm_new_password" name="confirm_new_password" required>

        <button type="submit" name="change_password">Şifreyi Değiştir</button>
    </form>

    
    <h2>Bakiye Yükleme</h2>
    <form action="customerActions.php" method="POST">
        <label for="balance_amount">Yüklenecek Bakiye:</label>
        <input type="number" id="balance_amount" name="balance_amount" step="0.01" required>
        <button type="submit" name="add_balance">Bakiye Yükle</button>
    </form>

   

    
    <h2>Siparişlerim</h2>
<form action="activeOrder.php" method="POST">
    <button type="submit" name="view_active_orders">Aktif Siparişleri Görüntüle</button>
</form>

<form action="customerActions.php" method="GET">
        <button type="submit" name="view_past_orders">Geçmiş Siparişleri Görüntüle</button>
    </form>

    
    <h2>Restoran Listeleme</h2>
    <form action="customerActions.php" method="GET">
        <button type="submit" name="list_restaurants">Tüm Restoranları Listele</button>
    </form>

</body>
</html>


