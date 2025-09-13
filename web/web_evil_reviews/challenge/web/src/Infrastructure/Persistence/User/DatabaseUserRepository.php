<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;
use PDO;

class DatabaseUserRepository implements UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @inheritdoc
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM users');
        $users = [];
        
        while ($row = $stmt->fetch()) {
            $users[] = $this->createUserFromRow($row);
        }
        
        return $users;
    }

    /**
     * @inheritdoc
     */
    public function findUserOfId(int $id): User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        
        if (!$row) {
            throw new UserNotFoundException();
        }
        
        return $this->createUserFromRow($row);
    }

    /**
     * @inheritdoc
     */
    public function findUserByEmployeeId(string $employeeId): User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE employee_id = :employee_id');
        $stmt->execute(['employee_id' => $employeeId]);
        $row = $stmt->fetch();
        
        if (!$row) {
            throw new UserNotFoundException();
        }
        
        return $this->createUserFromRow($row);
    }

    /**
     * @inheritdoc
     */
    public function authenticateUser(string $employeeId, string $password): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE employee_id = :employee_id');
        $stmt->execute(['employee_id' => $employeeId]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return null;
        }
        
        // Verify password
        if (password_verify($password, $row['password'])) {
            return $this->createUserFromRow($row);
        }
        
        return null;
    }

    /**
     * @inheritdoc
     */
    public function findManagerHierarchy(int $userId, int $levels = 3): array
    {
        $managers = [];
        $currentUserId = $userId;
        
        // Find user's managers up to specified levels
        for ($i = 0; $i < $levels; $i++) {
            $stmt = $this->pdo->prepare('
                SELECT m.* FROM users u
                JOIN users m ON u.manager_id = m.id
                WHERE u.id = :user_id
            ');
            $stmt->execute(['user_id' => $currentUserId]);
            $row = $stmt->fetch();
            
            if (!$row) {
                break;
            }
            
            $manager = $this->createUserFromRow($row);
            $managers[] = $manager;
            $currentUserId = $manager->getId();
        }
        
        return $managers;
    }

    /**
     * Create a user from a database row
     * 
     * @param array $row
     * @return User
     */
    private function createUserFromRow(array $row): User
    {
        return new User(
            (int) $row['id'],
            $row['employee_id'],
            $row['password'],
            $row['first_name'],
            $row['last_name'],
            $row['position'] ?? null,
            $row['department'] ?? null,
            isset($row['manager_id']) ? (int) $row['manager_id'] : null
        );
    }
} 