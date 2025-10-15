<?php

namespace App\Data\Feedback;

use App\Data\User\UserData;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class FeedbackData extends Data
{
    public function __construct(
        public int $id,
        public bool $is_positive,
        public ?string $comment,
        public UserData $user,
        public Carbon $created_at,
    ) {}
}
