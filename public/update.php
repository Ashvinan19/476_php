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
    <?php include '../includes/navbar.php'; ?>
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