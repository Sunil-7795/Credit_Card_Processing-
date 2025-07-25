<?php
session_start();
include 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // If resend, get email from session
    if (isset($_POST['resend']) && isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
    } else {
        $email = trim($_POST['email']);
        $_SESSION['email'] = $email;
    }

    // Check if email exists in DB
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Generate and save OTP
        $otp = rand(100000, 999999);
        $expiry = date("Y-m-d H:i:s", time() + 30); // 30 seconds expiry
        $_SESSION['otp'] = $otp;

        $update = $conn->prepare("UPDATE users SET otp = ?, otp_expiry = ? WHERE email = ?");
        $update->bind_param("sss", $otp, $expiry, $email);
        $update->execute();

        // Show success or redirect
        header("Location: ../verify_otp.html");
        exit;
    } else {
        echo "<p style='color:red; text-align:center;'>❌ Email not found. <br><a href='../index.html?login=true'>🔙 Go Back</a></p>";
    }
}
?>
