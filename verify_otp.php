  <?php
session_start();
include 'db.php';

$message = "";
$status  = "";

// Show success message if redirected from resend
if (isset($_GET['success']) && $_GET['success'] === 'resent') {
    $message = "✅ A new OTP has been sent to your email.";
    $status  = "success";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $otpArray = $_POST['otp'] ?? [];
    $otp = is_array($otpArray) ? implode('', $otpArray) : trim($otpArray);

    if (!$email || !$otp) {
        $message = "❌ Missing email or OTP.";
        $status  = "error";
    } else {
        $stmt = $conn->prepare("SELECT id, otp_code, otp_expires_at, email_verified 
                                FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();

        if (!$user) {
            $message = "❌ No account found with that email.";
            $status  = "error";
        } elseif ($user['email_verified']) {
            $message = "✅ This email is already verified. You can log in now.";
            $status  = "success";
        } elseif ($user['otp_code'] !== $otp) {
            $message = "❌ Invalid OTP code.";
            $status  = "error";
        } elseif (strtotime($user['otp_expires_at']) < time()) {
            $message = "❌ OTP has expired. Please request a new one.";
            $status  = "error";
        } else {
            $upd = $conn->prepare("UPDATE users 
                                   SET email_verified = 1, otp_code = NULL, otp_expires_at = NULL 
                                   WHERE id = ?");
            $upd->bind_param("i", $user['id']);
            if ($upd->execute()) {
                // Redirect to login after success
                header("Location: login.php?verified=1");
                exit;
            } else {
                $message = "❌ Failed to update verification status.";
                $status  = "error";
            }
            $upd->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>OTP Verification</title>
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
    color: #fff; /* default body text */
  }

  .otp-box {
    background: rgba(255, 255, 255, 0.75); /* semi-transparent white */
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    width: 400px;
    text-align: center;
    color: #000; /* force all text inside to black */
  }

  .otp-box h2 {
    margin-bottom: 10px;
    color: #000; /* black heading */
  }

  .message.success {
    color: green;
    font-weight: bold;
    margin-bottom: 15px;
  }
  .message.error {
    color: red;
    font-weight: bold;
    margin-bottom: 15px;
  }

  .otp-inputs {
    display: flex;
    justify-content: space-between;
    margin: 20px 0;
  }

  .otp-inputs input {
    width: 50px; height: 50px;
    text-align: center;
    font-size: 20px;
    border: 2px solid #ddd;
    border-radius: 8px;
    outline: none;
    transition: 0.2s;
    color: #000; /* black input text */
    background: rgba(255,255,255,0.9); /* solid enough for readability */
  }

  .otp-inputs input:focus {
    border-color: #007BFF;
    box-shadow: 0 0 4px rgba(0,123,255,0.4);
  }

  /* Primary button (Verify) */
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
    margin-top: 10px;
  }

  button:hover {
    background: #0056b3;
  }

  /* Resend OTP as plain text link */
  button.resend-btn {
    background: none;
    border: none;
    color: #007BFF;
    font-size: 14px;
    cursor: pointer;
    text-decoration: underline;
    margin-top: 10px;
  }

  button.resend-btn:hover {
    color: #0056b3;
    text-decoration: none;
  }

  button.resend-btn:disabled {
    color: #888;
    cursor: not-allowed;
    text-decoration: none;
  }
</style>


</head>
<body>
  <div class="otp-box">
    <!-- Success/Error card for resend -->
    <div id="resendMessage" style="display:none; margin-top:15px; padding:12px; border-radius:8px; font-weight:bold;"></div>

    <h2>Verify Your Email</h2>
    <p>Enter the 6-digit code sent to your email.</p>

    <?php if ($message): ?>
      <p class="message <?php echo $status; ?>"><?php echo $message; ?></p>
    <?php endif; ?>

    <?php if ($status !== "success"): ?>
      <form action="verify_otp.php" method="POST">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? $_POST['email'] ?? ''); ?>">
        <div class="otp-inputs">
          <input type="text" name="otp[]" maxlength="1" required>
          <input type="text" name="otp[]" maxlength="1" required>
          <input type="text" name="otp[]" maxlength="1" required>
          <input type="text" name="otp[]" maxlength="1" required>
          <input type="text" name="otp[]" maxlength="1" required>
          <input type="text" name="otp[]" maxlength="1" required>
        </div>
        <button type="submit">Verify</button>
      </form>

      <!-- Resend OTP -->
      <form id="resendForm" action="resend_otp.php" method="POST">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? $_POST['email'] ?? ''); ?>">
        <button type="submit" id="resendBtn" class="resend-btn">Resend OTP</button>
      </form>
      <p id="cooldownMsg" style="color:#555; font-size:14px; display:none; margin-top:8px;"></p>

    <?php endif; ?>
  </div>

  <script>
    const inputs = document.querySelectorAll('.otp-inputs input');
    inputs.forEach((input, index) => {
      input.addEventListener('input', () => {
        if (input.value.length === 1 && index < inputs.length - 1) {
          inputs[index + 1].focus();
        }
      });
      input.addEventListener('keydown', (e) => {
        if (e.key === "Backspace" && input.value === "" && index > 0) {
          inputs[index - 1].focus();
        }
      });
    });
  </script>
  <script>
    const resendForm = document.getElementById('resendForm');
    const resendBtn = document.getElementById('resendBtn');
    const cooldownMsg = document.getElementById('cooldownMsg');
    const resendMessage = document.getElementById('resendMessage');
    let cooldown = false;

    resendForm.addEventListener('submit', (e) => {
      e.preventDefault(); // stop normal form submission

      if (cooldown) return;

      const formData = new FormData(resendForm);

      // AJAX request
      fetch('resend_otp.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.text())
      .then(data => {
        // Show message card
        resendMessage.style.display = "block";
        resendMessage.style.background = "#d4edda"; // green
        resendMessage.style.color = "#155724";
        resendMessage.style.border = "1px solid #c3e6cb";
        resendMessage.innerHTML = "✅ A new OTP has been sent. Please check your email.";

        // Start cooldown
        cooldown = true;
        let seconds = 30;
        resendBtn.disabled = true;
        resendBtn.classList.add("disabled");
        cooldownMsg.style.display = "block";
        cooldownMsg.innerText = `⏳ Please wait ${seconds} seconds before requesting again.`;

        const timer = setInterval(() => {
          seconds--;
          cooldownMsg.innerText = `⏳ Please wait ${seconds} seconds before requesting again.`;
          if (seconds <= 0) {
            clearInterval(timer);
            cooldown = false;
            resendBtn.disabled = false;
            resendBtn.classList.remove("disabled");
            cooldownMsg.style.display = "none";
          }
              }, 1000);
            })
            .catch(err => {
              resendMessage.style.display = "block";
              resendMessage.style.background = "#f8d7da"; // red
              resendMessage.style.color = "#721c24";
              resendMessage.style.border = "1px solid #f5c6cb";
              resendMessage.innerHTML = "❌ Failed to send OTP. Please try again.";
            });
          });
    </script>

</body>
</html>
