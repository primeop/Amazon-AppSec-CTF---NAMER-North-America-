<?php

declare(strict_types=1);

use App\Application\Actions\Auth\LoginAction;
use App\Application\Actions\Auth\LogoutAction;
use App\Application\Actions\Dashboard\DashboardAction;
// use App\Application\Actions\User\ListUsersAction;
// use App\Application\Actions\User\ViewUserAction;
use App\Application\Actions\SelfReview\ViewSelfReviewAction;
use App\Application\Actions\SelfReview\EditSelfReviewAction;
use App\Application\Actions\SelfReview\UpdateSelfReviewAction;
use App\Application\Middleware\AuthMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    // Login route
    $app->any('/', LoginAction::class)->setName('login');
    
    // Auth protected routes
    $app->group('', function (Group $group) {
        // Dashboard route
        $group->get('/dashboard', DashboardAction::class);
        
        // Self-review routes
        $group->get('/self-review', ViewSelfReviewAction::class);
        $group->get('/self-review/edit', EditSelfReviewAction::class);
        $group->post('/self-review/update', UpdateSelfReviewAction::class);

        // Logout route
        $group->get('/logout', LogoutAction::class);
    })->add(AuthMiddleware::class);
};
