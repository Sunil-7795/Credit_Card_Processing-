<?php
session_start();
include 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$enteredOtp = trim($_POST['otp'] ?? '');
$storedEmail = $_SESSION['email'] ?? '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>OTP Verification</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #43cea2, #185a9d);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card {
      background: white;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
      text-align: center;
      width: 100%;
      max-width: 400px;
    }
    #countdown {
      font-weight: bold;
      margin-top: 10px;
    }
    #resendBtn {
      display: none;
    }
  </style>
</head>
<body>
  <div class="card">
    <?php
    if (!$storedEmail || !$enteredOtp) {
        echo "<h4 class='text-danger'>Session expired or invalid access.</h4>
              <a href='../index.html?login=true' class='btn btn-danger mt-3'>Back to Login</a>";
        exit;
    }

    // Check OTP
    $stmt = $conn->prepare("SELECT otp, otp_expiry FROM users WHERE email = ?");
    $stmt->bind_param("s", $storedEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $storedOtp = $row['otp'];
        $expiry = $row['otp_expiry'];

        if ($enteredOtp === $storedOtp && strtotime($expiry) > time()) {
            // Valid OTP
            $clear = $conn->prepare("UPDATE users SET otp = NULL, otp_expiry = NULL WHERE email = ?");
            $clear->bind_param("s", $storedEmail);
            $clear->execute();

            echo "<h4 class='text-success'>✅ OTP Verified</h4>
                  <p>Welcome, <strong>$storedEmail</strong></p>
                  <p>Redirecting to dashboard...</p>
                  <script>
                    setTimeout(() => window.location.href = '../dashboard.html', 2000);
                  </script>";
        } else {
            echo "<h4 class='text-danger'>❌ Invalid or Expired OTP</h4>
                  <p id='countdown'>⏳ OTP expires in 30 seconds</p>
                  <button id='resendBtn' class='btn btn-warning mt-2' onclick='resendOTP()'>🔄 Resend OTP</button>";
        }
    } else {
        echo "<h4 class='text-danger'>❌ Email not found.</h4>
              <a href='../index.html?login=true' class='btn btn-warning mt-3'>Back</a>";
    }
    ?>
  </div>

  <?php if (isset($storedOtp) && $enteredOtp !== $storedOtp): ?>
  <script>
    let seconds = 30;
    const countdownEl = document.getElementById("countdown");
    const resendBtn = document.getElementById("resendBtn");

    const timer = setInterval(() => {
      countdownEl.textContent = `⏳ OTP expires in ${seconds} seconds`;
      if (seconds === 0) {
        clearInterval(timer);
        countdownEl.innerHTML = "❌ OTP expired.";
        resendBtn.style.display = "inline-block";
      }
      seconds--;
    }, 1000);

    function resendOTP() {
      fetch("../php/send_otp.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: `email=<?php echo urlencode($storedEmail); ?>`
      })
      .then(() => {
        alert("✅ New OTP sent to your email.");
        window.location.href = "../verify_otp.html";
      });
    }
  </script>
  <?php endif; ?>

</body>
</html>
