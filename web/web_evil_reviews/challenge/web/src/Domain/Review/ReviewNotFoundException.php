<?php

declare(strict_types=1);

namespace App\Domain\Review;

use App\Domain\DomainException\DomainRecordNotFoundException;

class ReviewNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The review you requested does not exist.';
} 