<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productID = $_POST["product_id"] ?? '';

    try {
        $stmt = $pdo->prepare("DELETE FROM InventoryTable WHERE ProductID = ?");
        $stmt->execute([$productID]);

        if ($stmt->rowCount()) {
            $message = "Record deleted successfully.";
        } else {
            $message = "No matching record found.";
        }
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Product</title>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <h2>Delete Product from Inventory</h2>
    <form method="post">
        <label>Product ID:</label><br>
        <input type="number" name="product_id" required><br><br>
        <input type="submit" value="Delete">
    </form>

    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
</body>
</html>