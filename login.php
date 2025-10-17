<?php
session_start();
include 'db.php'; // make sure $conn is created here

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.html");
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    echo "❌ Please fill in all fields.";
    exit;
}

// Initialize to avoid "undefined variable" warnings
$admin = null;
$user  = null;

/**
 * 1) Try admins table first
 */
if ($stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ? LIMIT 1")) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $admin = $result->fetch_assoc();
    }
    $stmt->close();
}

if ($admin) {
    $stored = $admin['password'];

    if (preg_match('/^\$2[ayb]\$/', $stored)) {
        if (password_verify($password, $stored)) {
            $_SESSION['id']       = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['role']     = 'admin';
            header("Location: admin_dashboard.php");
            exit;
        } else {
            header("Location: index.html?error=invalid");
        }
    } else {
        if (hash_equals($stored, $password)) {
            $_SESSION['id']       = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['role']     = 'admin';
            header("Location: admin_dashboard.php");
            exit;
        } else {
            header("Location: index.html?error=invalid");
            exit;
        }
    }
}

/**
 * 2) If no admin found, check users table
 */
if ($stmt = $conn->prepare("SELECT id, username, password, role, email_verified FROM users WHERE username = ? LIMIT 1")) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $user = $result->fetch_assoc();
    }
    $stmt->close();
}

if ($user) {
    // ✅ Check if email is verified
    if ((int)$user['email_verified'] !== 1) {
        header("Location: verify_otp.php?email=" . urlencode($user['email']));
        exit;
    }


    if (password_verify($password, $user['password'])) {
        $_SESSION['id']       = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'] ?? 'user';

        if ($_SESSION['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;
    } else {
        header("Location: index.html?error=invalid");
        exit;
    }
}

        header("Location: index.html?error=invalid");
        exit;
?>
