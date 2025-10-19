<?php

declare(strict_types=1);

namespace App\Builders;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserQueryBuilder extends Builder
{
    public function findByEmail(string $email): ?User
    {
        return $this->where('email', $email)->first();
    }

    public function withAvailableProductsCount(): self
    {
        return $this->withCount(['products as available_products_count' => function (ProductQueryBuilder $builder) {
            return $builder->isAvailable();
        }]);
    }

    public function hasAvailableProducts(): self
    {
        return $this->whereHas('products', function (ProductQueryBuilder $builder) {
            return $builder->isAvailable();
        });
    }

    public function withoutAdmin(): self
    {
        return $this->where('is_admin', 0);
    }

    public function withMedia(): self
    {
        return $this->with('media');
    }
}
