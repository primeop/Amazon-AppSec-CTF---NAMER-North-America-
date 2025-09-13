<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use PDO;

class DatabaseSeeder
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function seedDatabase(): void
    {
        $this->createTables();
        $this->seedUsers();
        $this->seedReviews();
    }

    private function createTables(): void
    {
        // Create users table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                employee_id VARCHAR(50) NOT NULL,
                password VARCHAR(255) NOT NULL,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                position VARCHAR(100),
                department VARCHAR(100),
                manager_id INT,
                UNIQUE KEY (employee_id),
                FOREIGN KEY (manager_id) REFERENCES users(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // Create performance_reviews table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS performance_reviews (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                reviewer_id INT NOT NULL,
                review_date DATE NOT NULL,
                content TEXT NOT NULL,
                rating INT NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (reviewer_id) REFERENCES users(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    private function seedUsers(): void
    {
        // Only seed if the table is empty
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM users");
        if ((int) $stmt->fetchColumn() > 0) {
            return;
        }

        // Create executive leadership
        $this->createUser(
            'phillip_price', 
            'exec_secure123', 
            'Phillip', 
            'Price', 
            'CEO', 
            'Executive', 
            null
        );

        $this->createUser(
            'tyrell_wellick', 
            'svp_secure456', 
            'Tyrell', 
            'Wellick', 
            'CTO', 
            'Technology', 
            1
        );

        $this->createUser(
            'angela_moss', 
            'dir_secure789', 
            'Angela', 
            'Moss', 
            'Director', 
            'Technology', 
            2
        );

        // Create elliot account (the requested one)
        $this->createUser(
            'elliot', 
            'system1', 
            'Elliot', 
            'Alderson', 
            'Security Engineer', 
            'Information Security', 
            3
        );
    }

    private function seedReviews(): void
    {
        // Only seed if the table is empty
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM performance_reviews");
        if ((int) $stmt->fetchColumn() > 0) {
            return;
        }

        // Performance review from Angela (direct manager)
        $this->createReview(
            4, // Elliot
            3, // Angela
            '2023-12-15',
            "Elliot has shown exceptional technical skills and attention to detail. His commitment to security protocols is exemplary. However, there are concerns about his communication skills and occasional unexcused absences. He needs to work on team collaboration and keeping management informed of his activities.\n\nStrengths:\n- Outstanding technical knowledge\n- Identifies vulnerabilities others miss\n- Solves complex problems efficiently\n\nAreas for improvement:\n- Communication with team members\n- Following standard reporting procedures\n- Attendance and punctuality",
            4 // Rating out of 5
        );

        // Performance review from Tyrell (second level manager)
        $this->createReview(
            4, // Elliot
            2, // Tyrell
            '2023-12-20',
            "While Elliot's technical capabilities are undeniable, I have serious concerns about his adherence to company policies and chain of command. His recent security audit of the Steel Mountain facility, while thorough, was conducted without proper authorization. His behavior sometimes exhibits a concerning pattern that could potentially compromise our security protocols.\n\nI recommend close supervision and possible psychological evaluation before granting higher security clearances. Despite these concerns, his technical contributions remain valuable to Evil Core's mission.",
            3 // Rating out of 5
        );

        // Performance review from Phillip Price (CEO, third level manager)
        $this->createReview(
            4, // Elliot
            1, // Phillip
            '2023-12-30',
            "I've been monitoring Mr. Alderson's activities with great interest. His technical brilliance is matched only by his unpredictability, which makes him both an asset and a liability. His recent discovery of vulnerabilities in our Global Debt Ledger security deserves recognition, but his methods are concerning.\n\nEmployees like Elliot require special handling - too much pressure will break them, too little oversight and they become dangerous. I'm authorizing continued employment with restricted access to critical systems. His potential usefulness outweighs the risk, for now.\n\nWATCH CLOSELY. If his behavior becomes more erratic, immediate termination may be necessary.",
            2 // Rating out of 5
        );
    }

    private function createUser(
        string $employeeId, 
        string $password, 
        string $firstName, 
        string $lastName, 
        ?string $position = null, 
        ?string $department = null, 
        ?int $managerId = null
    ): int {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare("
            INSERT INTO users (employee_id, password, first_name, last_name, position, department, manager_id)
            VALUES (:employee_id, :password, :first_name, :last_name, :position, :department, :manager_id)
        ");
        
        $stmt->execute([
            'employee_id' => $employeeId,
            'password' => $hashedPassword,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'position' => $position,
            'department' => $department,
            'manager_id' => $managerId
        ]);
        
        return (int) $this->pdo->lastInsertId();
    }

    private function createReview(
        int $userId, 
        int $reviewerId, 
        string $reviewDate, 
        string $content, 
        int $rating
    ): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO performance_reviews (user_id, reviewer_id, review_date, content, rating)
            VALUES (:user_id, :reviewer_id, :review_date, :content, :rating)
        ");
        
        $stmt->execute([
            'user_id' => $userId,
            'reviewer_id' => $reviewerId,
            'review_date' => $reviewDate,
            'content' => $content,
            'rating' => $rating
        ]);
    }
} 