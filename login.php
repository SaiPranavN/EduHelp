<?php
include 'db_connection.php';


if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $userType = $_POST['user_type'];

    if ($userType === 'student') {
        $degree = $_POST['degree'];
        $college = $_POST['college'];
        $sql = "INSERT INTO users (name, email, password, degree, college) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $email, $password, $degree, $college]);
        echo "Student registered successfully!";
    } elseif ($userType === 'professional') {
        $phone = $_POST['phone'];
        $profession = $_POST['profession'];
        $organization = $_POST['organization'];
        $idCard = file_get_contents($_FILES['id_card']['tmp_name']); // For file upload
        $sql = "INSERT INTO professionals (name, email, phone_number, password, profession, organization, id_card) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $email, $phone, $password, $profession, $organization, $idCard]);
        echo "Professional registered successfully!";
    }
}

// User Login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $userType = $_POST['user_type'];

    if ($userType === 'student') {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            $_SESSION['type'] = 'student';
            header("Location: login.php");
        } else {
            echo "Invalid credentials for student!";
        }
    } elseif ($userType === 'professional') {
        $sql = "SELECT * FROM professionals WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $professional = $stmt->fetch();

        if ($professional && password_verify($password, $professional['password'])) {
            $_SESSION['user'] = $professional;
            $_SESSION['type'] = 'professional';
            header("Location: login.php");
        } else {
            echo "Invalid credentials for professional!";
        }
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
}


if (isset($_POST['ask_question']) && $_SESSION['type'] === 'student') {
    $question_text = $_POST['question_text'];
    $category = $_POST['category'];
    $user_id = $_SESSION['user']['id'];

    $sql = "INSERT INTO questions (user_id, question_text, category) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $question_text, $category]);
    echo "Question posted!";
}

// Answering Questions (For professionals)-----------------
if (isset($_POST['answer_question']) && $_SESSION['type'] === 'professional') {
    $question_id = $_POST['question_id'];
    $answer_text = $_POST['answer_text'];
    $professional_id = $_SESSION['user']['id'];

    // Insert new answer 
    $sql = "INSERT INTO answers (question_id, professional_id, answer_text, answered_at) VALUES (?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$question_id, $professional_id, $answer_text]);

    echo "Answer submitted!";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Support System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>

    <header>
        <h1>Students' Support & Aid portal (EduHelp)</h1>
    </header>

    <?php if (!isset($_SESSION['user'])): ?>
        <br>
        <h2 style="text-align: center;">Welcome to the EduHelp login and registration section!</h2>
        <h4 style="text-align: center;">(Please do login if you already have an EduHelp account. If not, do register!!)</h4>
        <a href="index.php">
            <button type="button" id="back_btn">Head back</button>
        </a>
        <div class="container">

            <h2 style="color: #007bff;">Login</h2>
            <form method="POST">
                <p>Please select user type</p>
                <select name="user_type" required>
                    <option value="student">Student</option>
                    <option value="professional">Professional</option>
                </select>
                <br>
                <label for="email">Please enter your email id</label><br><br>
                <input type="email" name="email" placeholder="Eg. xyz@abc.com" required><br>
                <label for="password">Enter your password</label><br><br>
                <input type="password" name="password" required><br>
                <button type="submit" name="login">Login</button><br><br>
                <button type="submit" name="forgot" id="fg_btn">Forgot password</button>
            </form>
            <hr><br>
            <h2>Register & create an account as a <span style="color: #007bff;">Student</span></h2><br>
            <form method="POST">
                <label>Please give us your full name</label><br>
                <input type="hidden" name="user_type" value="student"><br>
                <input type="text" name="name" placeholder="Enter your name" required><br>
                <label for="email">Enter your email address</label><br><br>
                <input type="email" name="email" placeholder="xyz@abc.com" required><br>
                <label for="password">Create a new password</label><br><br>
                <input type="password" name="password" placeholder="Password must be 8 characters long" required><br>
                <label for="degree">Let us know your field of study</label><br><br>
                <input type="text" name="degree" placeholder="Ex. Computer science and engineering" required><br>
                <label for="college">Enter the name of your college / university</label><br><br>
                <input type="text" name="college" placeholder="Ex. The National Institute of Engineering" required><br>
                <button type="submit" name="register">Register</button>
            </form>
            <br>

            <h2>Register and create an account as a <span style="color: #007bff;">professional</span></h2>
            <form method="POST" enctype="multipart/form-data">
                <label>Please give us your full name</label><br>
                <input type="hidden" name="user_type" value="professional"><br>
                <input type="text" name="name" placeholder="Ex. Joseph stalin" required><br>
                <label for="email">Enter your email address</label><br><br>
                <input type="email" name="email" placeholder="xyz@abc.com" required><br>
                <label for="phone">Enter your phone number</label><br><br>
                <input type="text" name="phone" placeholder="+91" required><br>
                <label for="password">Create a new password</label><br><br>
                <input type="password" name="password" placeholder="Password must be 8 characters long" required><br>
                <label for="profession">Please enter your current profession</label><br><br>
                <input type="text" name="profession" placeholder="Ex. IT support" required><br>
                <label for="organization">Enter the name of the organization you currently work in</label><br><br>
                <input type="text" name="organization" placeholder="Organization" required><br>
                <label for="id_card">Submit the image of your id card(.pdf,.jpeg)</label><br><br>
                <input type="file" name="id_card" required><br>
                <button type="submit" name="register">Register</button>
            </form>
        </div>
    <?php else: ?>
        <nav>
            <a href="login.php">Home</a>
            <?php if ($_SESSION['type'] === 'student'): ?>
                <a href="?ask_question" onclick="scrollDown(event)" id = "ask_quest">Ask Question</a>
            <?php endif; ?>
            <a href="view_questions.php" class="btn">View Questions & Answers</a>



            <a href="?logout">Logout</a>
        </nav>
        <section class="cover animate-slide-in">
            <h2 style="text-align: center;">Welcome to <span style="color: #007bff;">EduHelp&#8482;</span></h2><br>
            <h3 style="text-align: center;">This platform is designed to support students in three critical areas: mental health, higher education, and tech careers.</h3>


            <div class="content-section">
                <h3 style="color: #007bff; font-size:25px">Providing Mental Health Support</h3> <br>
                <p>Mental health is an essential part of your well-being, and we understand that student life can be demanding. The combination of academic pressure, social relationships, and personal life can often lead to stress, anxiety, or other emotional struggles.

                    Our platform connects you with licensed mental health professionals who are here to help you manage these challenges. Whether you’re facing stress, burnout, anxiety, or loneliness, our experts provide confidential advice tailored to your situation. You can ask about coping strategies, self-care tips, and how to maintain mental balance while juggling the demands of your student life.
                    You don’t have to face your challenges alone.</p>
            </div>


            <div class="content-section">
                <h3 style="color: #007bff;font-size:25px">Higher Studies Guidance</h3> <br>
                <p>Stepping into the job market can be a daunting experience, especially if it’s your first time. Preparing for your future career involves more than just having a degree—it requires a well-planned strategy. From building your resume to acing interviews and developing the right skills, our platform provides you with access to career professionals and industry experts who can guide you every step of the way.

                    Whether you're unsure of what career path to follow or need help with job applications, our platform covers essential topics like:</p><br>
                <ul class="list">
                    <li style="font-size: 16px;">
                        Crafting an impactful resume and cover letter.
                    </li>
                    <li style="font-size: 16px;">Preparing for job interviews with confidence.</li>
                    <li style="font-size: 16px;">Exploring internship opportunities and career options.</li>
                    <li style="font-size: 16px;">Learning about networking and building a professional profile.</li>
                </ul>
            </div>


            <div class="content-section">
                <h3 style="color: #007bff; font-size:25px">Tech Jobs Preparation</h3><br>
                <p>Thinking about continuing your education? The world of higher studies is filled with endless possibilities, but choosing the right program can feel overwhelming. Whether you are considering a master’s degree, PhD, or professional certification, our platform connects you with experienced educators who can help you make informed decisions.

                    Our professionals guide you on:</p> <br>
                <ul>
                    <li style="font-size: 16px;">
                        Crafting an impactful resume and cover letter.
                    </li>
                    <li style="font-size: 16px;">Choosing the right field or program that aligns with your career goals.</li>
                    <li style="font-size: 16px;">Preparing application materials and writing compelling personal statements.</li>
                    <li style="font-size: 16px;">Studying abroad—tips for choosing international programs and universities.</li>
                </ul>
            </div>
            <button id="scrollButton" style="position: fixed; bottom: 20px; right: 20px;">Scroll down</button>

        </section>
        <?php
        

        if (isset($_SESSION['user']) && $_SESSION['type'] !== 'professional') {
            echo '<h2 id = "his_head">Click here to view your history</h2>';
            echo '<button id = "his_btn" onclick="window.location.href=\'my_questions.php\'">View History</button>';
        }
        ?>

        <?php if ($_SESSION['type'] === 'professional'): ?>
            <h2 id="his_head">Click here to view your answer history</h2>
            <button id="his_btn" onclick="window.location.href='professional_history.php'">View Answer History</button>
        <?php endif; ?>

        <br>

        <div class="container">
            <?php

            if (isset($_GET['ask_question']) && $_SESSION['type'] === 'student') {
                echo '<h2>Ask a Question</h2>';
                echo '<form method="POST">
                    <textarea name="question_text" placeholder="Enter your question" required></textarea>
                    <select name="category" required>
                        <option value="higher_studies">Higher Studies</option>
                        <option value="tech_jobs">Tech Jobs</option>
                        <option value="mental_health">Mental Health</option>
                    </select>
                    <button type="submit" name="ask_question">Submit</button>
                  </form>';
            }
            //---------------------------
            //view_questions.php

            // Handle upvoting
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
                    echo "Answer upvoted successfully!";
                } else {
                   
                    $remove_sql = "DELETE FROM answer_upvotes WHERE answer_id = ? AND user_id = ?";
                    $remove_stmt = $pdo->prepare($remove_sql);
                    $remove_stmt->execute([$answer_id, $user_id]);
                    echo "Upvote removed!";
                }
            }


            //------------------------------

            if (isset($_GET['view_questions'])) {
                echo '<h2>Questions & Answers</h2>';
            
                // Filter form for categories
                echo '<form method="GET" action="">';
                echo '<input type="hidden" name="view_questions" value="1">';
                echo '<label for="category">Filter by Category:</label> ';
                echo '<select name="category" id="category" onchange="this.form.submit()">';
                echo '<option value="">All</option>';
                echo '<option value="higher_studies"' . (isset($_GET['category']) && $_GET['category'] === 'higher_studies' ? ' selected' : '') . '>Higher Studies</option>';
                echo '<option value="career_guidance"' . (isset($_GET['category']) && $_GET['category'] === 'career_guidance' ? ' selected' : '') . '>Career Guidance</option>';
                echo '<option value="mental_health"' . (isset($_GET['category']) && $_GET['category'] === 'mental_health' ? ' selected' : '') . '>Mental Health</option>';
                echo '</select>';
                echo '<noscript><button type="submit">Filter</button></noscript>';
                echo '</form>';
            
                // Build query with category filter
                $sql = "SELECT q.id, q.question_text, q.category, u.name AS student_name
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
            
                    // Display question
                    echo '<p><strong>Question by ' . htmlspecialchars($row['student_name']) . ':</strong> ' . htmlspecialchars($row['question_text']) . '</p>';
                    echo '<p><em>Category: ' . htmlspecialchars($row['category']) . '</em></p>';
            
                    // Fetch answers for the question
                    $question_id = $row['id'];
                    $sql_answers = "SELECT a.id, a.answer_text, p.name AS professional_name, a.answered_at,
                                    COUNT(DISTINCT av.id) as upvote_count
                                    FROM answers a
                                    JOIN professionals p ON a.professional_id = p.id
                                    LEFT JOIN answer_upvotes av ON a.id = av.answer_id
                                    WHERE a.question_id = ?
                                    GROUP BY a.id";
                    $stmt_answers = $pdo->prepare($sql_answers);
                    $stmt_answers->execute([$question_id]);
            
                    // Check for answers
                    if ($stmt_answers->rowCount() > 0) {
                        while ($answer_row = $stmt_answers->fetch()) {
                            echo '<div class="answer">';
                            echo '<p><strong>Answer by ' . htmlspecialchars($answer_row['professional_name']) . ':</strong> ' . htmlspecialchars($answer_row['answer_text']) . '</p>';
            
                            if (!empty($answer_row['answered_at'])) {
                                $answeredDate = date('F j, Y, g:i a', strtotime($answer_row['answered_at']));
                                echo '<p><em>Answered on ' . htmlspecialchars($answeredDate) . '</em></p>';
                                echo '<p class="upvote-section">';
                                echo '<span class="upvote-count">' . $answer_row['upvote_count'] . ' upvotes</span>';
            
                                if (isset($_SESSION['user'])) {
                                    $check_upvote_sql = "SELECT id FROM answer_upvotes 
                                                       WHERE answer_id = ? AND user_id = ?";
                                    $check_upvote_stmt = $pdo->prepare($check_upvote_sql);
                                    $check_upvote_stmt->execute([$answer_row['id'], $_SESSION['user']['id']]);
                                    $has_upvoted = $check_upvote_stmt->rowCount() > 0;
            
                                    echo '<form method="POST" class="upvote-form">';
                                    echo '<input type="hidden" name="answer_id" value="' . $answer_row['id'] . '">';
                                    echo '<button type="submit" name="upvote_answer" class="upvote-button ' .
                                        ($has_upvoted ? 'upvoted' : '') . '">';
                                    echo ($has_upvoted ? 'Remove Upvote' : 'Upvote');
                                    echo '</button>';
                                    echo '</form>';
                                }
                            } else {
                                echo '<p><em>Answer date not available</em></p>';
                            }
            
                            echo '</div>'; 
                        }
                    } else {
                        echo '<p><em>No answers yet</em></p>';

                        //------------------------
                        //AIzaSyCgcHUzMhHSjqW0h5plW8Bu_Y1pMYbJ4Lc





                        echo '<form method="POST">

                        <input type="hidden" name="question_text" value="' . htmlspecialchars($row['question_text']) . '">
                        <button type="submit" name="get_ai_answer">Get AI Answer</button>
                        </form>';
                    }

                    // Answer form for professionals
                    if ($_SESSION['type'] === 'professional') {
                        echo '<form method="POST">
                                <input type="hidden" name="question_id" value="' . $row['id'] . '">
                                <textarea name="answer_text" placeholder="Enter your answer" required></textarea>
                                <button type="submit" name="answer_question">Submit Answer</button>
                              </form>';
                    }

                    echo '</div>'; 
                }
            }


            ?>
        </div>
    <?php endif; ?>
    <footer>
        <h2 class="foot">EduHelp&#8482;</h2>
        <h5 class="foot" style="margin-bottom: 15px;">&#169; All rights reserved (2025)</h5>
        <h3 class="foot">Follow us on </h3><br>
        <div class="footer-social">

            <ul class="social-icons">
                <li><a href="www.meta.com"><img id="fb" src="project_images/fb.png" alt="Facebook"></a></li>
                <li><a href="www.x.com"><img src="project_images/x.png" alt="Twitter"></a></li>
                <li><a href="www.instagram.com"><img src="project_images/insta.png" alt="Instagram"></a></li>
                <li><a href="www.linkedin.com"><img src="project_images/ik.png" alt="LinkedIn"></a></li>
            </ul>
        </div>

    </footer>
    <script src="script.js"></script>
</body>

</html>