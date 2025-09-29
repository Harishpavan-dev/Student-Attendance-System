<?php
// Start session
session_start();

// Database configuration
$host = "sql112.infinityfree.com";
$user = "if0_38371120";
$pass = "Hh2468024";
$db = "if0_38371120_hndit_attendance_db";

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set default timezone
date_default_timezone_set('Asia/Colombo');
?>
