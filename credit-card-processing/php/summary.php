<?php
session_start();
include 'db.php';

header("Content-Type: application/json");

$email = $_SESSION['email'] ?? '';
if (!$email) {
  echo json_encode(["status" => "error", "message" => "User not logged in"]);
  exit;
}

// Total Paid
$totalQuery = $conn->prepare("SELECT SUM(amount) AS total FROM transactions WHERE email = ?");
$totalQuery->bind_param("s", $email);
$totalQuery->execute();
$totalResult = $totalQuery->get_result()->fetch_assoc();
$totalPaid = $totalResult['total'] ?? 0;

// Daily Summary (last 7 days)
$dailyQuery = $conn->prepare("
  SELECT DATE(timestamp) AS date, SUM(amount) AS total
  FROM transactions
  WHERE email = ?
  GROUP BY DATE(timestamp)
  ORDER BY date DESC
  LIMIT 7
");
$dailyQuery->bind_param("s", $email);
$dailyQuery->execute();
$dailyResult = $dailyQuery->get_result();

$dailyData = [];
while ($row = $dailyResult->fetch_assoc()) {
  $dailyData[] = $row;
}

echo json_encode([
  "status" => "success",
  "total_paid" => $totalPaid,
  "daily" => array_reverse($dailyData) // Oldest to latest
]);
