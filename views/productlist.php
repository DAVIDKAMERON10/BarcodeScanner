<?php
session_start();
require_once 'C:/xampp/htdocs/BarcodeScanner/classes/database.php';  // Database class
require_once 'C:/xampp/htdocs/BarcodeScanner/classes/product.php'; 

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);

$resultsFound = true; // Flag to indicate if results are found

// Get sorting parameters from the URL
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'barcode'; // Default sort by barcode
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC'; // Default order is ascending

// Validate the sort column
$valid_columns = ['barcode', 'price', 'name'];
if (!in_array($sort, $valid_columns)) {
    $sort = 'barcode'; // Default to sorting by barcode if invalid
}

// Validate the order (ASC or DESC)
$order = ($order == 'ASC') ? 'ASC' : 'DESC'; // Default to ASC if invalid

// Determine the current page number
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 5; // Number of products per page
$offset = ($page - 1) * $perPage;

// Fetch data based on search or display all
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $stmt = $product->search($search, $sort, $order, $perPage, $offset);
    if ($stmt->rowCount() == 0) {
        $resultsFound = false; // No results found
    }
} else {
    $stmt = $product->readAll($sort, $order, $perPage, $offset); // Pass sorting and pagination parameters to the readAll method
}

// Calculate the total number of products for pagination
$totalProducts = $product->countAll();
$totalPages = ceil($totalProducts / $perPage);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Product List</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .container {
            text-align: center;
        }
        table {
            margin: 0 auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #000;
            text-align: center;
        }
        .navigation, .search-sort {
            margin-bottom: 20px;
        }
        .dropdown {
            margin-bottom: 20px;
        }
        .pagination {
            margin-top: 20px;
        }
        .pagination a {
            padding: 8px 16px;
            margin: 0 5px;
            text-decoration: none;
            background-color: #008CBA;
            color: white;
            border-radius: 5px;
        }
        .pagination a.disabled {
            background-color: #ccc;
            pointer-events: none;
        }
        .actions a {
            margin: 0 5px;
            padding: 5px 10px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            background-color: #008CBA;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Product List</h1>

        <!-- Navigation Links Table -->
        <table class="navigation">
            <tr>
                <td><a href="add_product.php">Add Product</a></td>
                <td><a href="index.php">Scan Product</a></td>
                <td><a href="history.php">View History</a></td>
            </tr>
        </table>

        <!-- Search Form -->
        <form method="get" action="productlist.php">
            <input type="text" name="search" placeholder="Search products..." required>
            <button type="submit" class="btn-search">Search</button>
        </form>

        <!-- Sorting Dropdown -->
        <div class="dropdown">
            <form method="get" action="productlist.php">
                <label for="sort">Sort by:</label>
                <select name="sort" id="sort">
                    <option value="barcode" <?php echo ($sort == 'barcode') ? 'selected' : ''; ?>>Barcode</option>
                    <option value="price" <?php echo ($sort == 'price') ? 'selected' : ''; ?>>Price</option>
                    <option value="name" <?php echo ($sort == 'name') ? 'selected' : ''; ?>>Product Name (Alphabetical)</option>
                </select>
                <label for="order">Order:</label>
                <select name="order" id="order">
                    <?php if ($sort == 'barcode'): ?>
                        <option value="ASC" <?php echo ($order == 'ASC') ? 'selected' : ''; ?>>Ascending (0-9)</option>
                        <option value="DESC" <?php echo ($order == 'DESC') ? 'selected' : ''; ?>>Descending (9-0)</option>
                    <?php elseif ($sort == 'name'): ?>
                        <option value="ASC" <?php echo ($order == 'ASC') ? 'selected' : ''; ?>>Ascending (A-Z)</option>
                        <option value="DESC" <?php echo ($order == 'DESC') ? 'selected' : ''; ?>>Descending (Z-A)</option>
                    <?php else: ?>
                        <option value="ASC" <?php echo ($order == 'ASC') ? 'selected' : ''; ?>>Ascending (Low to High)</option>
                        <option value="DESC" <?php echo ($order == 'DESC') ? 'selected' : ''; ?>>Descending (High to Low)</option>
                    <?php endif; ?>
                </select>
                <button type="submit" class="btn-sort">Sort</button>
            </form>
        </div>

        <!-- Product List Table -->
        <table class="product-list">
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Barcode</th>
                <th>Actions</th>
            </tr>
            <?php if ($resultsFound): ?>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                        <td><?php echo htmlspecialchars($row['barcode']); ?></td>
                        <td>
                            <div class="actions">
                                <a href="edit_product.php?id=<?php echo $row['id']; ?>">Edit</a>
                                <a href="delete_product.php?id=<?php echo $row['id']; ?>">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No products found for the search term "<?php echo htmlspecialchars($search); ?>"</td>
                </tr>
            <?php endif; ?>
        </table>

        <!-- Pagination Links -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?><?php echo isset($search) ? '&search=' . htmlspecialchars($search) : ''; ?>">Previous</a>
            <?php else: ?>
                <a class="disabled">Previous</a>
            <?php endif; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?><?php echo isset($search) ? '&search=' . htmlspecialchars($search) : ''; ?>">Next</a>
            <?php else: ?>
                <a class="disabled">Next</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
