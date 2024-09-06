<?php
try {
    $pdo = new PDO('sqlite:Quest.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $question_text = $_POST['question_text'];
        $difficulty = $_POST['difficulty'];
        $option1 = $_POST['option1'];
        $option2 = $_POST['option2'];
        $option3 = $_POST['option3'];
        $option4 = $_POST['option4'];
        $correct_option = $_POST['correct_option'];

        $check_sql = "SELECT COUNT(*) FROM questions WHERE question_text = :question_text";
        $stmt = $pdo->prepare($check_sql);
        $stmt->execute([':question_text' => $question_text]);
        $exists = $stmt->fetchColumn();

        if ($exists == 0) {
            
            $sql = "INSERT INTO questions (question_text, option1, option2, option3, option4, true_option, difficulty)
                    VALUES (:question_text, :option1, :option2, :option3, :option4, :true_option, :difficulty)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':question_text' => $question_text,
                ':option1' => $option1,
                ':option2' => $option2,
                ':option3' => $option3,
                ':option4' => $option4,
                ':true_option' => $correct_option,
                ':difficulty' => $difficulty
            ]);

            echo "Soru başarıyla eklendi!";
        } else {
            echo "Bu soru zaten mevcut!";
        }

        
        header("Location: listPage.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Veritabanı hatası: " . $e->getMessage();
}
?>
