<?php

namespace App\Observers;

use App\Models\Feedback;
use App\Services\Rating\RatingService;

class FeedbackObserver
{
    public function __construct(
        protected RatingService $ratingService
    ) {}

    public function created(Feedback $feedback): void
    {
        $this->ratingService->increaseRating($feedback);
    }

    public function updated(Feedback $feedback): void
    {
        if ($feedback->isDirty('is_positive')) {
            $this->ratingService->changeRating($feedback, $feedback->getOriginal('is_positive') === true);
        }
    }

    public function deleted(Feedback $feedback): void
    {
        $this->ratingService->decreaseRating($feedback);
    }
}
