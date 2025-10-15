<?php

declare(strict_types=1);

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 */
class FeedbackQueryBuilder extends Builder
{
    public function hasComments(): static
    {
        return $this->where('comment', '!=', null);
    }

    public function withUser(): static
    {
        return $this->with('user');
    }
}
