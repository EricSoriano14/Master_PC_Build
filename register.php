<?php
session_start();
include 'db.php';               // must define $conn (mysqli)
require __DIR__ . '/vendor/autoload.php';   
include 'mail_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation
    if (!$username || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        die('Please provide valid username, email and a password (min 6 chars).');
    }

    // Check duplicate email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        die('Email already registered. Try logging in or use resend OTP.');
    }
    $stmt->close();

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Generate OTP & expiry (10 minutes)
    $otp = random_int(100000, 999999);
    $expires_at = date("Y-m-d H:i:s", strtotime("+10 minutes"));

    // Insert user (other columns use defaults)
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, email_verified, otp_code, otp_expires_at) VALUES (?, ?, ?, 0, ?, ?)");
    $otp_str = (string)$otp;  // convert to string first
    $stmt->bind_param("sssss", $username, $email, $hashed, $otp_str, $expires_at);


    if ($stmt->execute()) {
        $inserted_id = $conn->insert_id;
        // send OTP email
        $mail = new PHPMailer(true);
        try {
            // SMTP config
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USER;
            $mail->Password   = MAIL_PASS;
            $mail->SMTPSecure = 'ssl'; 
            $mail->Port       = 465;

            // Local dev certificate issues: ok for local testing (don't use in production)
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ];

            $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
            $mail->addAddress($email, $username);

            $mail->isHTML(true);
            $mail->Subject = 'Your Master PC Build verification code';
            $mail->Body    = "Hi {$username},<br><br>Your verification code is: <b>{$otp}</b><br>It expires in 10 minutes.<br><br>If you didn't register, ignore this email.";

            $mail->send();

            // Redirect to the verify form (prefill email)
            header('Location: verify_otp.php?email=' . urlencode($email));
            exit;
        } catch (Exception $e) {
            // If mail fails, remove the just-inserted user to avoid half-baked accounts
            $del = $conn->prepare("DELETE FROM users WHERE id = ?");
            $del->bind_param("i", $inserted_id);
            $del->execute();
            $del->close();

            echo "❌ Email could not be sent. Mailer Error: " . $mail->ErrorInfo;
            exit;
        }
    } else {
        echo "❌ Registration failed: " . $conn->error;
    }
}
?>
