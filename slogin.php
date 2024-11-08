<?php
session_start();

include("connection.php");
include("functions.php");

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user_name = $_POST['user_name'];
    $password = $_POST['password'];

    if (!empty($user_name) && !empty($password) && !is_numeric($user_name)) {
        $query = "SELECT * FROM users_seller where user_name = '$user_name' LIMIT 1";
        $result = mysqli_query($con, $query);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $user_data = mysqli_fetch_assoc($result);
                
                // Use password_verify to check the hashed password
                if (password_verify($password, $user_data['password'])) {
                    $_SESSION['user_id'] = $user_data['user_id'];
                    header("Location: shome.php");
                    die;
                } else {
                    $error_message = "Wrong username or password!";
                }
            } else {
                $error_message = "Wrong username or password!";
            }
        } else {
            $error_message = "Database query failed!";
        }
    } else {
        $error_message = "Please enter valid credentials!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Seller Login Page</title>
    <link rel="stylesheet" href="clogin.css">
    <link rel="stylesheet" href="cproducts.css">
</head>

<body>
<?=template_header1('Seller Login')?>

    <div class="wrapper">
        <div class="form-box login">
        <button type="button" onclick="window.location.href='gateway.php';" class="btn">Back to Gateway</button>
            <h2>Seller Login</h2>
            <form action="" method="post">
                <div id="signInMessage" class="messageDiv" style="display:none;"></div>
                <div class="input-box">
                    <input type="text" name="user_name" placeholder="Username" required>
                    <span class="icon"><ion-icon name="person"></ion-icon></span>
                </div>

                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                </div>

                <div class="remember-forgot">
                    <a href="#">Forgot Password?</a>
                </div>

                <button type="submit" value="Login" class="btn">Login</button>

                <div class="login-register">
                    <p>Don't have an account?<a href="sregister.php" class="register-link"> Register Here</a></p>
                </div>

                <?php if (!empty($error_message)): ?>
                    <div class="error-message">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
