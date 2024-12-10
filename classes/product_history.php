<?php
require_once 'Database.php';


class ProductHistory {
    private $conn;
    private $table_name = "product_history";

    public $id;
    public $product_id;
    public $action;
    public $action_time;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET product_id=:product_id, action=:action";
        $stmt = $this->conn->prepare($query);

        $this->product_id=htmlspecialchars(strip_tags($this->product_id));
        $this->action=htmlspecialchars(strip_tags($this->action));

        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":action", $this->action);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY action_time DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
    
        // Bind the ID to the placeholder
        $stmt->bindParam(':id', $id);
    
        // Check if the delete query is successful
        if ($stmt->execute()) {
            return true;
        } else {
            // If there is an issue, show error
            echo "Error: " . $stmt->errorInfo()[2];
            return false;
        }
    }
    public function deleteByProductId($product_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $product_id = htmlspecialchars(strip_tags($product_id));

        // Bind the product_id parameter
        $stmt->bindParam(":product_id", $product_id);

        // Execute query and return true if successful
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
    
    public function getHistory() {
        $query = "SELECT ph.id, p.name, p.barcode, ph.scanned_at
                  FROM product_history ph
                  JOIN products p ON ph.product_id = p.id
                  ORDER BY ph.scanned_at DESC";
        
        try {
            // Prepare the query
            $stmt = $this->conn->prepare($query);

            // Execute the query
            $stmt->execute();

            // Return the statement object for further processing
            return $stmt;
        } catch (Exception $e) {
            echo "Error executing query: " . $e->getMessage();
            return null;
        }
    }
    public function insertScan($product_id, $barcode, $product_name) {
        $query = "INSERT INTO product_history (product_id, barcode, product_name, scanned_at) VALUES (:product_id, :barcode, :product_name, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':barcode', $barcode);
        $stmt->bindParam(':product_name', $product_name);


        // If insertion is successful, return true, else false
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    
}
?>
