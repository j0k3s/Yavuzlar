<?php
session_start();


if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php"); 
    exit;
}

try {
    
    $pdo = new PDO('mysql:host=localhost;dbname=tezgah;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sayfası</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="admin-page">
    <h1>Admin Panel</h1>

    <img src="images/yavuzlarlogo.jpg" alt="yavuzlar" style="width:200px;height:auto;">

    <h2>Restoran İşlemleri</h2>
    
    <form action="adminActions.php" method="POST">
        <h3>Restoran Ekle</h3>
        <label for="restoran_name">Restoran Adı:</label>
        <input type="text" name="restoran_name" required><br><br>
        <label for="restoran_description">Restoran Açıklama:</label>
        <textarea name="restoran_description" required></textarea><br><br>
        <input type="submit" name="add_restaurant" value="Restoran Ekle">
    </form>

    
    <form action="adminActions.php" method="POST">
        <h3>Restoran Güncelle</h3>
        <label for="restoran_id">Restoran ID:</label>
        <input type="text" name="restoran_id" required><br><br>
        <label for="restoran_name">Yeni Restoran Adı:</label>
        <input type="text" name="restoran_name"><br><br>
        <label for="restoran_description">Yeni Restoran Açıklama:</label>
        <textarea name="restoran_description"></textarea><br><br>
        <input type="submit" name="update_restaurant" value="Restoran Güncelle">
    </form>

    
    <form action="adminActions.php" method="POST">
        <h3>Restoran Soft Silme</h3>
        <label for="restoran_id">Restoran ID:</label>
        <input type="text" name="restoran_id" required><br><br>
        <input type="submit" name="soft_delete_restaurant" value="Restoran Soft Sil">
    </form>

    
    <form action="adminActions.php" method="GET">
        <h3>Restoran Ara</h3>
        <label for="search_restaurant">Restoran Adı:</label>
        <input type="text" name="search_restaurant"><br><br>
        <input type="submit" name="search_restaurant_action" value="Restoran Ara">
    </form>

    
    <form action="adminActions.php" method="GET">
        <h3>Restoranları Listele</h3>
        <input type="submit" name="list_restaurants" value="Restoranları Listele">
    </form>

    <hr>

   
    <h2>Müşteri İşlemleri</h2>
  
    <form action="adminActions.php" method="POST">
        <h3>Müşteri Ekle</h3>
        <label for="customer_name">Müşteri Adı:</label>
        <input type="text" name="customer_name" required><br><br>
        <label for="customer_surname">Müşteri Soyadı:</label>
        <input type="text" name="customer_surname" required><br><br>
        <label for="customer_username">Kullanıcı Adı:</label>
        <input type="text" name="customer_username" required><br><br>
        <label for="customer_password">Şifre:</label>
        <input type="password" name="customer_password" required><br><br>
        <input type="submit" name="add_customer" value="Müşteri Ekle">
    </form>

    <form action="adminActions.php" method="POST">
        <h3>Müşteri Güncelle</h3>
        <label for="customer_id">Müşteri ID:</label>
        <input type="text" name="customer_id" required><br><br>
        <label for="customer_name">Yeni Müşteri Adı:</label>
        <input type="text" name="customer_name"><br><br>
        <label for="customer_surname">Yeni Müşteri Soyadı:</label>
        <input type="text" name="customer_surname"><br><br>
        <input type="submit" name="update_customer" value="Müşteri Güncelle">
    </form>

    
    <form action="adminActions.php" method="POST">
        <h3>Müşteri Soft Silme (Banlama)</h3>
        <label for="customer_id">Müşteri ID:</label>
        <input type="text" name="customer_id" required><br><br>
        <input type="submit" name="soft_delete_customer" value="Müşteriyi Soft Sil (Banla)">
    </form>

    <form action="adminActions.php" method="GET">
        <h3>Müşteri Ara</h3>
        <label for="search_customer">Müşteri Adı:</label>
        <input type="text" name="search_customer"><br><br>
        <input type="submit" name="search_customer_action" value="Müşteri Ara">
    </form>

    
    <form action="adminActions.php" method="GET">
        <h3>Müşterileri Listele</h3>
        <label for="filter_customers">Filtrele:</label>
        <select name="filter_customers">
            <option value="all">Tüm Müşteriler</option>
            <option value="active">Aktif Müşteriler</option>
            <option value="deleted">Silinmiş Müşteriler</option>
        </select><br><br>
        <input type="submit" name="list_customers" value="Müşterileri Listele">
    </form>

    <form action="adminActions.php" method="GET">
        <h3>Müşterinin Siparişlerini Görüntüle</h3>
        <label for="customer_id">Müşteri ID:</label>
        <input type="text" name="customer_id" required><br><br>
        <input type="submit" name="view_customer_orders" value="Siparişleri Görüntüle">
    </form>

</body>
</html>

