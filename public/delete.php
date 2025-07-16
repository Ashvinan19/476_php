<?php
session_start();
require_once('../includes/db.php');

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productID = $_POST["product_id"] ?? '';
    $supplierName = $_POST["supplier_name"] ?? '';

    try {
        $stmt = $pdo->prepare("
            DELETE FROM InventoryTable 
            WHERE ProductID = ? 
              AND TRIM(SupplierName) COLLATE utf8mb4_general_ci = TRIM(?) COLLATE utf8mb4_general_ci
        ");
        $stmt->execute([$productID, $supplierName]);

        if ($stmt->rowCount()) {
            $message = "Record was successfully deleted.";
        } else {
            $message = "No matching record was found for ProductID {$productID} and Supplier '{$supplierName}'.";
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
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="main-content">
        <div class="container">
            <h2>Delete Product from Inventory</h2>
            <form method="post">
                <div class="form-group">
                    <label for="product_id">Product ID:</label>
                    <input type="number" name="product_id" id="product_id" required>
                </div>

                <div class="form-group">
                    <label for="supplier_name">Supplier Name:</label>
                    <input type="text" name="supplier_name" id="supplier_name" required>
                </div>

                <input class="button-link" type="submit" value="Delete">
            </form>

            <?php if ($message): ?>
                <div class="<?php echo str_starts_with($message, 'Error') ? 'error-message' : 'success-message'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
