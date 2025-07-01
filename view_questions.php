<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Handle upvote logic
if (isset($_POST['upvote_answer']) && isset($_SESSION['user'])) {
    $answer_id = $_POST['answer_id'];
    $user_id = $_SESSION['user']['id'];

    $check_sql = "SELECT id FROM answer_upvotes WHERE answer_id = ? AND user_id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$answer_id, $user_id]);

    if ($check_stmt->rowCount() == 0) {
        $upvote_sql = "INSERT INTO answer_upvotes (answer_id, user_id) VALUES (?, ?)";
        $upvote_stmt = $pdo->prepare($upvote_sql);
        $upvote_stmt->execute([$answer_id, $user_id]);
    } else {
        $remove_sql = "DELETE FROM answer_upvotes WHERE answer_id = ? AND user_id = ?";
        $remove_stmt = $pdo->prepare($remove_sql);
        $remove_stmt->execute([$answer_id, $user_id]);
    }
}

// Handle professional answering logic
if (isset($_POST['answer_question']) && $_SESSION['type'] === 'professional') {
    $question_id = $_POST['question_id'];
    $answer_text = $_POST['answer_text'];
    $professional_id = $_SESSION['user']['id'];

    $sql = "INSERT INTO answers (question_id, professional_id, answer_text, answered_at) VALUES (?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$question_id, $professional_id, $answer_text]);
}

// Handle adding comments
if (isset($_POST['add_comment']) && isset($_SESSION['user'])) {
    $answer_id = $_POST['answer_id'];
    $comment_text = $_POST['comment_text'];
    $user_id = $_SESSION['user']['id'];

    $sql_comment = "INSERT INTO comments (answer_id, user_id, comment_text, commented_at) VALUES (?, ?, ?, NOW())";
    $stmt_comment = $pdo->prepare($sql_comment);
    $stmt_comment->execute([$answer_id, $user_id, $comment_text]);
}

// Handle deleting questions
if (isset($_POST['delete_question']) && isset($_SESSION['user'])) {
    $question_id = $_POST['question_id'];

    $delete_question_sql = "DELETE FROM questions WHERE id = ? AND user_id = ?";
    $delete_question_stmt = $pdo->prepare($delete_question_sql);
    $delete_question_stmt->execute([$question_id, $_SESSION['user']['id']]);

    
    $delete_answers_sql = "DELETE FROM answers WHERE question_id = ?";
    $delete_answers_stmt = $pdo->prepare($delete_answers_sql);
    $delete_answers_stmt->execute([$question_id]);
}

// Handle deleting answers
if (isset($_POST['delete_answer']) && isset($_SESSION['user'])) {
    $answer_id = $_POST['answer_id'];

    $delete_answer_sql = "DELETE FROM answers WHERE id = ? AND professional_id = ?";
    $delete_answer_stmt = $pdo->prepare($delete_answer_sql);
    $delete_answer_stmt->execute([$answer_id, $_SESSION['user']['id']]);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questions & Answers</title>
    <link rel="stylesheet" href="./styles/style_view_questions.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <h1 style="text-align: center;">Questions & Answers</h1>
        <nav style="text-align: center; margin : 30px">
            <a href="index.php" style="font-size: 35px; margin : 20px">Home</a>
            <a href="logout.php" style="font-size: 35px; margin : 20px">Logout</a>
        </nav>
    </header>

    <div class="container">
      
        <form method="GET" action="">
            <label for="category">Filter by Category:</label>
            <select name="category" id="category" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="higher_studies" " . (isset($_GET['category']) && $_GET['category'] === 'higher_studies' ? 'selected' : '') . ">Higher Studies</option>
                <option value="career_guidance" " . (isset($_GET['category']) && $_GET['category'] === 'career_guidance' ? 'selected' : '') . ">Career Guidance</option>
                <option value="mental_health" " . (isset($_GET['category']) && $_GET['category'] === 'mental_health' ? 'selected' : '') . ">Mental Health</option>
            </select>
        </form>

        <?php
        // Fetch questions based on filter
        $sql = "SELECT q.id, q.question_text, q.category, q.user_id, u.name AS student_name
                FROM questions q
                JOIN users u ON q.user_id = u.id";

        if (isset($_GET['category']) && !empty($_GET['category'])) {
            $sql .= " WHERE q.category = :category";
        }

        $stmt = $pdo->prepare($sql);

        if (isset($_GET['category']) && !empty($_GET['category'])) {
            $stmt->execute(['category' => $_GET['category']]);
        } else {
            $stmt->execute();
        }

        while ($row = $stmt->fetch()) {
            echo '<div class="questions">';
            echo '<p><strong>Question by ' . htmlspecialchars($row['student_name']) . ':</strong> ' . htmlspecialchars($row['question_text']) . '</p>';
            echo '<p><em>Category: ' . htmlspecialchars($row['category']) . '</em></p>';

            
            if ($_SESSION['user']['id'] === $row['user_id']) {
                echo '<form method="POST">';
                echo '<input type="hidden" name="question_id" value="' . $row['id'] . '">';
                echo '<button type="submit" name="delete_question">Delete Question</button>';
                echo '</form>';
            }

           
            $question_id = $row['id'];
            $sql_answers = "SELECT a.id, a.answer_text, a.professional_id, p.name AS professional_name, a.answered_at,
                            COUNT(DISTINCT av.id) as upvote_count
                            FROM answers a
                            JOIN professionals p ON a.professional_id = p.id
                            LEFT JOIN answer_upvotes av ON a.id = av.answer_id
                            WHERE a.question_id = ?
                            GROUP BY a.id";
            $stmt_answers = $pdo->prepare($sql_answers);
            $stmt_answers->execute([$question_id]);

            if ($stmt_answers->rowCount() > 0) {
                while ($answer_row = $stmt_answers->fetch()) {
                    echo '<div class="answer">';
                    echo '<p><strong>Answer by ' . htmlspecialchars($answer_row['professional_name']) . ':</strong> ' . htmlspecialchars($answer_row['answer_text']) . '</p>';
                    echo '<p><em>Answered on ' . date('F j, Y, g:i a', strtotime($answer_row['answered_at'])) . '</em></p>';
                    echo '<p class="upvote-section">';
                    echo '<span>' . $answer_row['upvote_count'] . ' upvotes</span>';

                    if (isset($_SESSION['user'])) {
                        echo '<form method="POST" class="upvote-form">';
                        echo '<input type="hidden" name="answer_id" value="' . $answer_row['id'] . '">';
                        echo '<button type="submit" name="upvote_answer">Upvote</button>';
                        echo '</form>';
                    }

                    echo '</p>';

                    // Delete answer button (only for the author)
                    if ($_SESSION['user']['id'] === $answer_row['professional_id']) {
                        echo '<form method="POST">';
                        echo '<input type="hidden" name="answer_id" value="' . $answer_row['id'] . '">';
                        echo '<button type="submit" name="delete_answer">Delete Answer</button>';
                        echo '</form>';
                    }

                    // Fetch comments for the answer
                    $answer_id = $answer_row['id'];
                    $sql_comments = "SELECT c.comment_text, u.name AS commenter_name, c.commented_at
                                     FROM comments c
                                     JOIN users u ON c.user_id = u.id
                                     WHERE c.answer_id = ?";
                    $stmt_comments = $pdo->prepare($sql_comments);
                    $stmt_comments->execute([$answer_id]);

                    echo '<div class="comments">';
                    echo '<h4>Comments:</h4>';
                    while ($comment_row = $stmt_comments->fetch()) {
                        echo '<p><strong>' . htmlspecialchars($comment_row['commenter_name']) . ':</strong> ' . htmlspecialchars($comment_row['comment_text']) . '</p>';
                        echo '<p><em>Commented on ' . date('F j, Y, g:i a', strtotime($comment_row['commented_at'])) . '</em></p>';
                    }

                    // Add comment form
                    if (isset($_SESSION['user'])) {
                        echo '<form method="POST">';
                        echo '<input type="hidden" name="answer_id" value="' . $answer_id . '">';
                        echo '<textarea name="comment_text" placeholder="Add a comment" required></textarea>';
                        echo '<button type="submit" name="add_comment">Submit Comment</button>';
                        echo '</form>';
                    }

                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p><em>No answers yet.</em></p>';
            }

            // Answer form for professionals
            if ($_SESSION['type'] === 'professional') {
                echo '<form method="POST">';
                echo '<input type="hidden" name="question_id" value="' . $row['id'] . '">';
                echo '<textarea name="answer_text" placeholder="Enter your answer" required></textarea>';
                echo '<button type="submit" name="answer_question">Submit Answer</button>';
                echo '</form>';
            }

            echo '</div>';
        }
        ?>
    </div>
</body>
</html>
