<?php
try {
    $pdo = new PDO('sqlite:Quest.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("
        SELECT u.username, COUNT(s.is_correct) * 10 AS score 
        FROM users u 
        JOIN submissions s ON u.user_id = s.user_id 
        WHERE s.is_correct = 1 
        GROUP BY u.user_id 
        ORDER BY score DESC
    ");

    echo "<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        table {
            width: 50%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

    </style>";

    echo "<h2>Scoreboard</h2>";
    echo "<table>";
    echo "<tr><th>Username</th><th>Score</th></tr>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr><td>{$row['username']}</td><td>{$row['score']}</td></tr>";
    }

    echo "</table>";

} catch (PDOException $e) {
    echo "Veritabanı hatası: " . $e->getMessage();
}
?>
