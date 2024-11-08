<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Options</title>
    <link rel="stylesheet" href="gateway.css">
</head>
<body>
<?=template_header1('Login Options')?>

<div class="container">
    <h1>Select Your Login Type</h1>

    <a href="#" class="btn" onclick="promptPassword()">
        <img src="admin-icon.png" alt="Admin Icon" class="icon"> Admin Login
    </a>

    <a href="clogin.php" class="btn">
        <img src="customer-icon.png" alt="Customer Icon" class="icon"> Customer Login
    </a>
    <a href="slogin.php" class="btn">
        <img src="seller-icon.png" alt="Seller Icon" class="icon"> Seller Login
    </a>
</div>

<script>
function promptPassword() {
    // Prompt the user for the password
    var password = prompt("Please enter the admin password:");

    // Define the correct admin password (you can change this to any password you want)
    var correctPassword = "1229";

    // Check if the entered password matches the correct one
    if (password === correctPassword) {
        // Redirect to the Admin Login page if the password is correct
        window.location.href = 'alogin.php';
        alert("Correct Password, Welcome Admin");
    } else {
        // Show an alert if the password is incorrect
        alert("Incorrect password. Access denied.");
    }
}
</script>

<?=template_footer()?>
</body>
</html>
