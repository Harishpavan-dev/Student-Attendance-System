<?php
// Start session
session_start();

// Database configuration
$host = "";
$user = "";
$pass = "";
$db = "";

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set default timezone
date_default_timezone_set('Asia/Colombo');
?>

