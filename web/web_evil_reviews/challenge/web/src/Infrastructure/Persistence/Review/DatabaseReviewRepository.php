<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Review;

use App\Domain\Review\PerformanceReview;
use App\Domain\Review\ReviewNotFoundException;
use App\Domain\Review\ReviewRepository;
use PDO;

class DatabaseReviewRepository implements ReviewRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @inheritdoc
     */
    public function findReviewsForUser(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM performance_reviews WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        $reviews = [];
        
        while ($row = $stmt->fetch()) {
            $reviews[] = $this->createReviewFromRow($row);
        }
        
        return $reviews;
    }

    /**
     * @inheritdoc
     */
    public function findReviewsByReviewer(int $userId, int $reviewerId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM performance_reviews 
            WHERE user_id = :user_id AND reviewer_id = :reviewer_id
        ');
        $stmt->execute([
            'user_id' => $userId,
            'reviewer_id' => $reviewerId
        ]);
        $reviews = [];
        
        while ($row = $stmt->fetch()) {
            $reviews[] = $this->createReviewFromRow($row);
        }
        
        return $reviews;
    }

    /**
     * @inheritdoc
     */
    public function findReviewById(int $reviewId): PerformanceReview
    {
        $stmt = $this->pdo->prepare('SELECT * FROM performance_reviews WHERE id = :id');
        $stmt->execute(['id' => $reviewId]);
        $row = $stmt->fetch();
        
        if (!$row) {
            throw new ReviewNotFoundException();
        }
        
        return $this->createReviewFromRow($row);
    }

    /**
     * @inheritdoc
     */
    public function findSelfReview(int $userId): ?PerformanceReview
    {
        $stmt = $this->pdo->prepare('SELECT * FROM performance_reviews WHERE user_id = :user_id AND reviewer_id = :reviewer_id');
        $stmt->execute([
            'user_id' => $userId,
            'reviewer_id' => $userId // Self-review has the same user_id and reviewer_id
        ]);
        
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        
        return $this->createReviewFromRow($row);
    }

    /**
     * @inheritdoc
     */
    public function saveSelfReview(int $userId, string $content, int $rating): PerformanceReview
    {
        // Check if self-review already exists
        $existingReview = $this->findSelfReview($userId);
        
        if ($existingReview) {
            // Update existing review
            $stmt = $this->pdo->prepare('
                UPDATE performance_reviews 
                SET content = :content, rating = :rating, review_date = :review_date
                WHERE id = :id
            ');
            
            $stmt->execute([
                'id' => $existingReview->getId(),
                'content' => $content,
                'rating' => $rating,
                'review_date' => date('Y-m-d')
            ]);
            
            return new PerformanceReview(
                $existingReview->getId(),
                $userId,
                $userId,
                date('Y-m-d'),
                $content,
                $rating
            );
        } else {
            // Create new self-review
            $stmt = $this->pdo->prepare('
                INSERT INTO performance_reviews (user_id, reviewer_id, review_date, content, rating)
                VALUES (:user_id, :reviewer_id, :review_date, :content, :rating)
            ');
            
            $stmt->execute([
                'user_id' => $userId,
                'reviewer_id' => $userId, // Self-review has the same user_id and reviewer_id
                'review_date' => date('Y-m-d'),
                'content' => $content,
                'rating' => $rating
            ]);
            
            $id = (int) $this->pdo->lastInsertId();
            
            return new PerformanceReview(
                $id,
                $userId,
                $userId,
                date('Y-m-d'),
                $content,
                $rating
            );
        }
    }

    /**
     * Create a performance review from a database row
     * 
     * @param array $row
     * @return PerformanceReview
     */
    private function createReviewFromRow(array $row): PerformanceReview
    {
        return new PerformanceReview(
            (int) $row['id'],
            (int) $row['user_id'],
            (int) $row['reviewer_id'],
            $row['review_date'],
            $row['content'],
            (int) $row['rating']
        );
    }
} 