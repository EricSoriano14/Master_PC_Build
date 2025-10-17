<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      background: url('images/imgbg.jpg') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      font-family: 'Montserrat', sans-serif;
      height: 100vh;
      margin: 0;
      color: #fff;
    }
    .forgot-box {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(10px);
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.2);
      width: 400px;
      text-align: center;
      color: #000;
    }
    .forgot-box h2 { margin-bottom: 15px; color: #000; }
    input[type="email"] {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
    }
    button {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 8px;
      background: #007BFF;
      color: white;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s;
    }
    button:hover { background: #0056b3; }
  </style>
</head>
<body>
  <div class="forgot-box">
    <h2>Forgot Password</h2>
    <p>Enter your email to receive a reset link.</p>
    <form action="forgot_password_action.php" method="POST">
      <input type="email" name="email" placeholder="Enter your email" required>
      <button type="submit">Send Reset Link</button>
    </form>
    <p><a href="index.html">Back to Login</a></p>
  </div>
</body>
</html>
