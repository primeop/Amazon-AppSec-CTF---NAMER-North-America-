<?php

declare(strict_types=1);

namespace App\Application\Auth;

use App\Domain\User\User;
use App\Domain\User\UserRepository;

class AuthService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Authenticate a user based on employee ID and password
     * 
     * @param string $employeeId
     * @param string $password
     * @return User|null
     */
    public function authenticate(string $employeeId, string $password): ?User
    {
        return $this->userRepository->authenticateUser($employeeId, $password);
    }

    /**
     * Set authentication data in session
     * 
     * @param User $user
     * @return void
     */
    public function setAuth(User $user): void
    {
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['employee_id'] = $user->getEmployeeId();
        $_SESSION['auth'] = true;
    }

    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return isset($_SESSION['auth']) && $_SESSION['auth'] === true;
    }

    /**
     * Get the authenticated user
     * 
     * @return User|null
     */
    public function getAuthenticatedUser(): ?User
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return null;
        }

        try {
            return $this->userRepository->findUserOfId($userId);
        } catch (\Exception $e) {
            $this->clearAuth();
            return null;
        }
    }

    /**
     * Clear authentication data
     * 
     * @return void
     */
    public function clearAuth(): void
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['employee_id']);
        unset($_SESSION['auth']);
    }
} 