<?php

declare(strict_types=1);

namespace App\Domain\Review;

interface ReviewRepository
{
    /**
     * Find all reviews for a user
     * 
     * @param int $userId
     * @return PerformanceReview[]
     */
    public function findReviewsForUser(int $userId): array;
    
    /**
     * Find reviews by a specific reviewer for a user
     * 
     * @param int $userId
     * @param int $reviewerId
     * @return PerformanceReview[]
     */
    public function findReviewsByReviewer(int $userId, int $reviewerId): array;
    
    /**
     * Get a specific review by ID
     * 
     * @param int $reviewId
     * @return PerformanceReview
     * @throws ReviewNotFoundException
     */
    public function findReviewById(int $reviewId): PerformanceReview;

    /**
     * Find self-review for a user
     * 
     * @param int $userId
     * @return PerformanceReview|null
     */
    public function findSelfReview(int $userId): ?PerformanceReview;
    
    /**
     * Save or update a self-review
     * 
     * @param int $userId
     * @param string $content
     * @param int $rating
     * @return PerformanceReview
     */
    public function saveSelfReview(int $userId, string $content, int $rating): PerformanceReview;
} 