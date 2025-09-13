<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use App\Console\SeedDatabaseCommand;
use DI\ContainerBuilder;
//use PDO;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate container builder
$containerBuilder = new ContainerBuilder();

// Add settings
$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);

// Add dependencies
$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($containerBuilder);

// Build container
$container = $containerBuilder->build();

// Run seeder
$pdo = $container->get(PDO::class);
$seeder = new SeedDatabaseCommand($pdo);
$seeder->execute(); 