<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "asma@2005";
$dbname = "medmealsignin_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>