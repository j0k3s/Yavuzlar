<?php
include 'db.php'; 

if (isset($_GET['id'])) {
    $question_id = $_GET['id'];

    $query = $pdo->prepare("SELECT * FROM questions WHERE question_id = ?");
    $query->execute([$question_id]);
    $question = $query->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Question</title>
    
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>Edit Question</h1>
    <form action="saveEditQuestion.php" method="post">
        <input type="hidden" name="question_id" value="<?php echo $question['question_id']; ?>">
        <label>Question Text:</label>
        <input type="text" name="question_text" value="<?php echo htmlspecialchars($question['question_text']); ?>" required><br>

        <label>Option 1:</label>
        <input type="text" name="option1" value="<?php echo htmlspecialchars($question['option1']); ?>" required><br>

        <label>Option 2:</label>
        <input type="text" name="option2" value="<?php echo htmlspecialchars($question['option2']); ?>" required><br>

        <label>Option 3:</label>
        <input type="text" name="option3" value="<?php echo htmlspecialchars($question['option3']); ?>" required><br>

        <label>Option 4:</label>
        <input type="text" name="option4" value="<?php echo htmlspecialchars($question['option4']); ?>" required><br>

        <label>Correct Option:</label>
        <input type="text" name="true_option" value="<?php echo htmlspecialchars($question['true_option']); ?>" required><br>

        <label>Difficulty:</label>
        <input type="text" name="difficulty" value="<?php echo htmlspecialchars($question['difficulty']); ?>" required><br>

        <button type="submit">Save</button>
    </form>
</body>
</html>
