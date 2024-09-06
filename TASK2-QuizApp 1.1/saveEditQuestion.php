<?php
include 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $question_id = $_POST['question_id'];
    $question_text = $_POST['question_text'];
    $option1 = $_POST['option1'];
    $option2 = $_POST['option2'];
    $option3 = $_POST['option3'];
    $option4 = $_POST['option4'];
    $true_option = $_POST['true_option'];
    $difficulty = $_POST['difficulty'];

   
    $query = $pdo->prepare("UPDATE questions SET question_text = ?, option1 = ?, option2 = ?, option3 = ?, option4 = ?, true_option = ?, difficulty = ? WHERE question_id = ?");
    $query->execute([$question_text, $option1, $option2, $option3, $option4, $true_option, $difficulty, $question_id]);

    
    echo "Soru başarıyla güncellendi!";
    header('Location: listPage.php'); 
    exit();
}
?>
