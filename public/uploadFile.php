<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['the_file']) && $_FILES['the_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['the_file']['tmp_name'];
        $fileName = $_FILES['the_file']['name'];
        $uploadFileDir = './uploads/';
        $dest_path = $uploadFileDir . $fileName;

        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // echo "<p>Your file has been uploaded.</p>";

            // Use $pdo from db.php
            try {
                $table = $_POST['table'];

                if (($handle = fopen($dest_path, "r")) !== false) {
                    while (($data = fgetcsv($handle, 1000, ",", '"', "\\")) !== false) {
                        if ($data === [null] || $data === false) continue;
                        if ($table === "Product") {
                            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM ProductTable WHERE ProductID = ? AND SupplierID = ? AND Status = ?");
                            $insertStmt = $pdo->prepare("INSERT INTO ProductTable (ProductID, ProductName, Description, Price, Quantity, Status, SupplierID) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        } elseif ($table === "Supplier") {
                            $stmt = $pdo->prepare("INSERT INTO SupplierTable (SupplierID, SupplierName, Address, Phone, Email) VALUES (?, ?, ?, ?, ?)");
                        } else {
                            throw new Exception("Invalid table selection.");
                        }
                        // Check for correct number of columns before attempting to insert
                        if (($table === "Product" && count($data) !== 7) || ($table === "Supplier" && count($data) !== 5)) {
                            echo "<p style='color:red;'>Incorrect number of columns for table $table: " . htmlspecialchars(implode(", ", $data)) . "</p>";
                            continue;
                        }
                        // Truncate known fields to match column lengths
                            if ($table === "Product") {
                                $data[0] = (int) trim($data[0]);     // ProductID
                                $data[1] = substr(trim($data[1]), 0, 100); // ProductName
                                $data[2] = substr(trim($data[2]), 0, 255); // Description
                                $data[3] = (float) trim($data[3]);   // Price
                                $data[4] = (int) trim($data[4]);     // Quantity
                                $data[5] = substr(trim($data[5]), 0, 20);  // Status
                                $data[6] = (int) trim($data[6]);     // SupplierID
                            } elseif ($table === "Supplier") {
                                // SupplierID, SupplierName, Address, Phone, Email
                                $data[1] = substr($data[1], 0, 100); // SupplierName
                                $data[2] = substr($data[2], 0, 255); // Address
                                $data[3] = substr($data[3], 0, 20);  // Phone
                                $data[4] = substr($data[4], 0, 100); // Email
                            }
                        try {
                            // echo "<pre>Full row length: " . count($data) . " → " . htmlspecialchars(json_encode($data)) . "</pre>";
                            if ($table === "Product") {
                                $checkStmt->execute([$data[0], $data[6], $data[5]]);
                                if ($checkStmt->fetchColumn() == 0) {
                                    $insertStmt->execute($data);
                                } else {
                                    // echo "<p style='color:orange;'>Duplicate skipped for ProductID {$data[0]}, SupplierID {$data[6]}, Status '{$data[5]}'</p>";
                                }
                            } else {
                                $stmt->execute($data);
                            }
                        } catch (PDOException $e) {
                            if (str_contains($e->getMessage(), 'Integrity constraint violation')) {
                                echo "<p style='color:orange;'>Skipped duplicate row: " . htmlspecialchars(implode(", ", $data)) . "</p>";
                            } else {
                                throw $e;
                            }
                        }
                    }
                    fclose($handle);
                    // Refresh InventoryTable if Product or Supplier was updated
                    if ($table === "Product" || $table === "Supplier") {
                        $preview = $pdo->query("
                            SELECT 
                                p.ProductID,
                                p.ProductName,
                                SUM(p.Quantity) AS Quantity,
                                MAX(p.Price) AS Price,
                                p.Status,
                                s.SupplierName
                            FROM ProductTable p
                            JOIN SupplierTable s ON p.SupplierID = s.SupplierID
                            GROUP BY p.ProductID, p.ProductName, p.Status, s.SupplierName
                            ORDER BY p.ProductID ASC
                        ")->fetchAll(PDO::FETCH_ASSOC);

                        // Preview omitted in production

                        $pdo->exec("
                            DELETE FROM InventoryTable;

                            INSERT INTO InventoryTable (ProductID, ProductName, Quantity, Price, Status, SupplierName)
                            SELECT 
                                p.ProductID,
                                p.ProductName,
                                SUM(p.Quantity),
                                MAX(p.Price),
                                p.Status,
                                s.SupplierName
                            FROM ProductTable p
                            JOIN SupplierTable s ON p.SupplierID = s.SupplierID
                            GROUP BY p.ProductID, p.ProductName, p.Status, s.SupplierName
                            ORDER BY p.ProductID ASC
                        ");
                    }
                    // echo "<p>Data has been inserted into the $table table.</p>";
                    $successMessage = "Data successfully inserted into the $table table.";
                }
            } catch (PDOException $e) {
                echo "<p style='color:red;'>Database error: " . $e->getMessage() . "</p>";
            } catch (Exception $e) {
                echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p style='color:red;'>Failed to move uploaded file.</p>";
        }
    } else {
        echo "<p style='color:red;'>File upload error: ";
        switch ($_FILES['the_file']['error']) {
            case 4:
                echo "No file was uploaded.";
                break;
            case 6:
                echo "Missing temporary folder.";
                break;
            default:
                echo "Something unforeseen happened.";
        }
        echo "</p>";
    }
}
if (isset($successMessage)) {
    echo "<p class='success-message'>$successMessage</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload File</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="main-content">
        <div class="container">
            <h2>Upload CSV File to Product or Supplier Table</h2>
            <form method="post" enctype="multipart/form-data">
                <label>Select CSV file:</label><br>
                <input type="file" name="the_file" required><br><br>
                <label>Select target table:</label><br>
                <select name="table" required>
                    <option value="Product">Product</option>
                    <option value="Supplier">Supplier</option>
                </select><br><br>
                <input class="button-link" type="submit" value="Upload">
            </form>
        </div>
    </div>
</body>
</html>
