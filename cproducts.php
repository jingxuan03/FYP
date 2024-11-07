<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("connection.php");
require_once 'functions.php';

$user_data = check_login3($con);
$pdo = pdo_connect_mysql();

$num_products_on_each_page = 50;
$current_page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// SQL query setup
$sql = '
    SELECT products.*, users_seller.user_name
    FROM products
    LEFT JOIN users_seller ON products.seller_id = users_seller.user_id
    WHERE products.quantity > 0';

// If search query is present, filter by product name
if (!empty($search_query)) {
    $sql .= ' AND products.name LIKE :search_query';
}

$sql .= ' ORDER BY products.date_added DESC LIMIT :offset, :limit';

$stmt = $pdo->prepare($sql);

// Bind search query if it’s present
if (!empty($search_query)) {
    $stmt->bindValue(':search_query', '%' . $search_query . '%', PDO::PARAM_STR);
}

// Bind pagination limits
$stmt->bindValue(':offset', ($current_page - 1) * $num_products_on_each_page, PDO::PARAM_INT);
$stmt->bindValue(':limit', $num_products_on_each_page, PDO::PARAM_INT);

$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total filtered products for pagination
$total_products_sql = 'SELECT COUNT(*) FROM products WHERE quantity > 0';
if (!empty($search_query)) {
    $total_products_sql .= ' AND name LIKE :search_query';
}
$count_stmt = $pdo->prepare($total_products_sql);
if (!empty($search_query)) {
    $count_stmt->bindValue(':search_query', '%' . $search_query . '%', PDO::PARAM_STR);
}
$count_stmt->execute();
$total_products = $count_stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Products Page</title>
    <link rel="stylesheet" href="cproducts.css">
</head>
<body>
<?=template_header('Products')?>

<div class="products content-wrapper">
    <h1>Products</h1>
    <p><?=$total_products?> Products</p>

    <div class="products-wrapper" id="products-list">
        <?php if ($products): ?>
            <?php foreach ($products as $product): ?>
                <a href="index.php?page=sproduct&id=<?=$product['id']?>" class="product">
                    <img src="<?=$product['img']?>" width="200" height="200" alt="<?=$product['name']?>">
                    <span class="name"><?=$product['name']?></span>
                    <span class="price">RM <?=$product['price']?></span>
                    <span class="seller-name">Seller: <?=$product['user_name']?></span>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products found for "<?=htmlspecialchars($search_query)?>"</p>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php
    // Calculate total pages and show pagination if needed
    $total_pages = ceil($total_products / $num_products_on_each_page);
    if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="index.php?page=products&p=<?=$i?>&search=<?=urlencode($search_query)?>" class="page-link"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<?=template_footer()?>
</body>
</html>
