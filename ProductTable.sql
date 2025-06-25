USE cp476_project;

-- 1. Create Table
CREATE TABLE IF NOT EXISTS ProductTable(
    ID INT AUTO_INCREMENT PRIMARY KEY,
    ProductID INT,
    ProductName VARCHAR(100) NOT NULL,
    Description TEXT,
    Price DECIMAL(10,2),
    Quantity INT,
    Status VARCHAR(20)
    SupplierID INT,
    FOREIGN KEY (SupplierID) REFERENCES SupplierTable(SupplierID)
);


