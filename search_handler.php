<?php
session_start();
include("connection.php");

$pdo = pdo_connect_mysql();
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search_query) {
    $stmt = $pdo->prepare("
        SELECT products.*, users_seller.user_name
        FROM products
        LEFT JOIN users_seller ON products.seller_id = users_seller.user_id
        WHERE products.name LIKE :search_query AND products.quantity > 0
        ORDER BY products.date_added DESC
        LIMIT 10
    ");
    $stmt->bindValue(':search_query', '%' . $search_query . '%', PDO::PARAM_STR);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($products);
} else {
    echo json_encode([]);
}
?>
