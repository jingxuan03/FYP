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
    
    // Record the product view
    record_product_view($pdo, $user_data['user_id'], $product['id']);
} else {
    exit('Product does not exist!');
}

// Function to record a product view
function record_product_view($pdo, $user_id, $product_id) {
    // Check if this view is already recorded to avoid duplicates
    $stmt = $pdo->prepare("SELECT * FROM user_views WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);

    if ($stmt->rowCount() === 0) {
        // If this product hasn't been viewed before, insert it
        $stmt = $pdo->prepare("INSERT INTO user_views (user_id, product_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $product_id]);
    } else {
        // Update the timestamp for an existing view
        $stmt = $pdo->prepare("UPDATE user_views SET view_time = CURRENT_TIMESTAMP WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
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
<?=template_header2('Product')?> <!-- Assuming you have a header template -->

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
            <p><?= htmlspecialchars($product['desc']) ?></p>
        </div>
    </div>
</div>

<?=template_footer()?> <!-- Assuming you have a footer template -->
</body>
</html>
