<?php

declare(strict_types=1);

namespace App\Builders;

use App\Enum\StockItemStatus;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method Collection|StockItem[] get(array|string $column = ['*'])
 */
class StockItemQueryBuilder extends Builder
{
    public function isAvailable(): static
    {
        return $this->where('status', StockItemStatus::AVAILABLE->value);
    }

    public function isReserved(): static
    {
        return $this->where('status', StockItemStatus::RESERVED->value);
    }

    public function isActiveProduct(): static
    {
        return $this->whereHas('product', function (ProductQueryBuilder $query) {
            return $query->isActive();
        });
    }

    /**
     * @return $this
     */
    public function canByPurchased(): static
    {
        return $this->isAvailable()->isActiveProduct();
    }

    public function forProduct(int|Product $id): static
    {
        if (is_object($id)) {
            $id = $id->id;
        }

        return $this->where('product_id', $id);
    }

    /**
     * Только позиции, принадлежащие пользователю $user
     */
    public function forUser(int|User $user): static
    {
        if (is_object($user)) {
            $user = $user->id;
        }

        return $this->whereHas('product', function (ProductQueryBuilder $query) use ($user) {
            $query->whereSeller($user);
        });
    }

    /**
     * Только позиции, не принадлежащие пользователю $user
     */
    public function whereNotBelongsToUser(int|User $user): self
    {
        if (is_object($user)) {
            $user = $user->id;
        }

        return $this->whereHas('product', function (ProductQueryBuilder $query) use ($user) {
            $query->whereNotBelongsToUser($user);
        });
    }

    public function withProduct(): self
    {
        return $this->with('product');
    }

    public function withSeller(): self
    {
        return $this->with('seller');
    }
}
