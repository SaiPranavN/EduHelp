<?php
include 'db_connection.php';
session_start();
$email = $_SESSION['email'];

// Fetch Selected Newsletters
$sql = "SELECT n.title, n.description, n.image FROM newsletters n JOIN selected_newsletters s ON n.id = s.newsletter_id WHERE s.email = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);
$reading_list = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Reading List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>My Reading List</h1>
        <?php if (!empty($reading_list)): ?>
            <div class="newsletter-grid">
                <?php foreach ($reading_list as $newsletter): ?>
                    <div class="newsletter-card">
                        <img src="<?php echo htmlspecialchars($newsletter['image']); ?>" alt="<?php echo htmlspecialchars($newsletter['title']); ?>">
                        <h3><?php echo htmlspecialchars($newsletter['title']); ?></h3>
                        <p><?php echo htmlspecialchars($newsletter['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No newsletters in your reading list yet.</p>
        <?php endif; ?>
        <a href="subscribe.php" class="back-btn">Back to Subscribe</a>
    </div>
</body>
</html>
