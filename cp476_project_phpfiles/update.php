<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productID = $_POST["product_id"] ?? '';
    $newQuantity = $_POST["quantity"] ?? '';

    try {
        $stmt = $pdo->prepare("UPDATE InventoryTable SET Quantity = ? WHERE ProductID = ?");
        $stmt->execute([$newQuantity, $productID]);

        if ($stmt->rowCount()) {
            $message = "Record updated successfully.";
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
    <title>Update Quantity</title>
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
    <h2>Update Product Quantity</h2>
    <form method="post">
        <label>Product ID:</label><br>
        <input type="number" name="product_id" required><br><br>
        <label>New Quantity:</label><br>
        <input type="number" name="quantity" required><br><br>
        <input type="submit" value="Update">
    </form>

    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
</body>
</html>