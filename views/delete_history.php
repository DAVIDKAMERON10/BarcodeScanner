<?php
// Include necessary files for the database connection and classes
require_once 'C:/xampp/htdocs/BarcodeScanner/classes/Database.php';
require_once 'C:/xampp/htdocs/BarcodeScanner/classes/Product_History.php';

// Create a database connection
$database = new Database();
$db = $database->getConnection();

// Create ProductHistory object
$productHistory = new ProductHistory($db);

// Initialize message variables
$delete_message = '';
$delete_status = '';

// Check if an id is passed in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Call the delete method to remove the history entry
    if ($productHistory->delete($id)) {
        $delete_message = "History entry deleted successfully!";
        $delete_status = "success";
    } else {
        $delete_message = "Error deleting the history entry.";
        $delete_status = "error";
    }
} else {
    $delete_message = "No history entry selected for deletion.";
    $delete_status = "error";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete History Entry</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .container {
            text-align: center;
            margin-top: 50px;
        }
        .message {
            padding: 20px;
            border: 1px solid #000;
            display: inline-block;
            margin-bottom: 20px;
        }
        .message.success {
            border-color: green;
            color: green;
        }
        .message.error {
            border-color: red;
            color: red;
        }
        .navigation a {
            margin: 10px;
            display: inline-block;
            padding: 8px 20px;
            background-color: #f2f2f2;
            text-decoration: none;
            border: 1px solid #000;
            color: #000;
        }
        .navigation a:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Delete History Entry</h1>
        <div class="message <?php echo $delete_status; ?>">
            <?php echo $delete_message; ?>
        </div>
        <div class="navigation">
            <a href="history.php">Back to History</a>
        </div>
    </div>
</body>
</html>
