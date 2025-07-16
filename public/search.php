<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$results = [];
$search = $_POST["search"] ?? "";
$submitType = $_POST["submit"] ?? "";

$table = "InventoryTable"; // default

if ($submitType === "Product Table") {
    $table = "ProductTable";
} elseif ($submitType === "Supplier Table") {
    $table = "SupplierTable";
}

try {
    if ($submitType === "Search" && !empty($search)) {
        $stmt = $pdo->prepare("SELECT * FROM InventoryTable WHERE ProductName LIKE ? OR ProductID = ?");
        $stmt->execute(["%" . $search . "%", $search]);
    } else {
        $stmt = $pdo->query("SELECT * FROM $table");
    }
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p style='color:red;'>Database error: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Inventory</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">

</head><body>
    <?php include '../includes/navbar.php'; ?>
    <div class="main-content">
        <div class="container">
            <h2>Search Inventory</h2>
            <form method="post">
                <label>Enter Product Name or Product ID:</label>
                <input type="text" name="search" placeholder="Search products...">
                <input class="button-link" type="submit" name="submit" value="Search">
                <input class="button-link" type="submit" name="submit" value="Show All">
                <div class="table-button-row">
                    <input class="button-link inventory-btn<?php if ($table === 'InventoryTable') echo ' active'; ?>" type="submit" name="submit" value="Inventory Table">
                    <input class="button-link product-btn<?php if ($table === 'ProductTable') echo ' active'; ?>" type="submit" name="submit" value="Product Table">
                    <input class="button-link supplier-btn<?php if ($table === 'SupplierTable') echo ' active'; ?>" type="submit" name="submit" value="Supplier Table">
                </div>
            </form>

            <?php if (!empty($results)): ?>
                <h3>Search Results:</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <?php
                            if ($table === "InventoryTable") {
                                echo "<tr><th>Product ID</th><th>Product Name</th><th>Quantity</th><th>Price</th><th>Status</th><th>Supplier Name</th></tr>";
                            } elseif ($table === "ProductTable") {
                                echo "<tr><th>Product ID</th><th>Product Name</th><th>Description</th><th>Price</th><th>Quantity</th><th>Status</th><th>Supplier ID</th></tr>";
                            } elseif ($table === "SupplierTable") {
                                echo "<tr><th>Supplier ID</th><th>Supplier Name</th><th>Address</th><th>Phone</th><th>Email</th></tr>";
                            }
                            ?>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($results as $row) {
                                echo "<tr>";
                                foreach ($row as $value) {
                                    echo "<td>" . htmlspecialchars($value) . "</td>";
                                }
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                <p>No results found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>