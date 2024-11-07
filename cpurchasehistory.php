<?php

require_once 'functions.php';

// Assuming user is logged in and their user ID is stored in session
$user_id = $_SESSION['user_id'];

// Fetch purchase history for the logged-in user
$stmt = $pdo->prepare('
    SELECT 
        o.id AS order_id, o.order_date, o.total, oi.quantity, oi.price, p.name, p.img 
    FROM 
        orders o 
    JOIN 
        order_items oi ON o.id = oi.order_id 
    JOIN 
        products p ON oi.product_id = p.id 
    WHERE 
        o.user_id = ? 
    ORDER BY 
        o.order_date DESC, o.id ASC
');
$stmt->execute([$user_id]);
$purchase_history = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group products by order_id
$grouped_orders = [];
foreach ($purchase_history as $order) {
    $order_id = $order['order_id'];
    if (!isset($grouped_orders[$order_id])) {
        $grouped_orders[$order_id] = [
            'order_date' => $order['order_date'],
            'total' => $order['total'],
            'products' => []
        ];
    }
    $grouped_orders[$order_id]['products'][] = $order;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Purchase History</title>
    <link rel="stylesheet" href="cpurchasehistory.css">
</head>
<body>
<?=template_header('Products')?>

<h1>Purchase History</h1>

<table>
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Date</th>
            <th>Products</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($grouped_orders)): ?>
            <tr>
                <td colspan="4">You have no purchase history.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($grouped_orders as $order_id => $order_data): ?>
                <tr>
                    <td data-label="Order ID"><?= htmlspecialchars($order_id) ?></td>
                    <td data-label="Date"><?= htmlspecialchars($order_data['order_date']) ?></td>
                    <td data-label="Products">
                        <?php foreach ($order_data['products'] as $product): ?>
                            <div style="margin-bottom: 10px;">
                                <img src="<?= htmlspecialchars($product['img']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" width="50" height="50">
                                <?= htmlspecialchars($product['name']) ?> (x<?= htmlspecialchars($product['quantity']) ?>) - RM <?= htmlspecialchars($product['price']) ?>
                            </div>
                        <?php endforeach; ?>
                    </td>
                    <td data-label="Total" class="total">RM <?= htmlspecialchars($order_data['total']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?=template_footer()?>
</body>
</html>
