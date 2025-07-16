<?php
session_start();
// Debug: Check current session state (for troubleshooting only)
// echo "Session user: " . ($_SESSION["user"] ?? "not set") . "<br>";
require_once '../includes/db.php';

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
            echo "Session user just set: " . $_SESSION["user"];
            exit();
        } else {
            error_log("Password mismatch for user: $username");
            echo "ERROR: " . $username . " - Password mismatch.";
            $error = "Invalid username or password.";
        }
    } else {
        error_log("No user found with username: $username");
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventory Admin</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body class="login-page">
    <?php include '../includes/navbar.php'; ?>
    <div class="layout-wrapper">
        <div class="login-main">
            <div class="container">
                <h2>Login</h2>
                <?php if (!empty($error)): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required placeholder="Enter your username">
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required placeholder="Enter your password">
                    </div>
                    <button type="submit" class="button-link">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>