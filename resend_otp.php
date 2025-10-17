<?php
session_start();
include 'db.php';               
require __DIR__ . '/vendor/autoload.php';
include 'mail_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('❌ Please enter a valid email.');
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        die('❌ No account found with that email.');
    }

    // Generate new OTP & expiry
    $otp = random_int(100000, 999999);
    $expires_at = date("Y-m-d H:i:s", strtotime("+10 minutes"));

    $otp_str = (string)$otp;      // convert OTP to string
    $user_id = (int)$user['id'];  // user ID in a real variable

    // Update OTP in DB
    $upd = $conn->prepare("UPDATE users SET otp_code = ?, otp_expires_at = ? WHERE id = ?");
    $upd->bind_param("ssi", $otp_str, $expires_at, $user_id);

    if ($upd->execute()) {
        $upd->close();

        // Send OTP email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USER;
            $mail->Password   = MAIL_PASS;
            $mail->SMTPSecure = 'ssl'; 
            $mail->Port       = 465;

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ];

            $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
            $mail->addAddress($email, $user['username']);

            $mail->isHTML(true);
            $mail->Subject = 'Your new OTP code';
            $mail->Body    = "Hi {$user['username']},<br><br>Your new verification code is: <b>{$otp}</b><br>It expires in 10 minutes.<br><br>If you didn’t request this, you can ignore this email.";

            $mail->send();

            echo "✅ A new OTP has been sent to your email.";
        } catch (Exception $e) {
            echo "❌ Email could not be sent. Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        echo "❌ Failed to update OTP. Please try again.";
    }
}
?>
