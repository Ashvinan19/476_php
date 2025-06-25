<?php
session_start();
// Debug: Check current session state (for troubleshooting only)
//echo "Session user: " . ($_SESSION["user"] ?? "not set") . "<br>";
require_once 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"] ?? '';
    $password = $_POST["password"] ?? '';

    // Simple hardcoded login for demonstration
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        error_log("User found: $username");
        if (password_verify($password, $user['password'])) {
            error_log("Password matched for user: $username");
            $_SESSION["user"] = $username;
            header("Location: dashboard.php?login=success");
            exit();
        } else {
            error_log("Password mismatch for user: $username");
            $error = "Invalid username or password.";
        }
    } else {
        error_log("No user found with username: $username");
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
    <h2>Login</h2>
    <?php if (!empty($error)): ?>
        <div style="color:red; font-weight:bold; margin-bottom:15px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
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