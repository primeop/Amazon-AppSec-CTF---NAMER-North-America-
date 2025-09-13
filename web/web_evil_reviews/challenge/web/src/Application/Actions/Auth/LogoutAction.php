<?php

declare(strict_types=1);

namespace App\Application\Actions\Auth;

use App\Application\Auth\AuthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LogoutAction
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        // Clear session
        $this->authService->clearAuth();
        
        // Redirect to login
        return $response->withHeader('Location', '/')->withStatus(302);
    }
} 