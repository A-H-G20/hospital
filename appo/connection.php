<?php
// connection.php

$host = "localhost";
$user = "root";
$password = "";
$database = "edoc";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
