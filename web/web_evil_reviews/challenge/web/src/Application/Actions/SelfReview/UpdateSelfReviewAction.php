<?php

declare(strict_types=1);

namespace App\Application\Actions\SelfReview;

use App\Application\Auth\AuthService;
use App\Domain\Review\ReviewRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class UpdateSelfReviewAction
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
        
        // Process form submission
        $data = $request->getParsedBody();
        $content = $data['content'] ?? '';
        $rating = (int) ($data['rating'] ?? 3);
        
        // Validate rating
        if ($rating < 1) $rating = 1;
        if ($rating > 5) $rating = 5;
        
        // Save self-review
        $selfReview = $this->reviewRepository->saveSelfReview(
            $user->getId(),
            $content,
            $rating
        );
        
        // Redirect to view page
        return $response->withHeader('Location', '/self-review')->withStatus(302);
    }
} 