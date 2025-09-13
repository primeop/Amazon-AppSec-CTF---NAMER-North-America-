<?php

declare(strict_types=1);

namespace App\Application\Actions\Dashboard;

use App\Application\Auth\AuthService;
use App\Domain\Review\ReviewRepository;
use App\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class DashboardAction
{
    private Twig $twig;
    private AuthService $authService;
    private UserRepository $userRepository;
    private ReviewRepository $reviewRepository;

    public function __construct(
        Twig $twig, 
        AuthService $authService,
        UserRepository $userRepository,
        ReviewRepository $reviewRepository
    ) {
        $this->twig = $twig;
        $this->authService = $authService;
        $this->userRepository = $userRepository;
        $this->reviewRepository = $reviewRepository;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        // Get authenticated user from session
        $user = $this->authService->getAuthenticatedUser();

        if (!$user) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        // Get user's managers (2-3 levels up)
        $managers = $this->userRepository->findManagerHierarchy($user->getId(), 3);
        
        // Get performance reviews from managers
        $reviews = [];
        foreach ($managers as $manager) {
            $managerReviews = $this->reviewRepository->findReviewsByReviewer(
                $user->getId(), 
                $manager->getId()
            );
            
            if (!empty($managerReviews)) {
                $reviews = array_merge($reviews, $managerReviews);
            }
        }

        // Render dashboard with user data and reviews
        return $this->twig->render($response, 'dashboard.twig', [
            'user' => $user,
            'managers' => $managers,
            'reviews' => $reviews,
        ]);
    }
} 