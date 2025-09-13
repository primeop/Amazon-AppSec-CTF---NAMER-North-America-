<?php

declare(strict_types=1);

namespace App\Application\Actions\Auth;

use App\Application\Auth\AuthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class LoginAction
{
    private Twig $twig;
    private AuthService $authService;

    public function __construct(Twig $twig, AuthService $authService)
    {
        $this->twig = $twig;
        $this->authService = $authService;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        // If already logged in, redirect to dashboard
        if ($this->authService->isAuthenticated()) {
            return $response->withHeader('Location', '/dashboard')->withStatus(302);
        }

        // Handle login form submission
        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $employeeId = $data['employee_id'] ?? '';
            $password = $data['password'] ?? '';
            
            // Authenticate user
            $user = $this->authService->authenticate($employeeId, $password);
            
            if ($user) {
                // Set auth session
                $this->authService->setAuth($user);
                
                // Redirect to dashboard
                return $response->withHeader('Location', '/dashboard')->withStatus(302);
            }
            
            // If we get here, authentication failed
            return $this->twig->render($response, 'login.twig', [
                'error' => 'Invalid employee ID or password',
                'employee_id' => $employeeId,
            ]);
        }
        
        // Display login form
        return $this->twig->render($response, 'login.twig');
    }
} 