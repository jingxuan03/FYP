<?php
// Check to make sure the id parameter is specified in the URL
if (isset($_GET['id'])) {
    // Prepare statement and execute, prevents SQL injection
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    // Fetch the product from the database and return the result as an Array
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the product exists (array is not empty)
    if (!$product) {
        // Simple error to display if the id for the product doesn't exists (array is empty)
        exit('Product does not exist!');
    }
} else {
    // Simple error to display if the id wasn't specified
    exit('Product does not exist!');
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
        <span class="price">
            RM <?= htmlspecialchars($product['price']) ?>
        </span>
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
