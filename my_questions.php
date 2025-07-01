<?php
session_start();
include 'db_connection.php'; // Include your database connection file

// Ensure the user is logged in
if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'student') {
    echo "Access denied. Only students can view this page.";
    exit();
}

$user_id = $_SESSION['user']['id']; // Logged-in student's ID

// Fetch questions and their corresponding answers
$sql = "
    SELECT q.question_text, q.category, q.created_at, a.answer_text, a.answered_at, p.name AS professional_name
    FROM questions q
    LEFT JOIN answers a ON q.id = a.question_id
    LEFT JOIN professionals p ON a.professional_id = p.id
    WHERE q.user_id = ?
    ORDER BY q.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$questions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Questions and Answers</title>
    <link rel="stylesheet" href="my_questions.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    
</head>
<body>
    <div class="container">
        <h1>My query history</h1>
        <a href="login.php" class="back-button">Back to Home</a>

        <?php if (count($questions) > 0): ?>
            <?php foreach ($questions as $question): ?>
                <div class="question-block">
                    <p><strong>Question:</strong> <?= htmlspecialchars($question['question_text']) ?></p>
                    <p><em>Category:</em> <?= htmlspecialchars($question['category']) ?></p>
                    <p><em>Asked on:</em> <?= htmlspecialchars(date('F j, Y, g:i a', strtotime($question['created_at']))) ?></p>
                </div>

                <div class="ans">

                    <?php if (!empty($question['answer_text'])): ?>
                        <p><strong>Answer:</strong> <?= htmlspecialchars($question['answer_text']) ?></p>
                        <p><em>Answered by:</em> <?= htmlspecialchars($question['professional_name'] ?: 'AI') ?></p>
                        <p><em>Answered on:</em> <?= htmlspecialchars(date('F j, Y, g:i a', strtotime($question['answered_at']))) ?></p>
                    <?php else: ?>
                        <p><em>No answer yet.</em></p>
                    <?php endif; ?>
                </div>
                
            <?php endforeach; ?>
        <?php else: ?>
            <p>No questions found.</p>
        <?php endif; ?>

       
    </div>
</body>
</html>
