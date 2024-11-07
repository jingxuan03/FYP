<?php
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
        <a href="alogin.php" class="btn">
            <img src="admin-icon.png" alt="Admin Icon" class="icon"> Admin Login
        </a>
        <a href="clogin.php" class="btn">
            <img src="customer-icon.png" alt="Customer Icon" class="icon"> Customer Login
        </a>
        <a href="slogin.php" class="btn">
            <img src="seller-icon.png" alt="Seller Icon" class="icon"> Seller Login
        </a>
    </div>
    <?=template_footer()?>
</body>
</html>
