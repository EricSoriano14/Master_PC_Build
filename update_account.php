<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Get form values
$first_name  = $_POST['first_name'];
$middle_name = $_POST['middle_name'];
$last_name   = $_POST['last_name'];
$barangay    = $_POST['barangay'];
$street      = $_POST['street'];
$postal_code = $_POST['postal_code'];
$municipality = $_POST['municipality'];
$province    = $_POST['province'];
$mobile      = $_POST['mobile'];

// Update user account details
$stmt = $conn->prepare("UPDATE users SET 
    first_name = ?, 
    middle_name = ?, 
    last_name = ?, 
    barangay = ?, 
    street = ?, 
    postal_code = ?, 
    municipality = ?, 
    province = ?, 
    mobile = ?
    WHERE username = ?");
$stmt->bind_param("ssssssssss", $first_name, $middle_name, $last_name, $barangay, $street, $postal_code, $municipality, $province, $mobile, $username);

if ($stmt->execute()) {
    header("Location: dashboard.php?updated=1");
    exit;
} else {
    echo "Error updating account: " . $conn->error;
}
?>
