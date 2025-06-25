<?php
$servername = "localhost"; // replace with your DB host if needed
$username = "admin";        // replace with actual DB username
$password = "pass123";            // replace with actual DB password
$dbname = "cp476_project"; // replace with your actual DB name

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully"; // optional debug message
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>