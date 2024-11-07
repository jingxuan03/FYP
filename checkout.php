<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ob_start();

// Check if there are products in the cart
$products_in_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$products = array();
$subtotal = 0.00;

// If there are products in cart, fetch them from the database
if ($products_in_cart) {
    $array_to_question_marks = implode(',', array_fill(0, count($products_in_cart), '?'));
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id IN (' . $array_to_question_marks . ')');
    $stmt->execute(array_keys($products_in_cart));
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $product) {
        $subtotal += (float)$product['price'] * (int)$products_in_cart[$product['id']];
    }
}

// Handle order placement
if (isset($_POST['placeorder'])) {
    error_log('Place order button clicked.');
    // Get the selected payment method
    if (isset($_POST['payment_method'])) {
        $payment_method = $_POST['payment_method'];
        error_log('Payment Method: ' . $payment_method);

        // Start transaction to ensure atomicity
        $pdo->beginTransaction();

        try {
            error_log('Subtotal: ' . $subtotal . ', Payment Method: ' . $payment_method);
            // Insert the order into the 'orders' table
            $stmt = $pdo->prepare('INSERT INTO orders (user_id, payment_method, total, order_date) VALUES (?, ?, ?, NOW())');
            $stmt->execute([$_SESSION['user_id'], $payment_method, $subtotal]);

            // Get the last inserted order ID
            $order_id = $pdo->lastInsertId();
            error_log('Order ID: ' . $order_id);

            // Insert each product into the 'order_items' table and update the product stock
            foreach ($products as $product) {
                $quantity = (int)$products_in_cart[$product['id']];
                $price = (float)$product['price'];

                error_log('Inserting into order_items: Order ID: ' . $order_id . ', Product ID: ' . $product['id'] . ', Quantity: ' . $quantity . ', Price: ' . $price);

                // Insert into 'order_items' table
                $stmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
                $stmt->execute([$order_id, $product['id'], $quantity, $price]);

                error_log('Updating quantity for Product ID: ' . $product['id'] . ' - Deducting: ' . $quantity);

                // Update product quantity in the 'products' table
                $stmt = $pdo->prepare('UPDATE products SET quantity = quantity - ? WHERE id = ?');
                if ($stmt->execute([$quantity, $product['id']])) {
                    error_log('Quantity updated for Product ID: ' . $product['id']);
                } else {
                    error_log('Failed to update quantity for Product ID: ' . $product['id']);
                }
            }

            // Commit the transaction
            $pdo->commit();

            // Clear the cart after placing the order
            $_SESSION['cart'] = [];
            error_log('Cart cleared successfully.');

            // Redirect to order success page
            header('Location: index.php?page=receipt&order_id=' . $order_id);
            exit;
        } catch (Exception $e) {
            // Rollback the transaction on error
            $pdo->rollBack();
            
            // Log the error for debugging
            error_log('Order placement failed: ' . $e->getMessage());

            // Display error to the user (optional)
            echo '<p>Error: Unable to place your order. Please try again later.</p>';
        }
    } else {
        error_log('No payment method selected.');
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Checkout</title>
    <link rel="stylesheet" href="checkout.css">
</head>

<body>
<?=template_header('Checkout')?>

<div class="checkout content-wrapper">
    <div class="button">
    <a href="index.php?page=cart" class="button">< Back</a>
    </div>
    <h1>Checkout</h1>
    <div class="order-summary">
        <table>
            <thead>
                <tr>
                    <td class="title" colspan="5">Order Summary</td>
                </tr>
                <tr>
                    <td colspan="2">Product</td>
                    <td>Price</td>
                    <td>Quantity</td>
                    <td>Total</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                <tr>
                    <td colspan="5" style="text-align:center;">You have no products in your cart</td>
                </tr>
                <?php else: ?>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td class="img">
                        <a href="index.php?page=product&id=<?=$product['id']?>">
                            <img src="<?=$product['img']?>" width="50" height="50" alt="<?=$product['name']?>">
                        </a>
                    </td>
                    <td><?=$product['name']?></td>
                    <td class="price">RM <?=$product['price']?></td>
                    <td><?=$products_in_cart[$product['id']]?></td>
                    <td class="price">RM <?=$product['price'] * $products_in_cart[$product['id']]?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="subtotal-row">
                    <td colspan="4" class="text-right">Subtotal</td>
                    <td class="price"><strong>RM <?=$subtotal?></strong></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="payment-methods">
    <h2>Payment Methods</h2>
    <form action="" method="post">
        <div class="payment-option">
            <input type="radio" id="cash" name="payment_method" value="Cash" required>
            <label for="cash">Cash</label>
        </div>
        <div class="payment-option">
            <input type="radio" id="credit_card" name="payment_method" value="Credit Card">
            <label for="credit_card">Credit Card</label>
        </div>
        <div id="credit-card-info" style="display: none;">
            <h3>Enter Credit Card Information</h3>
            <label for="card_number">Card Number:</label>
            <input type="text" id="card_number" name="card_number" pattern="\d{16}" title="Please enter a valid 16-digit card number">
            
            <label for="card_name">Name on Card:</label>
            <input type="text" id="card_name" name="card_name">
            
            <label for="expiry_date">Expiry Date:</label>
            <input type="month" id="expiry_date" name="expiry_date">
            
            <label for="cvv">CVV:</label>
            <input type="text" id="cvv" name="cvv" pattern="\d{3}" title="Please enter a valid 3-digit CVV">
        </div>
        <div class="payment-option">
            <input type="radio" id="e_wallet" name="payment_method" value="E-Wallet">
            <label for="e_wallet">E-Wallet</label>
        </div>
        <div class="buttons">
            <input type="submit" value="Place Order" name="placeorder">
        </div>
    </form>
</div>

<script>
    document.querySelectorAll('input[name="payment_method"]').forEach((elem) => {
        elem.addEventListener('change', function(event) {
            const creditCardInfo = document.getElementById('credit-card-info');
            if (event.target.value === 'Credit Card') {
                creditCardInfo.style.display = 'block'; // Show credit card fields
            } else {
                creditCardInfo.style.display = 'none'; // Hide credit card fields
                // Clear values when not required
                creditCardInfo.querySelectorAll('input').forEach(input => {
                    input.value = '';
                });
            }
        });
    });

    document.querySelector('form').addEventListener('submit', function(event) {
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const creditCardInfo = document.getElementById('credit-card-info');

        // Only validate credit card fields if Credit Card is selected
        if (paymentMethod === 'Credit Card') {
            const requiredInputs = creditCardInfo.querySelectorAll('input[required]');
            let allValid = true;

            requiredInputs.forEach(input => {
                if (!input.value || !input.checkValidity()) {
                    input.setCustomValidity('This field is required.');
                    input.reportValidity();
                    allValid = false; // If any input is invalid
                } else {
                    input.setCustomValidity(''); // Clear any custom error
                }
            });

            if (!allValid) {
                event.preventDefault(); // Prevent form submission if invalid
            }
        }
    });
</script>

</body>
</html>
