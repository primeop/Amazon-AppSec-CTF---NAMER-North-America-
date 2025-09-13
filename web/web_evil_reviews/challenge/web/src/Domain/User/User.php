<?php

declare(strict_types=1);

namespace App\Domain\User;

use JsonSerializable;

class User implements JsonSerializable
{
    private ?int $id;

    private string $employeeId;

    private string $password;

    private string $firstName;

    private string $lastName;

    private ?string $position;

    private ?string $department;

    private ?int $managerId;

    public function __construct(
        ?int $id, 
        string $employeeId, 
        string $password, 
        string $firstName, 
        string $lastName, 
        ?string $position = null,
        ?string $department = null,
        ?int $managerId = null
    ) {
        $this->id = $id;
        $this->employeeId = $employeeId;
        $this->password = $password;
        $this->firstName = ucfirst($firstName);
        $this->lastName = ucfirst($lastName);
        $this->position = $position;
        $this->department = $department;
        $this->managerId = $managerId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployeeId(): string
    {
        return $this->employeeId;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function getManagerId(): ?int
    {
        return $this->managerId;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'employeeId' => $this->employeeId,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'position' => $this->position,
            'department' => $this->department,
            'managerId' => $this->managerId,
        ];
    }
}
