<?php
require_once 'Database.php';

class Product {
    private $conn;
    private $table_name = "products";

    public $id;
    public $name;
    public $barcode;
    public $price;
    public $created_at;
   

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (name, price, barcode) VALUES (:name, :price, :barcode)";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->barcode = htmlspecialchars(strip_tags($this->barcode));
        

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":barcode", $this->barcode);
        

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
     
    public function getProductByBarcode($barcode) {
        $query = "SELECT id, name, barcode, price FROM " . $this->table_name . " WHERE barcode = :barcode LIMIT 1";

        // Prepare the query
        $stmt = $this->conn->prepare($query);

        // Bind the barcode parameter
        $stmt->bindParam(':barcode', $barcode);

        // Execute the query
        $stmt->execute();

        // Fetch the product data
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the product data
        return $row;
        
    }

    public function readAll($sort = 'barcode', $order = 'ASC', $perPage = 5, $offset = 0) {
        $query = "SELECT * FROM products ORDER BY $sort $order LIMIT :perPage OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->name = $row['name'];
        $this->barcode = $row['barcode'];
    }
    
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
    

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET name=:name, price=:price, barcode=:barcode WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->barcode = htmlspecialchars(strip_tags($this->barcode));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":barcode", $this->barcode);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function search($search, $sort = 'barcode', $order = 'ASC', $perPage = 5, $offset = 0) {
        $query = "SELECT * FROM products WHERE name LIKE :search ORDER BY $sort $order LIMIT :perPage OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':search', "%$search%");
        $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
   
}
?>
