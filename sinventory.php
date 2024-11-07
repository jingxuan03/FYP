<?php
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Seller Inventory</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<?=template_header2('Products')?>
    <div class="container my-5">
        <h2>List of Products</h2>
        <a class="btn btn-primary" href="screate.php" role="button">New Product</a>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Image</th>
                    <th>Date Added</th>
                </tr>
            </thead>
            <tbody>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "fypp";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to view your inventory.";
    exit;
}

// Fetch the user_id (which will be used as seller_id) from the session
$seller_id = $_SESSION['user_id'];

$connection = new mysqli($servername, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Modify the SQL query to fetch products only for the logged-in seller
$sql = "SELECT * FROM products WHERE seller_id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $seller_id);  // Use 'i' for integer binding
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Invalid query: " . $connection->error);
}

while ($row = $result->fetch_assoc()) {
    echo "
        <tr>
            <td>{$row['id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['desc']}</td>
            <td>{$row['price']}</td>
            <td>{$row['quantity']}</td>
            <td><img src='{$row['img']}' width='100' height='100' alt='{$row['name']}'></td>
            <td>{$row['date_added']}</td>
            <td>
                <a class='btn btn-primary btn-sm' href='sedit.php?id={$row['id']}'>Edit</a>
                <a class='btn btn-danger btn-sm' href='sdelete.php?id={$row['id']}'>Delete</a>
            </td>
        </tr>";
}

$stmt->close();
$connection->close();
?>

            </tbody>
        </table>
    </div>
    <?=template_footer()?>
</body>
</html>
