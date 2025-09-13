<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UserRepository
{
    /**
     * @return User[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return User
     * @throws UserNotFoundException
     */
    public function findUserOfId(int $id): User;

    /**
     * @param string $employeeId
     * @return User
     * @throws UserNotFoundException
     */
    public function findUserByEmployeeId(string $employeeId): User;

    /**
     * @param string $employeeId
     * @param string $password
     * @return User|null
     */
    public function authenticateUser(string $employeeId, string $password): ?User;

    /**
     * Get user's manager, and higher level managers
     * 
     * @param int $userId
     * @param int $levels How many levels up to retrieve
     * @return User[]
     */
    public function findManagerHierarchy(int $userId, int $levels = 3): array;
}
