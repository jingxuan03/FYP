<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to view this page.";
    exit;
}

$stmt = $pdo->prepare('
    SELECT SUM(order_items.quantity * order_items.price) AS total_sales
    FROM order_items
    JOIN orders ON order_items.order_id = orders.id
    WHERE orders.user_id = :user_id
');
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$total_sales = $stmt->fetch(PDO::FETCH_ASSOC)['total_sales'];

// Commission calculation (e.g., 10%)
$commission_rate = 0.1;
$earnings = $total_sales - ($total_sales * $commission_rate);

// Query for order details
$stmt = $pdo->prepare('
    SELECT orders.id AS order_id, products.name, order_items.quantity, order_items.price, (order_items.quantity * order_items.price) AS total_price
    FROM order_items
    JOIN orders ON order_items.order_id = orders.id
    JOIN products ON order_items.product_id = products.id
    JOIN users_seller ON products.seller_id = users_seller.user_id
    WHERE users_seller.user_id = :user_id
');
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Purchase History</title>
    <link rel="stylesheet" href="cpurchasehistory.css">
</head>
<body>
<?=template_header2('Products')?>

<div class="sales-dashboard">
    <h1>Sales Dashboard</h1>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order_items as $item): ?>
            <tr>
                <td><?=$item['order_id']?></td>
                <td><?=$item['name']?></td>
                <td><?=$item['quantity']?></td>
                <td>RM <?=$item['price']?></td>
                <td>RM <?=$item['total_price']?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?=template_footer()?>
</body>
</html>