<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function view(User $user, Product $product): bool
    {
        return $user->is_admin || $user->id === $product->user_id;
    }

    public function update(User $user, Product $order): bool
    {
        return $user->is_admin || $user->id === $order->user_id;
    }
}
