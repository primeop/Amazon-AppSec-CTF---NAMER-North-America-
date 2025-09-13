<?php

declare(strict_types=1);

use Slim\App;

return function (App $app) {
    // Parse json, form data and xml
    $app->addBodyParsingMiddleware();

    // Start session
    $app->add(function ($request, $handler) {
        session_start();
        return $handler->handle($request);
    });

    // Add Routing Middleware
    $app->addRoutingMiddleware();
};
