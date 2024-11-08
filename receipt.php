<?php
// Check if an order has been successfully placed
if (!isset($_GET['order_id'])) {
    // If there's no order ID, redirect to the homepage
    header('Location: index.php?page=shome');
    exit;
}

// Fetch the order ID from the URL
$order_id = $_GET['order_id'];

// Fetch order details from the database
$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch order items
$stmt = $pdo->prepare('SELECT oi.*, p.name, p.img FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?');
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Order Success</title>
    <link rel="stylesheet" href="cproducts.css">
</head>
<body>

<?=template_header('Order Success')?>

<div class="order-success content-wrapper">
    <h1>Thank You for Your Purchase!</h1>
    <div class="order-details">
        <h2>Your Order #<?=$order_id?> has been confirmed.</h2>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price (RM)</th>
                    <th>Total (RM)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                <tr>
                    <td>
                        <img src="<?=$item['img']?>" alt="<?=$item['name']?>" width="50" height="50">
                        <?=$item['name']?>
                    </td>
                    <td><?=$item['quantity']?></td>
                    <td><?=number_format($item['price'], 2)?></td>
                    <td><?=number_format($item['price'] * $item['quantity'], 2)?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="order-total">Total: RM <?=$order['total']?></p>
    </div>
    <a href="index.php?page=chome" class="button">Continue Shopping</a>
</div>

<?=template_footer()?>

</body>
</html>
