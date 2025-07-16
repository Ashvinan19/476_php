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
    $newQuantity = $_POST["quantity"] ?? '';
    $productName = $_POST["product_name"] ?? '';
    $price = $_POST["price"] ?? '';
    $status = $_POST["status"] ?? '';
    $updateFields = [];
    $params = [];

    if ($productName !== '') {
        $updateFields[] = "ProductName = ?";
        $params[] = $productName;
    }
    if ($price !== '') {
        $updateFields[] = "Price = ?";
        $params[] = $price;
    }
    if ($newQuantity !== '') {
        $updateFields[] = "Quantity = ?";
        $params[] = $newQuantity;
    }
    if ($status !== '') {
        $updateFields[] = "Status = ?";
        $params[] = $status;
    }

    // Check if record exists
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM InventoryTable WHERE ProductID = ? AND TRIM(SupplierName) COLLATE utf8mb4_general_ci = TRIM(?) COLLATE utf8mb4_general_ci");
    $checkStmt->execute([$productID, $supplierName]);

    if ($checkStmt->fetchColumn() == 0) {
        $message = "<div class='error-message'>No record found with Product ID {$productID} and Supplier '{$supplierName}'. Please double-check your values.</div>";
    } else {
        if (!empty($updateFields)) {
            $params[] = $productID;
            $params[] = $supplierName;

            $sql = "UPDATE InventoryTable SET " . implode(", ", $updateFields) . "
                    WHERE ProductID = ? 
                    AND TRIM(SupplierName) COLLATE utf8mb4_general_ci = TRIM(?) COLLATE utf8mb4_general_ci";

            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                if ($stmt->rowCount()) {
                    $message = "<div class='success-message'>Record was successfully updated.</div>";
                } else {
                    $message = "<div class='error-message'>No matching record was found or no changes made.</div>";
                }
            } catch (PDOException $e) {
                $message = "<div class='error-message'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        } else {
            $message = "<div class='error-message'>Please fill out at least one field to update.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Product Quantity</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="main-content">
        <div class="container">
            <?php if ($message): ?>
                <?php echo $message; ?>
            <?php endif; ?>

            <h2>Update Product Quantity</h2>
            <form method="post">
                <fieldset style="margin-bottom: 20px;">
                    <legend><strong>Step 1: Identify Record to Update</strong></legend>
                    <p style="font-size: 14px; color: #555;">Enter the Product ID and Supplier Name to identify the existing record. These fields are used to find the record to update.</p>

                    <label for="product_id">Product ID:</label>
                    <input type="number" name="product_id" id="product_id" required>

                    <label for="supplier_name">Supplier Name:</label>
                    <input type="text" name="supplier_name" id="supplier_name" required>
                </fieldset>

                <label for="product_name">Product Name:</label>
                <input type="text" name="product_name" id="product_name">

                <label for="price">Price:</label>
                <input type="number" name="price" id="price" step="0.01">

                <label for="quantity">New Quantity:</label>
                <input type="number" name="quantity" id="quantity">

                <label for="status">Status:</label>
                <select name="status" id="status">
                    <option value="">-- Select --</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                </select>

                <input type="submit" value="Update">
            </form>
        </div>
    </div>
</body>
</html>