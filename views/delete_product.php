<?php
require_once 'C:/xampp/htdocs/BarcodeScanner/classes/Database.php';
require_once 'C:/xampp/htdocs/BarcodeScanner/classes/Product.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);

if (isset($_GET['id'])) {
    $product->id = $_GET['id'];

    if ($product->delete()) {
        $message = "Product was deleted.";
    } else {
        $message = "Unable to delete product.";
    }
} else {
    $message = "No product ID was provided.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Product</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Delete Product</h1>
        <p><?php echo $message; ?></p>

        <?php if (isset($productData)): ?>
            <form method="POST" action="delete_product.php?id=<?php echo $productData['id']; ?>">
                <table>
                    <tr>
                        <td><strong>Product Name:</strong></td>
                        <td><?php echo htmlspecialchars($productData['name']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Price:</strong></td>
                        <td><?php echo htmlspecialchars($productData['price']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Barcode:</strong></td>
                        <td><?php echo htmlspecialchars($productData['barcode']); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <p>Are you sure you want to delete this product?</p>
                            <input type="submit" value="Delete Product">
                            <a href="productlist.php">Cancel</a>
                        </td>
                    </tr>
                </table>
            </form>
        <?php else: ?>
            <a href="productlist.php">Back to Product List</a>
        <?php endif; ?>
    </div>
</body>
</html>
