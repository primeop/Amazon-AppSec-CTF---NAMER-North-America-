<?php

declare(strict_types=1);

namespace App\Console;

use App\Infrastructure\Persistence\DatabaseSeeder;
use PDO;

class SeedDatabaseCommand
{
    private DatabaseSeeder $databaseSeeder;

    public function __construct(PDO $pdo)
    {
        $this->databaseSeeder = new DatabaseSeeder($pdo);
    }

    public function execute(): void
    {
        echo "Seeding database...\n";
        $this->databaseSeeder->seedDatabase();
        echo "Database seeded successfully!\n";
    }
} 