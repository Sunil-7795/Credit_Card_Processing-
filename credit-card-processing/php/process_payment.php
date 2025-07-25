<?php
session_start();
include 'db.php';

$email = $_SESSION['email'] ?? '';
$card_holder = $_POST['card_holder'] ?? '';
$card_number = $_POST['card_number'] ?? '';
$expiry = $_POST['expiry_date'] ?? '';
$amount = $_POST['amount'] ?? '';

if (!is_numeric($amount) || empty($card_holder) || empty($card_number) || empty($expiry) || empty($email)) {
    echo json_encode(["status" => "error", "message" => "Invalid input."]);
    exit;
}

// Optional: daily limit check (₹50,000)
$today = date('Y-m-d');
$check = $conn->prepare("SELECT SUM(amount) AS total FROM transactions WHERE email = ? AND DATE(timestamp) = ?");
$check->bind_param("ss", $email, $today);
$check->execute();
$res = $check->get_result()->fetch_assoc();
$totalToday = $res['total'] ?? 0;

if ($totalToday + $amount > 50000) {
    echo json_encode(["status" => "error", "message" => "You exceeded ₹50,000 daily limit."]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO transactions (email, card_holder, card_number, expiry_date, amount) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssssd", $email, $card_holder, $card_number, $expiry, $amount);
if ($stmt->execute()) {
    echo "✅ Transaction added successfully.";
} else {
    echo "❌ Error adding transaction.";
}
?>
