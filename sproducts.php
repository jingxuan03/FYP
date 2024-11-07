<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("connection.php");
require_once 'functions.php';

$user_data = check_login3($con);
$pdo = pdo_connect_mysql();

// The amounts of products to show on each page
$num_products_on_each_page = 50;

// The current page - in the URL, will appear as index.php?page=products&p=1, index.php?page=products&p=2, etc...
$current_page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;

// Select products ordered by the date added, excluding those with quantity 0, and join with users_seller to get seller information
$stmt = $pdo->prepare('
    SELECT products.*, users_seller.user_name
    FROM products
    LEFT JOIN users_seller ON products.seller_id = users_seller.user_id
    WHERE products.quantity > 0
    ORDER BY products.date_added DESC
    LIMIT ?,?');

// bindValue will allow us to use an integer in the SQL statement, which we need to use for the LIMIT clause
$stmt->bindValue(1, ($current_page - 1) * $num_products_on_each_page, PDO::PARAM_INT);
$stmt->bindValue(2, $num_products_on_each_page, PDO::PARAM_INT);
$stmt->execute();

// Fetch the products from the database and return the result as an Array
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the total number of products that are available (quantity > 0)
$total_products = $pdo->query('SELECT * FROM products WHERE quantity > 0')->rowCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Products Page</title>
    <link rel="stylesheet" href="cproducts.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
</head>

<body>
<?=template_header2('Products')?>

<div class="products content-wrapper">
    <h1>Products</h1>
    <p><?=$total_products?> Products</p>
    <div class="products-wrapper">
        <?php foreach ($products as $product): ?>
        <a href="index.php?page=sproduct&id=<?=$product['id']?>" class="product">
            <img src="<?=$product['img']?>" width="200" height="200" alt="<?=$product['name']?>">
            <span class="name"><?=$product['name']?></span>
            <span class="price">
                RM <?=$product['price']?>
            </span>
            <span class="seller-name">Seller: <?=$product['user_name']?></span> <!-- Display seller name -->
        </a>
        <?php endforeach; ?>
    </div>

<?=template_footer()?>
</body>
</html>
