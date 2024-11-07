<?php
session_start();

include("connection.php");
include("functions.php");

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        $stmt = $con->prepare("SELECT * FROM users_admin WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $user_data = mysqli_fetch_assoc($result);
                
                // Verifying the password hash
                if (password_verify($password, $user_data['password'])) {
                    // Correct login, redirect to home page
                    $_SESSION['user_id'] = $user_data['user_id'];
                    header("Location: ahome.php");
                    exit(); // Always exit after header to stop further execution
                } else {
                    // Wrong password
                    $error_message = "Wrong username or password!";
                }
            } else {
                // No user found with the given email
                $error_message = "Wrong username or password!";
            }
        }
    } else {
        $error_message = "Please enter valid credentials!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Login Page</title>
    <link rel="stylesheet" href="clogin.css">
    <link rel="stylesheet" href="cproducts.css">
</head>

<body>
<?=template_header1('Login')?>

    <div class="wrapper">
        <div class="form-box login">
        <button type="button" onclick="window.location.href='gateway.php';" class="btn">Back to Gateway</button>
            <h2>Admin Login</h2>
            <form action="" method="post">
                <div id="signInMessage" class="messageDiv" style="display:none;"></div>
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required>
                    <span class="icon"><ion-icon name="person"></ion-icon></span>
                </div>

                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                </div>

                <div class="remember-forgot">
                    <label><input type="checkbox">Remember Me</label>
                    <a href="#">Forgot Password?</a>
                </div>

                <button type="submit" value="Login" class="btn">Login</button>

                <div class="login-register">
                    <p>Don't have an account?<a href="aregister.php" class="register-link"> Register Here</a></p>
                </div>

                <?php if (!empty($error_message)): ?>
                    <div class="error-message">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <script src="script.js"></script>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
