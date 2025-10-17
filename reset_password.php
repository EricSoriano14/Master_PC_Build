<?php
include 'db.php';

// Get token from URL
$token = $_GET['token'] ?? '';

if (!$token) {
    die("❌ Invalid request: missing token.");
}

// Check if token exists in DB
$stmt = $conn->prepare("SELECT id, reset_token, reset_expires_at FROM users WHERE reset_token=? LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$res  = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

// Debug if no row found
if (!$user) {
    die("❌ Invalid token (not found in DB).");
}

// Check expiry
if (strtotime($user['reset_expires_at']) < time()) {
    die("❌ Token expired. Expired at: " . $user['reset_expires_at'] . 
        " | Server Time: " . date("Y-m-d H:i:s"));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
  <style>
    body {
      background: url('images/imgbg.jpg') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      font-family: 'Montserrat', sans-serif;
    }
    .reset-box {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(8px);
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      width: 400px;
      text-align: center;
      color: #000;
    }
    input {
      width: 100%;
      padding: 12px;
      margin: 8px 0;
      border-radius: 8px;
      border: 1px solid #ccc;
    }
    button {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 8px;
      background: #007BFF;
      color: #fff;
      font-size: 16px;
      cursor: pointer;
      margin-top: 10px;
    }
    button:hover { background: #0056b3; }
  </style>
</head>
<body>
  <div class="reset-box">
    <h2>Reset Your Password</h2>
    <form action="reset_password_action.php" method="POST">
      <!-- Pass token safely -->
      <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
      <input type="password" name="password" placeholder="New Password" required>
      <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      <button type="submit">Reset Password</button>
    </form>
  </div>
</body>
</html>
