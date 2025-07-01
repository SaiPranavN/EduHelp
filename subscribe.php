<?php

include 'db_connection.php';


$message = "";

// Handle the subscription form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subscribe'])) {
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $preferred_topics = $_POST['preferred_topics']; 
   
    if (!$email) {
        $message = "Invalid email address.";
    } elseif (empty($preferred_topics)) {
        $message = "Please select at least one preferred topic.";
    } else {
        try {
            // Remove any previous subscriptions for this email
            $delete_sql = "DELETE FROM newsletter_subscribers WHERE email = ?";
            $delete_stmt = $pdo->prepare($delete_sql);
            $delete_stmt->execute([$email]);

            // Insert new subscriptions
            $insert_sql = "INSERT INTO newsletter_subscribers (email, preferred_topics) VALUES (?, ?)";
            $insert_stmt = $pdo->prepare($insert_sql);
            foreach ($preferred_topics as $topic) {
                $insert_stmt->execute([$email, $topic]);
            }

            // Set session to indicate user is subscribed
            $_SESSION['subscribed'] = true;

            $message = "Subscription successful!";
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
        }
    }
}

// Handle subscription cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_subscription'])) {
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);

    if (!$email) {
        $message = "Invalid email address.";
    } else {
        try {
            $cancel_sql = "DELETE FROM newsletter_subscribers WHERE email = ?";
            $cancel_stmt = $pdo->prepare($cancel_sql);
            $cancel_stmt->execute([$email]);

            // Remove subscription session
            $_SESSION['subscribed'] = false;

            $message = "Subscription canceled successfully!";
        } catch (PDOException $e) {
            $message = "Failed to cancel subscription: " . $e->getMessage();
        }
    }
}

// Check if user is subscribed
$is_subscribed = isset($_SESSION['subscribed']) && $_SESSION['subscribed'] === true;

// Handle "Buy" action (This would normally involve more complex logic)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy_newsletter'])) {
    $newsletter_title = $_POST['newsletter_title'];
    $message = "Thank you for purchasing '$newsletter_title'!";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Newsletter - Your Website</title>
    <link rel="stylesheet" href="./styles/style.css">
</head>
<body>
    <div class="container">
        <h2>Newsletter Subscription</h2>

        
        <?php if ($message): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        
        <?php if (!$is_subscribed): ?>
            <form method="POST" class="subscription-form">
                <h3>Subscribe to Our Newsletter</h3>
                <input type="email" name="email" placeholder="Enter your email" required>
                <p>Select your preferred topics:</p>
                <label><input type="checkbox" name="preferred_topics[]" value="higher_studies"> Higher Studies</label><br>
                <label><input type="checkbox" name="preferred_topics[]" value="career_guidance"> Career Guidance</label><br>
                <label><input type="checkbox" name="preferred_topics[]" value="mental_health"> Mental Health</label><br>
                <button type="submit" name="subscribe" class="primary-btn">Subscribe</button>
            </form>
        <?php else: ?>
            
            <form method="POST" class="subscription-form">
                <h3>Cancel Subscription</h3>
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit" name="cancel_subscription" class="secondary-btn">Cancel Subscription</button>
            </form>

           
            <h3>Available Newsletters</h3>
            <div class="newsletter-grid">
                <?php
                
                $newsletters = [
                    ["title" => "Higher Studies Opportunities", "image" => "project_images/stud.jpeg", "price" => "Rs. 345"],
                    ["title" => "Career Guidance Tips", "image" => "project_images/stud.jpeg", "price" => "Rs. 345"],
                    ["title" => "Mental Health Awareness", "image" => "project_images/stud.jpeg", "price" => "Rs. 345"],
                ];

                foreach ($newsletters as $newsletter) {
                    echo '<div class="newsletter-card">';
                    echo '<img src="' . htmlspecialchars($newsletter['image']) . '" alt="' . htmlspecialchars($newsletter['title']) . '">';
                    echo '<h4>' . htmlspecialchars($newsletter['title']) . '</h4>';
                    echo '<p class="price">' . htmlspecialchars($newsletter['price']) . '</p>';
                    echo '<form method="POST">';
                    echo '<input type="hidden" name="newsletter_title" value="' . htmlspecialchars($newsletter['title']) . '">';
                    echo '<button type="submit" name="buy_newsletter" class="buy-btn">Buy Now</button>';
                    echo '</form>';
                    echo '</div>';
                }
                ?>
            </div>
        <?php endif; ?>

       
        <a href="index.php" class="back-btn">Back to Home</a>
    </div>
</body>
</html>
