<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token    = trim($_POST['token'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$token || strlen($password) < 6) {
        die("❌ Invalid request or password too short.");
    }

    // Find user with valid token
    $stmt = $conn->prepare("SELECT id, reset_expires FROM users WHERE reset_token = ? LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
    $stmt->close();

    if (!$user) {
        die("❌ Invalid or expired token.");
    }

    if (strtotime($user['reset_expires']) < time()) {
        die("❌ Token has expired.");
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users 
                            SET password = ?, reset_token = NULL, reset_expires = NULL 
                            WHERE id = ?");
    $stmt->bind_param("si", $hashed, $user['id']);

    if ($stmt->execute()) {
        $stmt->close();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Password Reset Success</title>
            <style>
              body {
                font-family: 'Montserrat', sans-serif;
                background: url('images/imgbg.jpg') no-repeat center center fixed;
                background-size: cover;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
              }
              .success-box {
                background: rgba(255, 255, 255, 0.85);
                padding: 30px;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.2);
                text-align: center;
                width: 400px;
              }
              .success-box h2 {
                color: green;
                margin-bottom: 10px;
              }
              .success-box a {
                display: inline-block;
                margin-top: 15px;
                padding: 10px 20px;
                background: #007BFF;
                color: white;
                text-decoration: none;
                border-radius: 8px;
              }
              .success-box a:hover {
                background: #0056b3;
              }
            </style>
            <meta http-equiv="refresh" content="5;url=login.php">
        </head>
        <body>
          <div class="success-box">
            <h2>✅ Password Reset Successful!</h2>
            <p>Your password has been updated. You can now log in with your new password.</p>
            <a href="login.php">Go to Login</a>
            <p style="margin-top:10px; font-size:14px; color:#555;">
              (You will be redirected automatically in 5 seconds.)
            </p>
          </div>
        </body>
        </html>
        <?php
        exit;
    } else {
        $stmt->close();
        die("❌ Failed to reset password. Try again.");
    }
}
?>
