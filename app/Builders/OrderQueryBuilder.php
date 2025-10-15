<?php

declare(strict_types=1);

namespace App\Builders;

use App\Enum\OrderStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method Collection get(array|string $column = ['*'])
 * @method OrderQueryBuilder whereUserId(int $id)
 */
class OrderQueryBuilder extends Builder
{
    public function whereUser(int $id): static
    {
        return $this->where('user_id', $id);
    }

    public function isCompleted(): static
    {
        return $this->where('status', OrderStatus::COMPLETED);
    }

    public function isNew(): static
    {
        return $this->where('status', OrderStatus::PENDING);
    }

    public function descOrder(): static
    {
        return $this->orderByDesc('id');
    }

    public function withUser(): static
    {
        return $this->with('user');
    }

    public function withItems(): static
    {
        return $this->with('items');
    }

    public function withItemsCount(): static
    {
        return $this->withCount('items');
    }

    public function withStockItems(): static
    {
        return $this->with('items.stockItem');
    }

    public function withProducts(): static
    {
        return $this->with('items.stockItem.product');
    }

    public function withProductSellers()
    {
        return $this->with('items.stockItem.product.user');
    }
}
