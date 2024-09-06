<?php
session_start();

try {
    $pdo = new PDO('sqlite:Quest.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_SESSION['user_id'])) {
        echo "Hata: Oturum açılmamış.";
        exit;
    }

    $userId = $_SESSION['user_id'];

    if (!isset($_SESSION['current_question'])) {
        $_SESSION['current_question'] = 0;
    }

    $currentQuestionIndex = $_SESSION['current_question'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userAnswer = $_POST['secenek'];

        $stmt = $pdo->prepare("SELECT * FROM questions LIMIT 1 OFFSET ?");
        $stmt->execute([$currentQuestionIndex - 1]);
        $previousQuestion = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($previousQuestion) {
            $isCorrect = ($userAnswer == $previousQuestion['true_option']) ? 1 : 0;

            $stmt = $pdo->prepare("INSERT INTO submissions (user_id, question_id, is_correct) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $previousQuestion['question_id'], $isCorrect]);
        }
    }

    $stmt = $pdo->prepare("SELECT * FROM questions LIMIT 1 OFFSET ?");
    $stmt->execute([$currentQuestionIndex]);
    $nextQuestion = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<link rel='stylesheet' type='text/css' href='style.css'>";

    if ($nextQuestion) {
        $_SESSION['current_question']++;

        echo "<form method='POST'>";
        echo "<p><strong>Soru " . ($currentQuestionIndex + 1) . ": {$nextQuestion['question_text']}</strong></p>";
        echo "<label><input type='radio' name='secenek' value='option1' required /> A) {$nextQuestion['option1']}</label><br />";
        echo "<label><input type='radio' name='secenek' value='option2' required /> B) {$nextQuestion['option2']}</label><br />";
        echo "<label><input type='radio' name='secenek' value='option3' required /> C) {$nextQuestion['option3']}</label><br />";
        echo "<label><input type='radio' name='secenek' value='option4' required /> D) {$nextQuestion['option4']}</label><br />";
        echo "<input type='submit' value='Sonraki Soru' />";
        echo "</form>";
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) as correct_count FROM submissions WHERE user_id = ? AND is_correct = 1");
        $stmt->execute([$userId]);
        $correctCount = $stmt->fetch(PDO::FETCH_ASSOC)['correct_count'];
        $score = $correctCount * 10;

        echo "<p>Quiz tamamlandı! Skorunuz: {$score}</p>";

        echo "<form action='scoreboard.php' method='get'>";
        echo "<input type='submit' value='Skor Tablosunu Gör' />";
        echo "</form>";

        unset($_SESSION['current_question']);
    }

} catch (PDOException $e) {
    echo "Veritabanı hatası: " . $e->getMessage();
}
?>
