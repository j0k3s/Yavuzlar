<?php
session_start();
require 'db.php';


$sql = "SELECT * FROM restaurant";
$stmt = $pdo->query($sql);
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tüm Restoranlar</title>
</head>
<body>
    <form action="customerPage.php" method="GET">
        <button type="submit" name="view_profile">Ana sayfaya dön</button>
    </form>

    <h1>Tüm Restoranlar:</h1>

    <?php
    if ($restaurants) {
        foreach ($restaurants as $restaurant) {
            echo "<div class='restaurant'>";
            echo "<h2>Restoran Adı: " . htmlspecialchars($restaurant['name']) . "</h2>";
            echo "<p>Açıklama: " . htmlspecialchars($restaurant['description']) . "</p>";

            $sql = "SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.restaurant_id = :restaurant_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['restaurant_id' => $restaurant['id']]);
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

          
            if ($comments) {
                echo "<h4>Yapılan Yorumlar:</h4>";
                foreach ($comments as $comment) {
                    echo "<div class='comment'>";
                    echo "<strong>" . htmlspecialchars($comment['username']) . ":</strong> ";
                    echo "<em>" . htmlspecialchars($comment['title']) . "</em><br>";
                    echo "<p>" . htmlspecialchars($comment['description']) . "</p>";
                    echo "<p>Skor: " . htmlspecialchars($comment['score']) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p class='no-comments'>Bu restoran için henüz yorum yapılmamış.</p>";
            }

            echo "</div>";
        }
    } else {
        echo "<p>Henüz restoran eklenmemiş.</p>";
    }
    ?>

    <?php
    
    $sql = "SELECT r.id, r.name, r.description, AVG(c.score) as average_score
            FROM restaurant r
            LEFT JOIN comments c ON r.id = c.restaurant_id
            GROUP BY r.id
            ORDER BY r.name";

    $stmt = $pdo->query($sql);
    $restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
    echo "<h2>Tüm Restoranlar ve Ortalama Puanları:</h2>";
    foreach ($restaurants as $restaurant) {
        echo "<div class='restaurant'>";
        echo "<h3>" . htmlspecialchars($restaurant['name']) . "</h3>";
        echo "<p>" . htmlspecialchars($restaurant['description']) . "</p>";
        
       
        $averageScore = $restaurant['average_score'] ? number_format($restaurant['average_score'], 1) : 'Henüz puan yok';
        echo "<p>Ortalama Puan: " . $averageScore . "</p>";
        
        echo "<hr>";
        echo "</div>";
    }
    ?>
</body>
</html>
