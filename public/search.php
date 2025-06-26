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

try {
    if ($submitType === "Search" && !empty($search)) {
        $stmt = $pdo->prepare("SELECT * FROM InventoryTable WHERE ProductName LIKE ? OR ProductID = ?");
        $stmt->execute(["%" . $search . "%", $search]);
    } else {
        $stmt = $pdo->query("SELECT * FROM InventoryTable");
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
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <h2>Search Inventory</h2>
    <form method="post">
        <label>Enter Product Name or Product ID:</label>
        <input type="text" name="search">
        <input type="submit" name="submit" value="Search">
        <input type="submit" name="submit" value="Show All">
    </form>

    <?php if (!empty($results)): ?>
        <h3>Search Results:</h3>
        <table border="1">
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Status</th>
                <th>Supplier Name</th>
            </tr>
            <?php foreach ($results as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['ProductID']); ?></td>
                    <td><?php echo htmlspecialchars($row['ProductName']); ?></td>
                    <td><?php echo htmlspecialchars($row['Quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['Price']); ?></td>
                    <td><?php echo htmlspecialchars($row['Status']); ?></td>
                    <td><?php echo htmlspecialchars($row['SupplierName']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <p>No results found.</p>
    <?php endif; ?>
</body>
</html>