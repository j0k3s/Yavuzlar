<?php
try {
    $db = new PDO('sqlite:Quest.db');
    $result = $db->query("SELECT * FROM questions");

   
    $questions = [];
    foreach ($result as $row) {
        $questions[] = $row;
    }
} catch (PDOException $e) {
    echo "Sorular listelenirken hata oluştu: " . $e->getMessage();
}
?>
