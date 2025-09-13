<?php

declare(strict_types=1);

namespace App\Application\Actions\SelfReview;

use App\Application\Auth\AuthService;
use App\Domain\Review\ReviewRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class EditSelfReviewAction
{
    private Twig $twig;
    private AuthService $authService;
    private ReviewRepository $reviewRepository;

    public function __construct(
        Twig $twig,
        AuthService $authService,
        ReviewRepository $reviewRepository
    ) {
        $this->twig = $twig;
        $this->authService = $authService;
        $this->reviewRepository = $reviewRepository;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        // Get authenticated user
        $user = $this->authService->getAuthenticatedUser();
        
        if (!$user) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }
        
        // Get user's self review
        $selfReview = $this->reviewRepository->findSelfReview($user->getId());
        
        // Render the edit form
        return $this->twig->render($response, 'self-review-edit.twig', [
            'user' => $user,
            'selfReview' => $selfReview
        ]);
    }
} 