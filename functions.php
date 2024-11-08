<?php

function check_login($con)
{
    if(isset($_SESSION['user_id']))
    {
        $id = $_SESSION['user_id'];
        $query = "select * from users_cust where user_id = '$id' limit 1";

        $result = mysqli_query($con, $query);
        if($result && mysqli_num_rows($result) > 0)
        {
            $user_data = mysqli_fetch_assoc($result);
            return $user_data;
        }
    }

    header("Location: clogin.php");
    die;
}

function check_login2($con)
{
    if(isset($_SESSION['user_id']))
    {
        $id = $_SESSION['user_id'];
        $query = "select * from users_admin where user_id = '$id' limit 1";

        $result = mysqli_query($con, $query);
        if($result && mysqli_num_rows($result) > 0)
        {
            $user_data = mysqli_fetch_assoc($result);
            return $user_data;
        }
    }

    header("Location: alogin.php");
    die;
}

function check_login3($con)
{
    if(isset($_SESSION['user_id']))
    {
        $id = $_SESSION['user_id'];
        $query = "select * from users_seller where user_id = '$id' limit 1";

        $result = mysqli_query($con, $query);
        if($result && mysqli_num_rows($result) > 0)
        {
            $user_data = mysqli_fetch_assoc($result);
            return $user_data;
        }
    }

    header("Location: slogin.php");
    die;
}

function random_num($length)
{
    $text = "";
    if($length < 5) {
        $length = 5; 
    }

    $len = rand(4, $length);

    for ($i = 0; $i < $len; $i++) {
        $text .= rand(0, 9);
    }

    return $text;
}

function pdo_connect_mysql() {

    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'fypp';
    try {
    	return new PDO('mysql:host=' . $DATABASE_HOST . ';dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);
    } catch (PDOException $exception) {
    	// If there is an error with the connection, stop the script and display the error.
    	exit('Failed to connect to database!');
    }
}

function template_header($title) {
    // Get the number of items in the shopping cart, which will be displayed in the header.
    $num_items_in_cart = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
    echo <<<EOT
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8">
            <title>$title</title>
            <link href="cproducts.css" rel="stylesheet" type="text/css">
            <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
        </head>
        <body>
            <header>
                <div class="content-wrapper">
                    <h1>Lunar</h1>
                    <nav>
                        <a href="index.php">Home</a>
                        <a href="index.php?page=cproducts">All Products</a>
                        <a href="index.php?page=cpurchasehistory">Purchase History</a>
                        <a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                        </a>
                    </nav>
                    <div class="link-icons">
                        <a href="index.php?page=cart">
                            <i class="fas fa-shopping-cart"></i>
                            <span>$num_items_in_cart</span>
                        </a>
                    </div>
                </div>
            </header>
            <main>
    EOT;
    }

    function template_footer() {
    $year = date('Y');
    echo <<<EOT
            </main>
            <footer>
                <div class="content-wrapper">
                    <p>&copy; $year, Lunar</p>
                </div>
            </footer>
        </body>
    </html>
    EOT;
    }

    function template_header1($title) {
        // Get the number of items in the shopping cart, which will be displayed in the header.
        $num_items_in_cart = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
        echo <<<EOT
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8">
                <title>$title</title>
                <link href="clogin2.css" rel="stylesheet" type="text/css">
                <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
            </head>
            <body>
                <header>
                    <div class="content-wrapper">
                        <h1>Lunar</h1>
                        <nav>
                        <a href="homepage.php">Home</a>
                        <a href="contactus.php">Contact Us</a>
                        <a href="gateway.php">Login</a>
                        </nav>
                    </div>
                </header>
                <main>
        EOT;
        }

        function template_header2($title) {
            // Get the number of items in the shopping cart, which will be displayed in the header.
            $num_items_in_cart = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
            echo <<<EOT
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="utf-8">
                    <title>$title</title>
                    <link href="cproducts.css" rel="stylesheet" type="text/css">
                    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
                </head>
                <body>
                    <header>
                        <div class="content-wrapper">
                            <h1>Lunar</h1>
                            <nav>
                                <a href="index.php?page=shome">Home</a>
                                <a href="index.php?page=sproducts">Marketplace</a>
                                <a href="index.php?page=sinventory">Inventory</a>
                                <a href="index.php?page=sgeneratedsales">Sales</a>
                                <a href="slogout.php">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                                </a>
                            </nav>
                        </div>
                    </header>
                    <main>
            EOT;
            }

    ?>