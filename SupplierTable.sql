-- To create the database
USE cp476_project;

-- 1.Create Supplier Table 
CREATE TABLE IF NOT EXISTS SupplierTable(
    SupplierID INT PRIMARY KEY,
    SupplierName VARCHAR(100) NOT NULL,
    Address VARCHAR(255),
    Phone VARCHAR(20),
    Email VARCHAR(100)
);
