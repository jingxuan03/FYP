<?php

require_once 'functions.php';

$servername = "localhost"; // Replace with your server name
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password
$dbname = "fypp"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products for the selected type
$products = [];
if (isset($_GET['productTypeId'])) {
    $productTypeId = $_GET['productTypeId'];
    $sql = "SELECT id, product_name FROM product_specs WHERE product_type_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productTypeId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
}

// Handle product comparison
$productSpecs = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product1']) && isset($_POST['product2'])) {
        $selectedProductIds = [$_POST['product1'], $_POST['product2']];
        
        foreach ($selectedProductIds as $productId) {
            $sql = "SELECT product_name, display, rear_camera, processor, battery, price FROM product_specs WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                // Extract just the percentage for the display
                if (preg_match('/(\d+)%/', $row['display'], $matches)) {
                    $row['display'] = intval($matches[1]); // Use the first captured group (the number)
                }

                // Repeat for rear_camera, processor, battery as needed
                if (preg_match('/(\d+)%/', $row['rear_camera'], $matches)) {
                    $row['rear_camera'] = intval($matches[1]);
                }
                if (preg_match('/(\d+)%/', $row['processor'], $matches)) {
                    $row['processor'] = intval($matches[1]);
                }
                if (preg_match('/(\d+)%/', $row['battery'], $matches)) {
                    $row['battery'] = intval($matches[1]);
                }

                $productSpecs[] = $row;
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Comparison</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="comparison.css"> <!-- Optional: Your CSS file -->
</head>
<body>
<?=template_header('Comparison')?>
    <h2><i class="fas fa-chart-bar"></i> Comparison</h2>
    <form id="comparisonForm" method="POST" action="comparison.php?productTypeId=<?= isset($productTypeId) ? $productTypeId : '' ?>">
        <label for="productType">Select Product Type:</label>
        <select id="productType" name="productType" onchange="window.location.href='comparison.php?productTypeId=' + this.value;">
            <option value="" selected>Select the Product Type</option>
            <option value="1" <?= isset($productTypeId) && $productTypeId == 1 ? 'selected' : '' ?>>Smartphone</option>
            <option value="2" <?= isset($productTypeId) && $productTypeId == 2 ? 'selected' : '' ?>>Tablet</option>
            <option value="3" <?= isset($productTypeId) && $productTypeId == 3 ? 'selected' : '' ?>>Laptop</option>
            <option value="4" <?= isset($productTypeId) && $productTypeId == 4 ? 'selected' : '' ?>>SmartWatch</option>
            <option value="5" <?= isset($productTypeId) && $productTypeId == 5 ? 'selected' : '' ?>>Headphone</option>
            <!-- Add more product types as needed -->
        </select>

        <label for="product1">Select Product 1:</label>
        <select id="product1" name="product1">
            <option value="" selected>Select Product 1</option>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['product_name']) ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>

        <label for="product2">Select Product 2:</label>
        <select id="product2" name="product2">
            <option value="" selected>Select Product 2</option>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['product_name']) ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>

        <button type="submit">Compare</button>
        <button type="reset" class="reset-button" onclick="location.href='comparison.php?productTypeId=<?= isset($productTypeId) ? $productTypeId : '' ?>';">Reset</button>
    </form>

    <?php if (!empty($productSpecs)): ?>
        <h2><i class="fas fa-chart-bar"></i> Results</h2>
        <table class="comparison-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <?php foreach ($productSpecs as $product): ?>
                        <th><?= htmlspecialchars($product['product_name']) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td class="category-name">Display</td>
                <?php foreach ($productSpecs as $product): ?>
                    <td>
                        <div class="bar-container">
                            <div class="bar" style="width: <?= $product['display'] ?>%;"></div>
                            <div class="percentage-text"><?= $product['display'] ?>%</div>
                        </div>
                    </td>
                <?php endforeach; ?>
            </tr>
                <tr>
                    <td class="category-name">Rear Camera</td>
                    <?php foreach ($productSpecs as $product): ?>
                        <td>
                            <div class="bar-container">
                                <div class="bar" style="width: <?= $product['rear_camera'] ?>%;"></div>
                                <div class="percentage-text"><?= $product['rear_camera'] ?>%</div>
                            </div>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <td class="category-name">Processor</td>
                    <?php foreach ($productSpecs as $product): ?>
                        <td>
                            <div class="bar-container">
                                <div class="bar" style="width: <?= $product['processor'] ?>%;"></div>
                                <div class="percentage-text"><?= $product['processor'] ?>%</div>
                            </div>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <td class="category-name">Battery</td>
                    <?php foreach ($productSpecs as $product): ?>
                        <td>
                            <div class="bar-container">
                                <div class="bar" style="width: <?= $product['battery'] ?>%;"></div>
                                <div class="percentage-text"><?= $product['battery'] ?>%</div>
                            </div>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <td class="category-name">Price</td>
                    <?php foreach ($productSpecs as $product): ?>
                        <td>
                            RM <?= $product['price'] ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>
    <?=template_footer()?>
</body>
</html>