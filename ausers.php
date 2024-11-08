<?php
session_start();

include("connection.php"); // Include your database connection
require_once 'functions.php'; // Include your functions

$pdo = pdo_connect_mysql();

try {
    // Handle deletion request
    if (isset($_GET['delete']) && isset($_GET['type'])) {
        $id = $_GET['delete'];
        $type = $_GET['type']; // Type can be 'seller' or 'customer'

        if ($type == 'seller') {
            // Delete associated products first
            $stmt = $pdo->prepare('DELETE FROM products WHERE seller_id = ?');
            $stmt->execute([$id]);

            // Now delete the seller
            $stmt = $pdo->prepare('DELETE FROM users_seller WHERE user_id = ?');
            $stmt->execute([$id]);
        } else {
            $stmt = $pdo->prepare('DELETE FROM users_cust WHERE user_id = ?');
            $stmt->execute([$id]);
        }

        header('Location: ausers.php');
        exit;
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}



// Fetch sellers and customers
$sellers = $pdo->query('SELECT user_id, user_name, email, added_at FROM users_seller')->fetchAll(PDO::FETCH_ASSOC);
$customers = $pdo->query('SELECT user_id, user_name, email, added_at FROM users_cust')->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Accounts</title>
    <link rel="stylesheet" href="ahome.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
</head>
<body>

<div class="sidebar">
    <div class="logo">Lunar</div>
    <ul class="menu">
        <li class="wlcuser">
            <i class=""></i>
            <span>Welcome Admin</span>
        </li>
        <li>
            <a href="ahome.php">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="active">
            <a href="ausers.php">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
        </li>
        <li>
            <a href="astatistics.php">
                <i class="fas fa-chart-bar"></i>
                <span>Statistics</span>
            </a>
        </li>
        <li class="logout">
            <a href="alogout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</div>

<div class="content">
    <h2>Manage Accounts</h2>

    <h3>Sellers</h3>
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Date Created</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sellers as $seller): ?>
            <tr>
                <td><?= htmlspecialchars($seller['user_id']) ?></td>
                <td><?= htmlspecialchars($seller['user_name']) ?></td>
                <td><?= htmlspecialchars($seller['email']) ?></td>
                <td><?= htmlspecialchars($seller['added_at']) ?></td>
                <td>
                    <a href="?delete=<?= $seller['user_id'] ?>&type=seller" onclick="return confirm('Are you sure you want to delete this seller?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Customers</h3>
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Date Created</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $customer): ?>
            <tr>
                <td><?= htmlspecialchars($customer['user_id']) ?></td>
                <td><?= htmlspecialchars($customer['user_name']) ?></td>
                <td><?= htmlspecialchars($customer['email']) ?></td>
                <td><?= htmlspecialchars($customer['added_at']) ?></td>
                <td>
                    <a href="?delete=<?= $customer['user_id'] ?>&type=customer" onclick="return confirm('Are you sure you want to delete this customer?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
