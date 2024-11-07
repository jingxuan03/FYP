<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

    include("connection.php");
    require_once 'functions.php';

    $user_data = check_login($con);
    $pdo = pdo_connect_mysql();
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
<?=template_header('Home')?>

<div class="featured">
    <h2>Xiaomi 14T</h2>
    <p>Master light, Capture night</p>
</div>
<div class="recentlyadded content-wrapper">
    <h2>Recently Added Products</h2>
    <div class="products">
        <?php foreach ($recently_added_products as $product): ?>
        <a href="index.php?page=product&id=<?=$product['id']?>" class="product">
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