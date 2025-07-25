<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
      <meta charset='UTF-8'>
      <title>Register</title>
      <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
      <style>
        body {
          background: linear-gradient(to right, #1f4037, #99f2c8);
          height: 100vh;
          display: flex;
          align-items: center;
          justify-content: center;
          margin: 0;
        }
        .card {
          padding: 2rem;
          max-width: 400px;
          width: 100%;
          border-radius: 10px;
          box-shadow: 0 0 15px rgba(0,0,0,0.2);
          text-align: center;
        }
        .emoji {
          font-size: 2rem;
        }
      </style>
    </head>
    <body>";

    if ($result->num_rows > 0) {
        echo "<div class='card bg-light'>
                <div class='alert alert-danger'>
                  <div class='emoji'>⚠️</div>
                  <strong>Email already registered.</strong>
                </div>
                <a href='../index.html?login=true' class='btn btn-outline-primary'>🔐 Go to Login</a>
              </div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashedPassword);

        if ($stmt->execute()) {
            echo "<div class='card bg-white'>
                    <div class='alert alert-success'>
                      <div class='emoji'>✅</div>
                      <strong>Registration Successful!</strong><br>
                      Redirecting to login...
                    </div>
                  </div>
                  <script>
                    setTimeout(() => {
                      window.location.href = '../index.html?login=true';
                    }, 2000);
                  </script>";
        } else {
            echo "<div class='card bg-light'>
                    <div class='alert alert-danger'>❌ Registration failed. Please try again.</div>
                    <a href='../index.html' class='btn btn-outline-danger mt-2'>Back</a>
                  </div>";
        }
    }

    echo "</body></html>";

    $stmt->close();
    $conn->close();
} else {
    echo "<div class='text-center mt-5 text-danger'>⚠️ Invalid Request Method.</div>";
}
?>
