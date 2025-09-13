<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use App\Application\Auth\AuthService;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
//use PDO;
use Slim\Views\Twig;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        
        PDO::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $dbSettings = $settings->get('db');
            
            $dsn = sprintf(
                '%s:host=%s;dbname=%s;charset=%s',
                $dbSettings['driver'],
                $dbSettings['host'],
                $dbSettings['database'],
                $dbSettings['charset']
            );
            
            return new PDO(
                $dsn,
                $dbSettings['username'],
                $dbSettings['password'],
                $dbSettings['flags']
            );
        },
        
        AuthService::class => \DI\autowire(),
        
        Twig::class => function (ContainerInterface $c) {
            $twig = Twig::create(__DIR__ . '/../templates', [
                'cache' => false,
                'debug' => true,
                'auto_reload' => true
            ]);
            
            $twig->addExtension(new \Twig\Extension\DebugExtension());
            
            return $twig;
        },
    ]);
};
