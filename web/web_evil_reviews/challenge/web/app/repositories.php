<?php

declare(strict_types=1);

use App\Domain\User\UserRepository;
use App\Domain\Review\ReviewRepository;
use App\Infrastructure\Persistence\User\DatabaseUserRepository;
use App\Infrastructure\Persistence\Review\DatabaseReviewRepository;
use DI\ContainerBuilder;


return function (ContainerBuilder $containerBuilder) {
    // Here we map our repositories to their implementations
    $containerBuilder->addDefinitions([
        UserRepository::class => \DI\autowire(DatabaseUserRepository::class),
        ReviewRepository::class => \DI\autowire(DatabaseReviewRepository::class),
    ]);
};
