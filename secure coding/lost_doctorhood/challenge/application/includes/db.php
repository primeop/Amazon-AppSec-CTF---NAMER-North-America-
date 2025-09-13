<?php

class Database {
    private $db_file;
    public $conn;

    public function __construct() {
        // Define the interdimensional database location
        $this->db_file = __DIR__ . '/../citadel.db';
        //$this->db_file = '/tmp/citadel.db';
    }

    public function connect() {
        try {
            // Establish interdimensional portal connection
            $this->conn = new PDO("sqlite:" . $this->db_file);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Enable Portal Stabilization Mode (WAL) for better interdimensional sync
            $this->conn->exec("PRAGMA journal_mode=WAL;");

            // Initialize the interdimensional registry
            $this->initializeDatabase();
        } catch (PDOException $e) {
            die("Interdimensional portal malfunction: " . $e->getMessage());
        }

        return $this->conn;
    }

    private function initializeDatabase() {
        try {
            // Create the interdimensional registry tables
            $this->conn->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    email TEXT UNIQUE NOT NULL,
                    password TEXT NOT NULL,
                    dimension TEXT DEFAULT 'C-137'
                );

                CREATE TABLE IF NOT EXISTS products (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    size TEXT NOT NULL,
                    dimension TEXT DEFAULT 'C-137'
                );
            ");

            // Insert initial interdimensional data
            $this->insertData();
        } catch (PDOException $e) {
            die("Failed to initialize interdimensional registry: " . $e->getMessage());
        }
    }

    private function insertData() {
        try {
            // Check if the products registry is empty
            $stmt = $this->conn->query("SELECT COUNT(*) FROM products");
            $count = $stmt->fetchColumn();

            if ($count == 0) {
                // Use a quantum-stable transaction
                $this->conn->beginTransaction();

                // Insert interdimensional merchandise
                $products = [
                    ['Portal Gun Replica', 'Standard'],
                    ['Meeseeks Box', 'Pocket'],
                    ['Plumbus', 'Universal'],
                    ['Microverse Battery', 'Compact'],
                    ['Butter Robot', 'Desktop'],
                    ['Interdimensional Cable Box', 'Standard'],
                    ['Pickle Rick T-Shirt', 'Large'],
                    ['Council of Ricks Robe', 'One Size'],
                    ['Szechuan Sauce', 'Travel Size'],
                    ['Mr. Poopybutthole Hat', 'Adjustable'],
                    ['Morty Waves', 'Quantum'],
                    ['Rick\'s Lab Coat', 'XL']
                ];

                foreach ($products as $product) {
                    $stmt = $this->conn->prepare("INSERT INTO products (name, size, dimension) VALUES (:name, :size, :dimension)");
                    $stmt->execute([
                        ':name' => $product[0],
                        ':size' => $product[1],
                        ':dimension' => 'C-137'
                    ]);
                }

                // Insert Council member credentials
                $stmt = $this->conn->prepare(
                    "INSERT INTO users (name, email, password, dimension) VALUES (:name, :email, :password, :dimension)"
                );
                $stmt->execute([
                    ':name' => 'Rick Sanchez',
                    ':email' => 'rick.c137@citadel.gov',
                    ':password' => '$2a$12$DpmHHrR4j8FHZAys.k81auDBGT7ULhrITwdLvIS5deOpM5mOG9qCK',
                    ':dimension' => 'C-137'
                ]);

                // Commit the quantum transaction
                $this->conn->commit();
            }
        } catch (PDOException $e) {
            // Reverse quantum state on failure
            $this->conn->rollBack();
            die("Interdimensional data corruption detected: " . $e->getMessage());
        }
    }
}

// Create a stable interdimensional connection
$db = new Database();
$pdo = $db->connect();
?>