<?php
require __DIR__ . '/vendor/autoload.php';
include 'mail_config.php';
use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = MAIL_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = MAIL_USER;
    $mail->Password = MAIL_PASS;
    $mail->SMTPSecure = 'tls';
    $mail->Port = MAIL_PORT;

    $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
    $mail->addAddress('yourtestemail@domain.com'); // change to an address you can check
    $mail->Subject = 'PHPMailer test';
    $mail->Body = 'This is a test email from Master PC Build local dev.';
    $mail->send();
    echo 'Sent OK';
} catch (Exception $e) {
    echo 'Mailer Error: ' . $mail->ErrorInfo;
}
