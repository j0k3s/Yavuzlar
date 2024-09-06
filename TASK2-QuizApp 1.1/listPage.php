<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #444;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .action-btns {
            display: flex;
            gap: 10px;
        }

        .edit-btn, .delete-btn {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        .edit-btn {
            background-color: #4CAF50;
            color: white;
        }

        .edit-btn:hover {
            background-color: #45a049;
        }

        .delete-btn {
            background-color: #f44336;
            color: white;
        }

        .delete-btn:hover {
            background-color: #e53935;
        }

        .login-btn, .add-btn {
            display: block;
            width: 200px;
            margin: 30px auto;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            text-align: center;
            border: none;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
        }

        .login-btn:hover, .add-btn:hover {
            background-color: #0056b3;
        }

    </style>
</head>
<body>
<h1>Question List</h1>
<table>
    <tr>
        <th>ID</th>
        <th>Question</th>
        <th>Option 1</th>
        <th>Option 2</th>
        <th>Option 3</th>
        <th>Option 4</th>
        <th>Correct Option</th>
        <th>Difficulty</th>
        <th>Actions</th>
    </tr>
    <?php include 'list_questions.php'; ?>
    <?php foreach ($questions as $question): ?>
    <tr>
        <td><?php echo htmlspecialchars($question['question_id']); ?></td>
        <td><?php echo htmlspecialchars($question['question_text']); ?></td>
        <td><?php echo htmlspecialchars($question['option1']); ?></td>
        <td><?php echo htmlspecialchars($question['option2']); ?></td>
        <td><?php echo htmlspecialchars($question['option3']); ?></td>
        <td><?php echo htmlspecialchars($question['option4']); ?></td>
        <td><?php echo htmlspecialchars($question['true_option']); ?></td>
        <td><?php echo htmlspecialchars($question['difficulty']); ?></td>
        <td>
            <div class="action-btns">
                <a href="editQuestion.php?id=<?php echo $question['question_id']; ?>">
                    <button class="edit-btn">Edit</button>
                </a>
                <a href="deleteQuestion.php?id=<?php echo $question['question_id']; ?>" onclick="return confirm('Are you sure you want to delete this question?');">
                    <button class="delete-btn">Delete</button>
                </a>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<a href="login.html" class="login-btn">Go to Login Page</a>
<a href="addQuestion.html" class="add-btn">Add Question</a>
</body>
</html>
