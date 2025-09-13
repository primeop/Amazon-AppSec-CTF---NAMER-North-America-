<?php

declare(strict_types=1);

namespace App\Domain\Review;

use JsonSerializable;

class PerformanceReview implements JsonSerializable
{
    private ?int $id;
    
    private int $userId;
    
    private int $reviewerId;
    
    private string $reviewDate;
    
    private string $content;
    
    private int $rating;

    public function __construct(
        ?int $id,
        int $userId,
        int $reviewerId,
        string $reviewDate,
        string $content,
        int $rating
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->reviewerId = $reviewerId;
        $this->reviewDate = $reviewDate;
        $this->content = $content;
        $this->rating = $rating;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getReviewerId(): int
    {
        return $this->reviewerId;
    }

    public function getReviewDate(): string
    {
        return $this->reviewDate;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'reviewerId' => $this->reviewerId,
            'reviewDate' => $this->reviewDate,
            'content' => $this->content,
            'rating' => $this->rating,
        ];
    }
} 