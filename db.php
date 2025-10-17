<?php
$host = "localhost";
$user = "root"; // default XAMPP username
$pass = ""; // default XAMPP password is empty
$dbname = "pc_build_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
