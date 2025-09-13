<?php
const DB_FILE = __DIR__ . '/database.db';

function createDatabase() {
    // Check if the database file exists
    if (!file_exists(DB_FILE)) {
        try {
            $pdo = new PDO('sqlite:' . DB_FILE);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Create tables
            $createTablesQuery = "
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL
            );

            CREATE TABLE ricks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                rick_id VARCHAR(255) NOT NULL,
                rating INTEGER NOT NULL,
                description VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            ";

            // Add the default data
            $pdo->exec($createTablesQuery);

            $defaultDataQuery = "
                INSERT INTO users (username, password) 
                VALUES 
                ('rick', '$2y$10\$G8I4RxYIj0BuLLTmf5Lm2OgV0dCEZpXvPXAc1ISB7Zf9/d1WJmoim'),
                ('morty', '$2y$10\$Ots0zPBcm8ucn5vvZsKB9uUtzUJF1lzMUn9IbZg0PB0ETRQOD/0eK');

                INSERT INTO ricks (rick_id, rating, description) 
                VALUES 
                ('Rick C-137', 5, 'The original Rick.'),
                ('Doofus Rick', 2, 'Not the smartest Rick.');
            ";

            // Execute the query
            $pdo->exec($defaultDataQuery);
        } catch (PDOException $e) {
            die("Error creating database: " . $e->getMessage());
        }
    }
}

createDatabase();

try {
    $pdo = new PDO('sqlite:' . DB_FILE);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>