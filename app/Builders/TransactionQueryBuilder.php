<?php

declare(strict_types=1);

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class TransactionQueryBuilder extends Builder
{
    public function whereUser(int $id): static
    {
        return $this->where('user_id', $id);
    }

    public function descOrder(): static
    {
        return $this->orderByDesc('id');
    }
}
