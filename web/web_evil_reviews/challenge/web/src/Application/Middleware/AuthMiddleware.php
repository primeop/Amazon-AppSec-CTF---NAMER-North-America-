<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Application\Auth\AuthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Routing\RouteContext;

class AuthMiddleware implements MiddlewareInterface
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        if (!$this->authService->isAuthenticated()) {
            $responseFactory = new ResponseFactory();
            $response = $responseFactory->createResponse();
            
            $routeContext = RouteContext::fromRequest($request);
            $routeParser = $routeContext->getRouteParser();
            
            return $response
                ->withHeader('Location', $routeParser->urlFor('login'))
                ->withStatus(302);
        }

        // Add the current user to the request attributes
        $user = $this->authService->getAuthenticatedUser();
        $request = $request->withAttribute('user', $user);

        return $handler->handle($request);
    }
} 