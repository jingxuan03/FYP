<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

    include("connection.php");
    require_once 'functions.php';

    $user_data = check_login2($con);

   // Query to get the total sales volume
    $query_sales = "SELECT SUM(total) as total_sales_volume FROM orders";
    $result_sales = mysqli_query($con, $query_sales);
    $total_sales_volume = 0;
    if ($result_sales) {
        $row_sales = mysqli_fetch_assoc($result_sales);
        $total_sales_volume = $row_sales['total_sales_volume'] ? $row_sales['total_sales_volume'] : 0;
    }

    // Query to get the total number of transactions
    $query_transactions = "SELECT COUNT(id) as total_transactions FROM orders";
    $result_transactions = mysqli_query($con, $query_transactions);
    $total_transactions = 0;
    if ($result_transactions) {
        $row_transactions = mysqli_fetch_assoc($result_transactions);
        $total_transactions = $row_transactions['total_transactions'] ? $row_transactions['total_transactions'] : 0;
    }

    $query_active_user = "SELECT COUNT(id) as total_active_user FROM users_cust";
    $result_transactions = mysqli_query($con, $query_active_user);
    $total_active_user= 0;
    if ($result_transactions) {
        $row_active_user = mysqli_fetch_assoc($result_transactions);
        $total_active_user = $row_active_user['total_active_user'] ? $row_active_user['total_active_user'] : 0;
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Home Page</title>
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
            <li class="active">
                <a href="ahome.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
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

    <div class="main--content">
        <div class="header-wrapper">
            <div class="header--title">
                <h2>Dashboard</h2>
                    <div class="search--box">
                        <i class="fasolidfa-search"></i><input type="text" placeholder="Search" />
                    </div>
            </div>
                    <div class="card--container">
                        <h3 class="main-title">Analytics</h3>
                        <br>
                        <div class="card--wrapper">
                            <div class="payment--card light-blue">
                                <div class="card--header">
                                    <div class="amount">
                                        <span class="title">
                                            Total Revenue</span>
                                            <span class="amount-value">
                                                RM<?php echo number_format($total_sales_volume, 2); ?>
                                            </span>
                                    </div>
                                </div>
                            </div>

                            <div class="payment--card light-blue">
                                <div class="card--header">
                                    <div class="amount">
                                        <span class="title">
                                            Total Number of Transactions</span>
                                            <span class="amount-value">
                                                <?php echo number_format($total_transactions); ?>
                                            </span>
                                    </div>
                                </div>
                            </div>

                            <div class="payment--card light-blue">
                                <div class="card--header">
                                    <div class="amount">
                                        <span class="title">
                                            Total Active User</span>
                                            <span class="amount-value">
                                            <?php echo number_format($total_active_user); ?>        
                                        </span>
                                    </div>
                                </div>
                            </div>
        </div>
    </div>

</body>
</html>