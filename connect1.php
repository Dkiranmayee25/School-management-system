<?php
$servername = "localhost";
$username   = "root";
$password   = ""; 
$database   = "school";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Do NOT put any SELECT queries or while loops here
?>
