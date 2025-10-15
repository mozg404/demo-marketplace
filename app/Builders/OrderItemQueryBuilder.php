<?php

declare(strict_types=1);

namespace App\Builders;

use App\Collections\OrderItemCollection;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method OrderItemCollection get(array|string $column = ['*'])
 * @method OrderItem|null first($columns = ['*'])
 */
class OrderItemQueryBuilder extends Builder
{
    public function whereSeller(User|int $user): static
    {
        if (is_object($user)) {
            $user = $user->id;
        }

        return $this->whereHas('product', function (ProductQueryBuilder $query) use ($user) {
            return $query->whereSeller($user);
        });
    }

    public function whereBuyer(User|int $user): static
    {
        if (is_object($user)) {
            $user = $user->id;
        }

        return $this->whereHas('order', function (OrderQueryBuilder $query) use ($user) {
            $query->whereUser($user);
        });
    }

    public function whereOrder(Order|int $order): static
    {
        if (is_object($order)) {
            $order = $order->id;
        }

        return $this->where('order_id', $order);
    }

    public function for(Order|User $model): static
    {
        if (is_a($model, Order::class)) {
            return $this->whereOrder($model);
        }

        if (is_a($model, User::class)) {
            return $this->whereBuyer($model);
        }

        return $this;
    }

    public function isCompleted(): static
    {
        return $this->whereHas('order', function (OrderQueryBuilder $query) {
            $query->isCompleted();
        });
    }

    public function isNew(): static
    {
        return $this->whereHas('order', function (OrderQueryBuilder $query) {
            $query->isNew();
        });
    }

    public function withOrder(): static
    {
        return $this->with('order');
    }

    public function withStockItem(): static
    {
        return $this->with('stockItem');
    }

    public function withBuyer(): static
    {
        return $this->with('buyer');
    }

    public function withSeller(): static
    {
        return $this->with('seller');
    }

    public function withProduct(): static
    {
        return $this->with('product');
    }

    public function withProductCategory(): static
    {
        return $this->with('product.category');
    }

    public function withFeedback(): static
    {
        return $this->with('feedback');
    }

    public function doesntHaveFeedback(): static
    {
        return $this->doesntHave('feedback');
    }
}
