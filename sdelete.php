<?php if (isset($_GET["id"]) ) {
    $id = $_GET["id"];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "fypp";

    $connection = new mysqli($servername, $username, $password, $database);

    $sql = "DELETE FROM products where id=$id";
    $connection->query($sql);
}

header("location: sinventory.php");
exit;
?>