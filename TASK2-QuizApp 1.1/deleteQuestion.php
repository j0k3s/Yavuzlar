<?php
include 'db.php'; 

if (isset($_GET['id'])) {
    $question_id = $_GET['id'];

    $query = $pdo->prepare("DELETE FROM questions WHERE question_id = ?");
    $query->execute([$question_id]);

    
    echo "Soru başarıyla silindi!";
    header('Location: listPage.php'); 
    exit();
}
?>
