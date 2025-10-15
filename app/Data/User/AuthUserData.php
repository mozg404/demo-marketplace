<?php

namespace App\Data\User;

use App\Support\Image;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

/**
 * Отправляется на фронт глобально
 */
class AuthUserData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public int $balance,
        public Carbon $created_at,
        public Carbon $updated_at,
        public ?UserAvatarData $avatar = null,
        public ?float $seller_rating = null,
        public ?float $positive_feedbacks_count = null,
        public ?float $negative_feedbacks_count = null,
    ) {

    }
}
