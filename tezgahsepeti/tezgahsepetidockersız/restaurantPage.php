<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'shop') {
    header("Location: login.php"); 
    exit;
}


if (isset($_SESSION['success_message'])) {
    echo "<p class='success'>" . $_SESSION['success_message'] . "</p>";
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    echo "<p class='error'>" . $_SESSION['error_message'] . "</p>";
    unset($_SESSION['error_message']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restoran Yönetimi (Shop)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Restoran Yönetimi</h1>

    <img src="images/yavuzlarlogo.jpg" alt="yavuzlar" style="width:200px;height:auto;">

    
    <h2>Yemek Ekleme</h2>
    <form action="restaurantActions.php" method="POST">
        <label for="restaurant_id">Restoran ID:</label>
        <input type="number" id="restaurant_id" name="restaurant_id" required>

        <label for="food_name">Yemek Adı:</label>
        <input type="text" id="food_name" name="food_name" required>

        <label for="food_description">Açıklama:</label>
        <textarea id="food_description" name="food_description" required></textarea>

        <label for="food_price">Fiyat:</label>
        <input type="number" step="0.01" id="food_price" name="food_price" required>

        <label for="food_discount">İndirim (%):</label>
        <input type="number" step="0.01" id="food_discount" name="food_discount">

        <button type="submit" name="add_food">Yemek Ekle</button>
    </form>

    
    <h2>Yemek Güncelleme</h2>
    <form action="restaurantActions.php" method="POST">
        <label for="food_id">Yemek ID:</label>
        <input type="number" id="food_id" name="food_id" required>

        <label for="food_name">Yeni Yemek Adı:</label>
        <input type="text" id="food_name" name="food_name" required>

        <label for="food_description">Yeni Açıklama:</label>
        <textarea id="food_description" name="food_description" required></textarea>

        <label for="food_price">Yeni Fiyat:</label>
        <input type="number" step="0.01" id="food_price" name="food_price" required>

        <label for="food_discount">Yeni İndirim (%):</label>
        <input type="number" step="0.01" id="food_discount" name="food_discount">

        <button type="submit" name="update_food">Yemek Güncelle</button>
    </form>

   
    <h2>Yemek Silme</h2>
    <form action="restaurantActions.php" method="POST">
        <label for="food_id_delete">Yemek ID:</label>
        <input type="number" id="food_id_delete" name="food_id" required>
        <button type="submit" name="soft_delete_food">Yemeği Sil (Soft Delete)</button>
    </form>

    <h2>Kupon Ekleme</h2>
    <form action="restaurantActions.php" method="POST">
        <label for="restaurant_id_coupon">Restoran ID:</label>
        <input type="number" id="restaurant_id_coupon" name="restaurant_id" required>

        <label for="coupon_name">Kupon Adı:</label>
        <input type="text" id="coupon_name" name="coupon_name" required>

        <label for="coupon_discount">İndirim (%):</label>
        <input type="number" step="0.01" id="coupon_discount" name="coupon_discount" required>

        <button type="submit" name="add_coupon">Kupon Ekle</button>
    </form>

    
    <h2>Kupon Güncelleme</h2>
    <form action="restaurantActions.php" method="POST">
        <label for="coupon_id">Kupon ID:</label>
        <input type="number" id="coupon_id" name="coupon_id" required>

        <label for="coupon_name">Yeni Kupon Adı:</label>
        <input type="text" id="coupon_name" name="coupon_name" required>

        <label for="coupon_discount">Yeni İndirim (%):</label>
        <input type="number" step="0.01" id="coupon_discount" name="coupon_discount" required>

        <button type="submit" name="update_coupon">Kupon Güncelle</button>
    </form>

  
    <h2>Kupon Silme</h2>
    <form action="restaurantActions.php" method="POST">
        <label for="coupon_id_delete">Kupon ID:</label>
        <input type="number" id="coupon_id_delete" name="coupon_id" required>
        <button type="submit" name="soft_delete_coupon">Kuponu Sil</button>
    </form>

    <h2>Yemek ve Kupon Arama</h2>
    <form action="restaurantActions.php" method="GET">
        <label for="search_food">Yemek Ara:</label>
        <input type="text" id="search_food" name="search_food">
        <button type="submit">Ara</button>
    </form>

    <form action="restaurantActions.php" method="GET">
        <label for="search_coupon">Kupon Ara:</label>
        <input type="text" id="search_coupon" name="search_coupon">
        <button type="submit">Ara</button>
    </form>

    
    <h2>Yemek ve Kupon Listeleme</h2>
    <form action="restaurantActions.php" method="GET">
        <button type="submit" name="list_foods">Tüm Yemekleri Listele</button>
    </form>

    <form action="restaurantActions.php" method="GET">
        <button type="submit" name="list_coupons">Tüm Kuponları Listele</button>
    </form>

    <h2>Restoran Arama ve Listeleme</h2>
    <form action="restaurantActions.php" method="GET">
        <label for="search_restaurant">Restoran Ara:</label>
        <input type="text" id="search_restaurant" name="search_restaurant">
        <button type="submit">Ara</button>
    </form>

    <form action="restaurantActions.php" method="GET">
        <button type="submit" name="list_restaurants">Tüm Restoranları Listele</button>
    </form>

 
    <h2>Sipariş Durum Güncelleme</h2>
    <form action="restaurantActions.php" method="POST">
        <label for="order_id">Sipariş ID:</label>
        <input type="number" id="order_id" name="order_id" required>

        <label for="order_status">Sipariş Durumu:</label>
        <select id="order_status" name="order_status" required>
            <option value="Hazırlanıyor">Hazırlanıyor</option>
            <option value="Yolda">Yolda</option>
            <option value="Teslim Edildi">Teslim Edildi</option>
        </select>

        <button type="submit" name="update_order_status">Sipariş Durumunu Güncelle</button>
    </form>

    <h2>Sipariş Veren Müşteri Profilini Görüntüleme</h2>
    <form action="restaurantActions.php" method="GET">
        <label for="order_id_customer">Sipariş ID:</label>
        <input type="number" id="order_id_customer" name="order_id">
        <button type="submit" name="view_order_customer">Müşteri Profilini Görüntüle</button>
    </form>
</body>
</html>
