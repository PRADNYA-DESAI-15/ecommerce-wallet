<?php
$host = "localhost";
$username = "root";
$password = "pradnya1512";
$database = "ecommerce";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
