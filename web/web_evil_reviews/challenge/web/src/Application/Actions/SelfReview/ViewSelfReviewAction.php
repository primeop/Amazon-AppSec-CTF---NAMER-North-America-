<?php

declare(strict_types=1);

namespace App\Application\Actions\SelfReview;

use App\Application\Auth\AuthService;
use App\Domain\Review\ReviewRepository;
use App\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class ViewSelfReviewAction
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
        
        // Check if we need to seed a self-review for elliot
        if (!$selfReview && $user->getEmployeeId() === 'elliot') {
            // Create a default self-review for Elliot
            $content = "I believe my technical skills are strong, but I'm aware that my communication with the team could use improvement. I often work independently and sometimes forget to document my findings or inform management about security issues I discover. I've been working on several side projects to improve our security infrastructure, though some of these haven't been formally approved. My attendance could be better - sometimes I get caught up in work and lose track of time. I'm committed to improving in these areas while continuing to strengthen our security posture.";
            
            $selfReview = $this->reviewRepository->saveSelfReview(
                $user->getId(),
                $content,
                3 // Rating out of 5
            );
        }
        
        // Render the self-review view
        return $this->twig->render($response, 'self-review.twig', [
            'user' => $user,
            'selfReview' => $selfReview,
            'showForm' => true
        ]);
    }
} 