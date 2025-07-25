<?php
header('Content-Type: application/json');
include 'db.php';

$response = [
  "total" => 0,
  "max" => 0,
  "last" => 0,
  "labels" => [],
  "values" => []
];

// Group by date for graph (last 10 days)
$sql = "SELECT DATE(date) as txn_date, SUM(amount) as total_amount
        FROM transactions
        GROUP BY DATE(date)
        ORDER BY txn_date DESC
        LIMIT 10";

$result = $conn->query($sql);
$labels = [];
$values = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = date('d-M', strtotime($row['txn_date']));
    $values[] = (float)$row['total_amount'];
}
$response['labels'] = array_reverse($labels);
$response['values'] = array_reverse($values);

// Summary
$totalQ = $conn->query("SELECT COUNT(*) as total FROM transactions");
$maxQ = $conn->query("SELECT MAX(amount) as max FROM transactions");
$lastQ = $conn->query("SELECT amount FROM transactions ORDER BY date DESC LIMIT 1");

$response['total'] = $totalQ->fetch_assoc()['total'] ?? 0;
$response['max'] = $maxQ->fetch_assoc()['max'] ?? 0;
$response['last'] = $lastQ->fetch_assoc()['amount'] ?? 0;

echo json_encode($response);
$conn->close();
?>
