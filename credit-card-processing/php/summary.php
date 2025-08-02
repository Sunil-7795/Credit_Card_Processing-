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

// Available Balance (limit ₹100000)
$availableBalance = 100000 - $totalPaid;

// Today's Total
$todayQuery = $conn->prepare("
  SELECT SUM(amount) AS total
  FROM transactions
  WHERE email = ? AND DATE(timestamp) = CURDATE()
");
$todayQuery->bind_param("s", $email);
$todayQuery->execute();
$todayResult = $todayQuery->get_result()->fetch_assoc();
$todayTotal = $todayResult['total'] ?? 0;

// Max Transaction
$maxQuery = $conn->prepare("
  SELECT MAX(amount) AS max_txn
  FROM transactions
  WHERE email = ?
");
$maxQuery->bind_param("s", $email);
$maxQuery->execute();
$maxResult = $maxQuery->get_result()->fetch_assoc();
$maxTransaction = $maxResult['max_txn'] ?? 0;

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
  "availableBalance" => $availableBalance,
  "todayTotal" => $todayTotal,
  "maxTransaction" => $maxTransaction,
  "email" => $email,
  "daily" => array_reverse($dailyData)
]);
