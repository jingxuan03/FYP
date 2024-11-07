<?php
session_start();

include("connection.php");
include("functions.php");

if($_SERVER['REQUEST_METHOD'] == "POST")
{
    $password = $_POST['password'];
    $email = $_POST['email'];

    if(!empty($password) && !empty($email))
    {
        // Generate user_id using random_num()
        $user_id = random_num(20);
        
        // Debugging - Check if user_id is being generated correctly
        echo "Generated User ID: " . $user_id . "<br>";

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Use prepared statements to avoid SQL injection
        $stmt = $con->prepare("INSERT INTO users_admin (user_id, password, email) VALUES (?, ?, ?)");

        // Bind parameters (s for string, i for integer)
        $stmt->bind_param("sss", $user_id, $hashed_password, $email);

        // Execute the statement
        if ($stmt->execute()) {
            header("Location: alogin.php");
            die;
        } else {
            echo "Error in registration!";
        }
    }
    else
    {
        echo "Please enter valid credentials!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Register Page</title>
    <link rel="stylesheet" href="clogin.css">
    <link rel="stylesheet" href="clogin2.css">
</head>

<body>
<?=template_header1('Register')?>

    <div class="wrapper">
        <div class="form-box register">
            <h2>Registration</h2>
            <form method="post" action="">
                <div id="registerMessage" class="messageDiv" style="display: none;"></div>

                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required>
                    <span class="icon"><ion-icon name="mail"></ion-icon></span>
                </div>

                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                </div>

                <div class="remember-forgot">
                    <label><input type="checkbox">I agree to the terms & conditions</label>
                </div>

                <button type="submit" class="btn">Register</button>

                <div class="login-register">
                    <p>Already have an account?<a href="alogin.php" class="login-link"> Login</a></p>
                </div>
            </form>
        </div>
    </div>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>