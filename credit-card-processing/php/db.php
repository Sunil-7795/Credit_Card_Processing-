<?php
// Database connection settings
$host = "localhost";
$user = "root";         // default for XAMPP
$password = "";         // default password is empty
$database = "credit_processing_system"; // your new DB name

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
