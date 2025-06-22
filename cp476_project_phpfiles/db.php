<?php
$servername = "###.###.#.##"; // ← IP address or domain of their machine
$username = "#######";       // ← MySQL username they created for you
$password = "########";       // ← Password they gave you
$dbname = "########";            // ← Their actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); //error detection 
}
?>