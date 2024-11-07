<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "fypp";

$connection = new mysqli($servername, $username, $password, $database);

$id = "";
$name = "";
$description = "";
$price = "";
$quantity = "";
$img = "";
$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET["id"])) {
        header("location: sinventory.php");
        exit;
    }

    $id = $_GET["id"];
    $sql = "SELECT * FROM products WHERE id=$id";
    $result = $connection->query($sql);
    $row = $result->fetch_assoc();

    if (!$row) {
        header("location: sinventory.php");
        exit;
    }

    // Populate form fields with existing values
    $name = $row["name"];
    $description = $row["desc"];
    $price = $row["price"];
    $quantity = $row["quantity"];
    $img = $row["img"]; // Use the existing image path

} else { // POST request handling
    $id = $_POST["id"] ?? ''; // Use null coalescing operator to avoid undefined index
    $name = $_POST["name"] ?? '';
    $description = $_POST["description"] ?? ''; // Ensure this matches the HTML input name
    $price = $_POST["price"] ?? '';
    $quantity = $_POST["quantity"] ?? '';

    // Retrieve the old image path from the hidden input
    $img = $_POST["existing_image"] ?? '';

    // Handle image upload if a new image is provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = __DIR__ . '/uploads/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $image_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $upload_file = $upload_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file)) {
            $img = 'uploads/' . $image_name; // Store the relative path of the new image
        } else {
            $errorMessage = "Error uploading the image.";
        }
    }

        // Validate form inputs
    if (empty($id) || empty($name) || empty($description) || empty($price) || !isset($quantity) || empty($img)) {
        $errorMessage = "All fields are required.";
    } else {
        $sql = "UPDATE products SET name = '$name', `desc` = '$description', price = '$price', quantity = '$quantity', img = '$img' WHERE id = $id";

        $result = $connection->query($sql);

        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
        } else {
            $successMessage = "Product updated successfully";
            header("location: sinventory.php");
            exit;
        }
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h2>Edit Product</h2>

        <?php if (!empty($errorMessage)): ?>
            <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <strong><?php echo $errorMessage; ?></strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($img); ?>"> 
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Name</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Description</label>
                <div class="col-sm-6">
                    <textarea class="form-control" name="description" rows="5" required><p><?php echo nl2br(htmlspecialchars($description)); ?></p></textarea>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Price</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="price" value="<?php echo htmlspecialchars($price); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Quantity</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="quantity" value="<?php echo htmlspecialchars($quantity); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Image</label>
                <div class="col-sm-6">
                    <input type="file" class="form-control" name="image" accept="image/*">
                    <small class="form-text text-muted">Current image: <?php echo $img; ?></small>
                </div>
            </div>
            <?php if (!empty($successMessage)): ?>
                <div class='row mb-3'>
                    <div class='offset-sm-3 col-sm-6'>
                        <div class='alert alert-success alert-dismissible fade show' role='alert'>
                            <strong><?php echo $successMessage; ?></strong>
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                        </div>
                    </div>    
                </div>
            <?php endif; ?>
            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                <div class="col-sm-3 d-grid">
                    <a class="btn btn-outline-primary" href="sinventory.php" role="button">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
