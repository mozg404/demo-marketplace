<?php

namespace App\Data\User;

use App\Support\Image;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?UserAvatarData $avatar = null,
        public Carbon $created_at,
        public float $seller_rating,
        public int $positive_feedbacks_count,
        public int $negative_feedbacks_count,
    ) {
    }
}
