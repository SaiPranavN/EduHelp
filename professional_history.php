<?php
session_start();
include 'db_connection.php';

// Ensure the user is logged in and is a professional
if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'professional') {
    echo "Access denied. Only professionals can view this page.";
    exit();
}

$professional_id = $_SESSION['user']['id']; // Logged-in professional's ID

// Fetch questions and answers by this professional
$sql = "
    SELECT q.question_text, q.category, q.created_at, 
           a.answer_text, a.answered_at, 
           u.name AS student_name
    FROM answers a
    JOIN questions q ON a.question_id = q.id
    JOIN users u ON q.user_id = u.id
    WHERE a.professional_id = ?
    ORDER BY a.answered_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$professional_id]);
$answers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Answer History</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="professional_history_style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 style="text-align: center; margin-bottom:auto;">EduHelp</h1>
        <h1>My Answer History</h1>
        <button>
        <a href="login.php" class="back-button" id = "a_btn">Back to Home</a>
        </button>

        <?php if (count($answers) > 0): ?>
            <?php foreach ($answers as $answer): ?>
                <div class="question-block">
                    <p><strong>Question by <?= htmlspecialchars($answer['student_name']) ?>:</strong> 
                        <?= htmlspecialchars($answer['question_text']) ?></p>
                    <p><em>Category:</em> <?= htmlspecialchars($answer['category']) ?></p>
                    <p><em>Question asked on:</em> <?= htmlspecialchars(date('F j, Y, g:i a', strtotime($answer['created_at']))) ?></p>
                </div>

                <div class="ans">
                    <p><strong>Your Answer:</strong> <?= htmlspecialchars($answer['answer_text']) ?></p>
                    <p><em>Answered on:</em> <?= htmlspecialchars(date('F j, Y, g:i a', strtotime($answer['answered_at']))) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No answers found in your history.</p>
        <?php endif; ?>
    </div>
</body>
</html>