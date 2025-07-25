<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
  echo json_encode([]);
  exit;
}

$email = $_SESSION['email'];

$stmt = $conn->prepare("SELECT * FROM transactions WHERE email = ? ORDER BY timestamp DESC");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];
while ($row = $result->fetch_assoc()) {
  $transactions[] = $row;
}

echo json_encode($transactions);
