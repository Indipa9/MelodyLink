<?php
// Database connection settings
$host = 'localhost';
$dbname = 'MelodyLink';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>