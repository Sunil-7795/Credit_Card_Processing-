<?php
session_start();
if (!isset($_SESSION['email'])) {
  header("Location: index.html");
  exit;
}
$email = $_SESSION['email'];
?>

<!-- Inside navbar -->
<span class="text-white me-3">👤 <?php echo htmlspecialchars($email); ?></span>
