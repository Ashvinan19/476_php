-- 1. Create InventoryTable structure
CREATE TABLE IF NOT EXISTS InventoryTable (
    ProductID INT,
    ProductName VARCHAR(100),
    Quantity INT,
    Price DECIMAL(10,2),
    Status CHAR(1),
    SupplierName VARCHAR(100),
    PRIMARY KEY (ProductID, SupplierName, Status)
);

-- 2. Populate InventoryTable by joining ProductTable and SupplierTable
INSERT INTO InventoryTable (ProductID, ProductName, Quantity, Price, Status, SupplierName)
SELECT 
    p.ProductID,
    p.ProductName,
    SUM(p.Quantity) AS Quantity,
    MAX(p.Price) AS Price,  -- Change to AVG/MIN if needed
    p.Status,
    s.SupplierName
FROM 
    ProductTable p
JOIN 
    SupplierTable s ON p.SupplierID = s.SupplierID
GROUP BY 
    p.ProductID, p.ProductName, p.Status, s.SupplierName
ORDER BY 
    p.ProductID ASC;
