<?php
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"] ?? '';
    $password = $_POST["password"] ?? '';

    // Basic hardcoded login check (for demo only)
    if ($username === "admin" && $password === "pass123") {
        $_SESSION["user"] = $username;
        header("Location: uploadFile.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <div style="position: fixed; top: 0; right: 0; width: 200px; height: 100%; background-color: #f0f0f0; padding: 20px;">
        <h4>Navigation</h4>
        <ul style="list-style-type: none; padding: 0;">
            <li><a href="uploadFile.php">Upload File</a></li>
            <li><a href="search.php">Search Inventory</a></li>
            <li><a href="delete.php">Delete Product</a></li>
            <li><a href="update.php">Update Product</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <h2>Login Page</h2>
    <form method="post" action="">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        <input type="submit" value="Login">
    </form>
    <?php if ($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
</body>
</html>