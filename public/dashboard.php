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
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div class="main-wrapper centered-dashboard">
        <div class="container dashboard-container">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>!</h2>
            <p>This is your dashboard.</p>
            <div class="button-group">
                <a class="button-link" href="uploadFile.php">Upload Product File</a>
                <a class="button-link" href="search.php">Search Inventory</a>
                <a class="button-link" href="update.php">Update Records</a>
                <a class="button-link" href="delete.php">Delete Records</a>
                <a class="button-link" href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>