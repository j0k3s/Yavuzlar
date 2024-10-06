<?php
session_start();
require 'db.php'; 


if (!isset($_SESSION['user_id'])) {
    echo "Lütfen giriş yapın.";
    exit;
}

$user_id = $_SESSION['user_id'];


if (isset($_POST['place_order'])) {
    $totalPrice = isset($_POST['total_price']) ? $_POST['total_price'] : 0;
    
    $sql = "INSERT INTO `order` (user_id, order_status, total_price, created_at) 
            VALUES (:user_id, :order_status, :total_price, :created_at)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        'user_id' => $user_id,
        'order_status' => 'active',
        'total_price' => $totalPrice,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    if ($result) {
        echo "Sipariş başarıyla oluşturuldu! Sipariş ID: " . $pdo->lastInsertId();
       
        $clearBasketSql = "DELETE FROM basket WHERE user_id = :user_id";
        $clearStmt = $pdo->prepare($clearBasketSql);
        $clearStmt->execute(['user_id' => $user_id]);
        
        header("Location: basketPage.php");
        exit();
    } else {
        echo "Sipariş oluşturulurken bir hata oluştu: ";
        print_r($stmt->errorInfo());
    }
}

$stmt = $pdo->prepare("SELECT b.quantity, b.note, f.name, f.price, f.discount
                       FROM basket b
                       JOIN food f ON b.food_id = f.id
                       WHERE b.user_id = :user_id");
$stmt->execute([':user_id' => $user_id]);
$basketItems = $stmt->fetchAll();


if (!$basketItems) {
    echo "<p>Sepetinizde ürün bulunmamaktadır.</p>";
} else {
    echo "<h2>Sepetinizdeki Ürünler</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Ürün Adı</th><th>Miktar</th><th>Fiyat</th><th>İndirim</th><th>Not</th><th>Toplam Fiyat</th></tr>";

    $totalPrice = 0;

    foreach ($basketItems as $item) {
        $foodName = $item['name'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        $discount = $item['discount'];
        $note = $item['note'];
        
        $discountedPrice = $price - ($price * $discount / 100);
        $totalItemPrice = $discountedPrice * $quantity;

        $totalPrice += $totalItemPrice;

        echo "<tr>
                <td>{$foodName}</td>
                <td>{$quantity}</td>
                <td>{$price} ₺</td>
                <td>{$discount} %</td>
                <td>{$note}</td>
                <td>{$totalItemPrice} ₺</td>
              </tr>";
    }

    echo "</table>";
    echo "<p><strong>Toplam Fiyat: {$totalPrice} ₺</strong></p>";
}
?>

<form action="customerPage.php" method="GET">
    <button type="submit" name="view_profile">anasayfaya dön</button>
</form>

<form action="" method="POST">
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
    <input type="hidden" name="total_price" value="<?php echo $totalPrice; ?>">
    <button type="submit" name="place_order">Sipariş Ver</button>
</form>

<?php
if (isset($_GET['view_profile'])) {
    header("Location: customerPage.php");
    exit();
}
?>