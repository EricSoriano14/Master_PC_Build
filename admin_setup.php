<?php
include 'db.php';

$username = "admin";
$email = "admin@example.com";
$password = "admin123"; 
$role = "admin";

$check = $conn->prepare("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "⚠️ An admin account already exists. No new admin was created.";
    exit;
}

$hashed_password = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

if ($stmt->execute()) {
    echo "✅ Admin account created successfully.<br>";
    echo "👉 Username: <b>$username</b><br>";
    echo "👉 Password: <b>$password</b><br>";
} else {
    echo "❌ Error creating admin: " . $stmt->error;
}
?>

