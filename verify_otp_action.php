<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email     = trim($_POST['email'] ?? '');
    $otpArray  = $_POST['otp'] ?? [];
    $otp       = is_array($otpArray) ? implode('', $otpArray) : trim($otpArray);

    if (!$email || !$otp) {
        header("Location: verify_otp.php?email=" . urlencode($email) . "&error=Missing+OTP");
        exit;
    }

    $stmt = $conn->prepare("SELECT id, otp_code, otp_expires_at, email_verified 
                            FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user   = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        header("Location: verify_otp.php?error=No+account+found");
        exit;
    }

    if ($user['email_verified']) {
        header("Location: verify_otp.php?email=" . urlencode($email) . "&success=already");
        exit;
    }

    if ($user['otp_code'] !== $otp) {
        header("Location: verify_otp.php?email=" . urlencode($email) . "&error=Invalid+OTP");
        exit;
    }

    if (strtotime($user['otp_expires_at']) < time()) {
        header("Location: verify_otp.php?email=" . urlencode($email) . "&error=OTP+expired");
        exit;
    }

    $upd = $conn->prepare("UPDATE users SET email_verified = 1, otp_code = NULL, otp_expires_at = NULL WHERE id = ?");
    $upd->bind_param("i", $user['id']);
    if ($upd->execute()) {
    // After verification, redirect directly to login
    header("Location: login.php?verified=1");
    exit;
    } else {
        header("Location: verify_otp.php?email=" . urlencode($email) . "&error=Update+failed");
        exit;
    }
}
?>
