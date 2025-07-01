<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduHelp</title>
    <link rel="shortcut icon" href="project_images/logo.jpeg" type="image/x-icon">
    <link rel="stylesheet" href="cover_style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="hero">
        <header class="header">
            <h1 class="logo">EduHelp</h1>
            <nav>
                <ul>
                    <li><a href="#about">About</a></li>
                    <li><a href="#about">Services</a></li>
                    <li><a href="javascript:void(0);" onclick="openContactForm()">Contact</a></li>
                    <li><a href="subscribe.php">Newsletter</a></li>
                </ul>
            </nav>
        </header>
        <div class="content">
            <h1 class="main-title">Empowering <span class="gradient-text">Students</span>, Transforming <span class="gradient-text">Lives</span></h1>
            <p class="subtitle">A platform for students to get guidance on education, career paths, and mental well-being.</p>
            <a href="login.php" class="cta-button">Login to Get Started</a>
        </div>
    </div>
   
    <div class="sections-container">
        <div class="section">
            <div class="text-content">
                <h3>Guidance for Studies</h3>
                <p>
                    Get personalized advice on higher education, including choosing the right programs, universities, and scholarships tailored to your career goals.
                </p>
            </div>
            <div class="image-content">
                <img src="project_images/stud.jpeg" alt="Studies Guidance">
            </div>
        </div>

        <div class="section">
            <div class="text-content" id = "about">
                <h3>Support for Mental Health</h3>
                <p>
                    Connect with mental health professionals and access resources to help manage stress, anxiety, and challenges in your personal and academic life.
                </p>
            </div>
            <div class="image-content">
                <img src="project_images/mental.jpeg" alt="Mental Health Support">
            </div>
        </div>

        <div class="section">
            <div class="text-content">
                <h3>Career Placement Assistance</h3>
                <p>
                    Discover the best practices for landing your dream job. From resume building to interview preparation, we've got you covered.
                </p>
            </div>
            <div class="image-content">
                <img src="/project_images/cargud.jpeg" alt="Career Placement">
            </div>
        </div>
    </div>

    <div id="testimonials" style="margin: 10px;">
            <h2>User Testimonials</h3>
            <div class="testimonial">
                <p style="font-size: 23px;">"EduHelp has transformed my academic journey. The guidance I received was invaluable!"</p>
                <p><strong>- Siddarth S</strong></p>
            </div>
            <div class="testimonial">
                <p style="font-size: 23px;">"As a professional, I found the platform to be a great way to connect with students and share my knowledge."</p>
                <p><strong>- Sanjay MS</strong></p>
            </div>
    </div>

        <hr>

        <div  id="faq" style="margin: 10px;">
            <h2>Frequently Asked Questions</h3>
            <div class="faq-item">
                <h2>How do I register?</h4>
                <p style="font-size: 23px;">You can register by clicking on the 'Register' button on the login page and filling out the required information.</p>
            </div>
            <div class="faq-item">
                <h2>What services does EduHelp offer?</h4>
                <p style="font-size: 23px;">EduHelp offers guidance on higher education, career paths, and mental well-being.</p>
            </div>
        </div>

        <div id="contactModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeContactForm()">&times;</span>
        <h2>Contact Us</h2>
        
        <form id="contactForm">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="message">Message:</label>
            <textarea id="message" name="message" required></textarea>
            <button type="submit">Send</button>
        </form>
    </div>
</div>

        <hr>

        
    

    
    
    <footer class="footer">

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

    <script>
function openContactForm() {
    document.getElementById("contactModal").style.display = "block";
}

function closeContactForm() {
    document.getElementById("contactModal").style.display = "none";
}

// Close the modal when clicking outside of it
window.onclick = function(event) {
    var modal = document.getElementById("contactModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

// Handle form submission
document.getElementById("contactForm").onsubmit = function(event) {
    event.preventDefault();
    alert("Thank you for contacting us, " + document.getElementById("name").value + "!");
    closeContactForm();
};
</script>
</body>

</html>