<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Error: You must be logged in to create a product.";
    exit;
}

// Fetch the user_id from the session
$user_id = $_SESSION['user_id']; 

// Establish database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=fypp', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Fetch the seller_id from the users_seller table using the user_id
$stmt = $pdo->prepare("SELECT user_id FROM users_seller WHERE user_id = ?");
$stmt->execute([$user_id]);
$seller = $stmt->fetch();

$seller_id = $seller['user_id'];

$name = "";
$description = "";
$price = "";
$quantity = "";
$img = "";
$errorMessage = "";
$successMessage = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle the uploaded image
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = __DIR__ . '/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true); // Create the directory if it doesn't exist
        }

        $image_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $upload_file = $upload_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file)) {
            $img = 'uploads/' . $image_name;
            $successMessage = "Image uploaded successfully!";
        } else {
            $errorMessage = "Error uploading the image.";
        }
    }

    // Get other form values
    $name = $_POST["name"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $quantity = $_POST["quantity"];

    // Validate form inputs
    if (empty($name) || empty($description) || empty($price) || empty($quantity) || empty($img)) {
        $errorMessage = "All fields are required";
    } else {
        // Insert the product into the database
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, `desc`, price, quantity, img, seller_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $quantity, $img, $seller_id]);

            // Reset form fields after successful insertion
            $name = "";
            $description = "";
            $price = "";
            $quantity = "";
            $img = "";

            $successMessage = "Product added successfully";
            header("Location: sinventory.php");
            exit();
        } catch (PDOException $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Seller Create Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h2>New Product</h2>

        <?php if (!empty($errorMessage)): ?>
            <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <strong><?php echo $errorMessage; ?></strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Name</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="name" value="<?php echo $name; ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Description</label>
                <div class="col-sm-6">
                    <textarea class="form-control" name="description" rows="10" required><p><?php echo nl2br(htmlspecialchars($description)); ?></p></textarea>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Price</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="price" value="<?php echo $price; ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Quantity</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="quantity" value="<?php echo $quantity; ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Image</label>
                <div class="col-sm-6">
                    <input type="file" class="form-control" name="image" accept="image/*" required>
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
