<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "Lütfen giriş yapın.";
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "SELECT id, user_id, order_status, total_price, created_at 
          FROM `order` 
          WHERE user_id = :user_id
          ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$orders = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişleriniz</title>
</head>
<body>
    <h1>Siparişleriniz</h1>

    <?php if (count($orders) > 0): ?>
        <table border="1">
            <tr>
                <th>Sipariş No</th>
                <th>Kullanıcı ID</th>
                <th>Sipariş Durumu</th>
                <th>Toplam Tutar</th>
                <th>Sipariş Tarihi</th>
            </tr>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                    <td><?php echo htmlspecialchars($order['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                    <td><?php echo htmlspecialchars($order['total_price']); ?> TL</td>
                    <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Siparişiniz bulunmamaktadır.</p>
    <?php endif; ?>

    <br>
    <a href="customerPage.php">Müşteri Sayfasına Dön</a>
</body>
</html>