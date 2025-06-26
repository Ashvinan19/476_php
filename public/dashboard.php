

<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>!</h2>
    <p>This is your dashboard.</p>
    <ul>
        <li><a href="uploadFile.php">Upload Product File</a></li>
        <li><a href="search.php">Search Inventory</a></li>
        <li><a href="update.php">Update Records</a></li>
        <li><a href="delete.php">Delete Records</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>