<?php
// Include necessary files for the database connection and classes
require_once 'C:/xampp/htdocs/BarcodeScanner/classes/Database.php';
require_once 'C:/xampp/htdocs/BarcodeScanner/classes/Product_History.php';

// Create a database connection
$database = new Database();
$db = $database->getConnection();

// Create ProductHistory object
$productHistory = new ProductHistory($db);

// Get all history records
$stmt = $productHistory->getHistory();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Product Scan History</h1>
        
        <table class="history-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Barcode</th>
                    <th>Scan Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['barcode']); ?></td>
                        <td><?php echo htmlspecialchars($row['scanned_at']); ?></td>
                        <td>
                            <a href="delete_history.php?id=<?php echo $row['id']; ?>" class="delete-link">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="productlist.php" class="back-link">Back to Product List</a>
    </div>
</body>
</html>

