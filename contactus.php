<?php
require_once 'functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Lunar</title>
    <link rel="stylesheet" href="contactus.css">
</head>
<body>
<?=template_header1('Contact Us')?>
    <div class="contact-container">
        <header>
            <h1>Contact Us</h1>
        </header>

        <section class="contact-info">
            <h2>Our Information</h2>
            <p>
                <strong>Lunar</strong><br>
                1234 Digital Avenue, Tech City, TC 56789<br>
                <strong>Email:</strong> <a href="mailto:support@lunartech.com">support@lunartech.com</a><br>
                <strong>Phone:</strong> +60 123 456 789
            </p>
        </section>

        <section class="contact-form">
            <h2>Send Us a Message</h2>
            <form action="submit_form.php" method="POST">
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Your Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="message">Your Message</label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="submit-btn">Send Message</button>
            </form>
        </section>
    </div>
    <?=template_footer()?>
</body>
</html>
