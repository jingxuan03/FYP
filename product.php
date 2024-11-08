<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("connection.php");
require_once 'functions.php';

$user_data = check_login($con);  // Ensure user is logged in, assuming check_login handles this
$pdo = pdo_connect_mysql();

// Check if the id parameter is specified in the URL
if (isset($_GET['id'])) {
    // Prepare statement and execute, prevents SQL injection
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    // Fetch the product from the database and return the result as an Array
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the product exists
    if (!$product) {
        exit('Product does not exist!');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Seller Product Description Page</title>
    <link rel="stylesheet" href="cproducts.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
</head>

<body>
<?=template_header('Product')?>

<div class="product content-wrapper">
    <img src="<?= htmlspecialchars($product['img']) ?>" width="500" height="500" alt="<?= htmlspecialchars($product['name']) ?>">
    <div>
        <h1 class="name"><?= htmlspecialchars($product['name']) ?></h1>
        <span class="price">RM <?= htmlspecialchars($product['price']) ?></span>
        <form action="index.php?page=cart" method="post" style="display: inline;">
            <input type="number" name="quantity" value="1" min="1" max="<?= htmlspecialchars($product['quantity']) ?>" placeholder="Quantity" required>
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
            <input type="submit" value="Add To Cart">
        </form>
        <a href="comparison.php" class="compare-button">Compare</a>
        <div class="description">
            <span class="text">Product Description :</span>
            <p><?= nl2br(str_replace(['<p>', '</p>', '<br>'], ['', '', "\n"], $product['desc'])) ?></p>
        </div>
    </div>
</div>

<?=template_footer()?> 
</body>
</html>
