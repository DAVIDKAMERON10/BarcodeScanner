<?php
require_once 'C:/xampp/htdocs/SCANNEROOP/classes/Database.php';
require_once 'C:/xampp/htdocs/SCANNEROOP/classes/Product.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);
$update_message = ""; // Variable to store the update message

if (isset($_GET['id'])) {
    $product->id = $_GET['id'];
    $product->readOne();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product->name = $_POST['name'];
    $product->barcode = $_POST['barcode'];
    $product->price = $_POST['price'];

    if ($product->update()) {
        $update_message = "Product was updated.";
    } else {
        $update_message = "Unable to update product.";
    }
}
?>


<<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="container">
        <h1>Edit Product</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $product->id); ?>" method="post">
            <table>
                <tr>
                    <td><label for="name">Product Name:</label></td>
                    <td><input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product->name); ?>" required></td>
                </tr>
                <tr>
                    <td><label for="barcode">Barcode:</label></td>
                    <td><input type="text" id="barcode" name="barcode" value="<?php echo htmlspecialchars($product->barcode); ?>" maxlength="13" pattern="\d{1,13}" title="Barcode must be exactly 13 digits" required></td>
                </tr>
                <tr>
                    <td><label for="price">Price:</label></td>
                    <td><input type="text" id="price" name="price" value="<?php echo htmlspecialchars($product->price); ?>" pattern="\d+(\.\d{1,2})?" title="Please enter a valid price" required></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:left;">
                        <button type="submit">Update</button>
                        <span style="margin-left: 160px; color: green;" class="msg"><?php echo $update_message; ?></span>
                    </td>
                </tr>
            </table>
        </form>
        <a href="productlist.php" class="edit-link">Back to Product List</a>
    </div>
</body>
</html>


