<?php
require_once 'C:/xampp/htdocs/BarcodeScanner/classes/Database.php';
require_once 'C:/xampp/htdocs/BarcodeScanner/classes/Product.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = $_POST['name'];
    $barcode = $_POST['barcode'];
    $price = $_POST['price'];

    // Validate price to ensure it is a positive number
    if (!is_numeric($price) || $price <= 0) {
        $error_message = "Price must be a positive number.";
    } 
    // Validate barcode to ensure it is at most 13 characters
    else if (strlen($barcode) > 13) {
        $error_message = "Barcode must be at most 13 characters long.";
    } 
    else {
        // Check for existing product with the same name or barcode
        $query = "SELECT * FROM products WHERE barcode = :barcode OR name = :name";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':barcode', $barcode);
        $stmt->bindParam(':name', $name);
        $stmt->execute();

        // If a product with the same barcode or name exists, show an error message
        if ($stmt->rowCount() > 0) {
            $error_message = "A product with the same barcode or name already exists.";
        } else {
            // Insert product into database if no duplicates found
            $product->name = $name;
            $product->barcode = $barcode;
            $product->price = $price;

            if ($product->create()) {
                $success_message = "Product added successfully.";
            } else {
                $error_message = "Failed to add the product.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="container">
        <h1>Add Product</h1>

        <?php
        if (isset($error_message)) {
            echo "<p style='color: red;'>$error_message</p>";
        }
        if (isset($success_message)) {
            echo "<p style='color: green;'>$success_message</p>";
        }
        ?>

        <form method="POST" action="add_product.php">
            <table>
                <tr>
                    <td><label for="name">Product Name:</label></td>
                    <td><input type="text" id="name" name="name" required><br><br></td>
                </tr>
                <tr>
                    <td><label for="barcode">Barcode:</label></td>
                    <td><input type="text" id="barcode" name="barcode" required maxlength="13"><br><br></td>
                </tr>
                <tr>
                    <td><label for="price">Price:</label></td>
                    <td><input type="number" id="price" name="price" required min="0.01" step="0.01"><br><br></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <button type="submit">Add Product</button>
                    </td>
                </tr>
            </table>
        </form>

        <div class="navigation2">
            <a href="productlist.php">Go to Product List</a>
            <a href="index.php">Scan Product</a>
        </div>
    </div>
</body>
</html>
