<?php

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "fypp";

if(!$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname))
{
    die("failed to connect");
}

try {
    return new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
} catch (PDOException $exception) {
    exit('Failed to connect to database!');
}
?>