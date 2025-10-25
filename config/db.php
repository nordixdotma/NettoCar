<?php
// Database Connection Configuration
$host = "localhost";
$user = "root";
$password = "";
$database = "nettocar";

// Create connection using mysqli
$con = mysqli_connect($host, $user, $password);

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Select database
mysqli_select_db($con, $database);

// Set charset to UTF8
mysqli_query($con, "SET NAMES UTF8");

// Set error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>
