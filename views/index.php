<?php
session_start();
require_once 'C:/xampp/htdocs/BarcodeScanner/classes/database.php';  // Database class
require_once 'C:/xampp/htdocs/BarcodeScanner/classes/product.php';   // Product class
require_once 'C:/xampp/htdocs/BarcodeScanner/classes/product_history.php'; // ProductHistory class

// Create a database connection
$database = new Database();
$db = $database->getConnection();

// Create Product and ProductHistory objects
$product = new Product($db);
$productHistory = new ProductHistory($db);

// Variable to hold product data
$productData = null;

// Check if barcode is submitted via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['barcode'])) {
    // Get the barcode from the POST request
    $barcode = $_POST['barcode'];

    // Search for the product by barcode
    $productData = $product->getProductByBarcode($barcode); // Assuming you have a method getProductByBarcode

    if ($productData) {
        // If product found, insert it into the product_history table
        $product_id = $productData['id'];
        $product_name = $productData['name'];
        
        // Call the insertScan method to record the scan
        if ($productHistory->insertScan($product_id, $barcode, $product_name)) {
            $message = "Product successfully scanned!";
        } else {
            $message = "Failed to record scan.";
        }
    } else {
        $message = "Product not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Product</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container1">
        <h1>Scan Product</h1>
        
        <form method="POST" action="">
            <table class="scan-table">
                <tr>
                    <td><label for="barcode">Scan or Enter Barcode:</label></td>
                    <td><input type="text" name="barcode" id="barcode" placeholder="Enter Barcode" required></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center;">
                        <button type="submit" class="btm">Scan</button>
                    </td>
                </tr>
            </table>
        </form>

        <?php
        // Display the message (success or failure)
        if (isset($message)) {
            echo "<p>$message</p>";
        }

        // Display the product details after successful scan
        if ($productData) {
            echo "<h3>Product Details:</h3>";
            echo "<table>";
            echo "<tr><td><strong>Name:</strong></td><td>" . htmlspecialchars($productData['name']) . "</td></tr>";
            echo "<tr><td><strong>Price:</strong></td><td>$" . number_format($productData['price'], 2) . "</td></tr>";  // Display the price with two decimals
            echo "</table>";
        }
        ?>

        <div class="navigation1">
            <a href="add_product.php">Add Product</a>
            <a href="productlist.php">Product List</a>
            <a href="history.php">View History</a>
        </div>
    </div>
</body>
</html>

