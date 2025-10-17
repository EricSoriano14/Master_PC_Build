<?php
session_start();
include 'db.php';
require __DIR__ . '/vendor/autoload.php';
include 'mail_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("❌ Please enter a valid email.");
    }

    // check if user exists
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
    $stmt->close();

    if (!$user) {
        die("❌ No account found with that email.");
    }

    // generate token + expiry (1 hour)
    $token   = bin2hex(random_bytes(32));
    $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // update db with reset token + expiry
    $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
    $stmt->bind_param("sss", $token, $expires, $email);
    $stmt->execute();
    $stmt->close();

    // create reset link
    $resetLink = "http://localhost/Master_PC_BUILD/reset_password.php?token=" . $token;

    // send email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USER;
        $mail->Password   = MAIL_PASS;
        $mail->SMTPSecure = 'ssl'; // use 'tls' with port 587 if you want
        $mail->Port       = 465;

        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($email, $user['username']);

        $mail->isHTML(true);
        $mail->Subject = "Reset Your Password - Master PC Build";
        $mail->Body    = "
            <p>Hi <b>{$user['username']}</b>,</p>
            <p>We received a request to reset your password. Click the link below to reset it:</p>
            <p><a href='$resetLink'>$resetLink</a></p>
            <p>This link will expire in 1 hour.</p>
            <p>If you did not request this, you can safely ignore this email.</p>
        ";

        $mail->send();
        echo "✅ A password reset link has been sent to your email.";
    } catch (Exception $e) {
        echo "❌ Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
