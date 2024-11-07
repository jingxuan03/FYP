<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("connection.php");
require_once 'functions.php';

// Verify that user is logged in and get user_id from session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Connect to the database
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=fypp', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch the seller's username from the users_seller table
        $stmt = $pdo->prepare('SELECT user_name FROM users_seller WHERE user_id = ?');
        $stmt->execute([$user_id]);
        $seller = $stmt->fetch(PDO::FETCH_ASSOC);

        // If seller is found, retrieve the username; otherwise, set as 'Guest'
        $seller_name = $seller ? $seller['user_name'] : 'Guest';
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        $seller_name = 'Guest';
    }
} else {
    $seller_name = 'Guest';
}

// Get the 4 most recently added products
$stmt = $pdo->prepare('SELECT * FROM products ORDER BY date_added DESC LIMIT 4');
$stmt->execute();
$recently_added_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Home Page</title>
    <link rel="stylesheet" href="cproducts.css">
</head>

<body>
<?=template_header2('Home')?>

<div class="featured">
    <h2>Welcome, <?= htmlspecialchars($seller_name) ?>!</h2> <!-- Display the seller's username -->
    <h2>Xiaomi 14T</h2>
    <p>Master light, Capture night</p>
</div>
<div class="recentlyadded content-wrapper">
    <h2>Recently Added Products</h2>
    <div class="products">
        <?php foreach ($recently_added_products as $product): ?>
        <a href="index.php?page=sproduct&id=<?=$product['id']?>" class="product">
            <img src="<?=$product['img']?>" width="200" height="200" alt="<?=$product['name']?>">
            <span class="name"><?=$product['name']?></span>
            <span class="price">
                RM <?=$product['price']?>
            </span>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<?=template_footer()?>

</body>
</html>
