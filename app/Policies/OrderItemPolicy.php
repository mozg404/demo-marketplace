<?php

namespace App\Policies;

use App\Models\OrderItem;
use App\Models\User;

class OrderItemPolicy
{
    public function view(User $user, OrderItem $orderItem): bool
    {
        return $user->id === $orderItem->order->user_id;
    }
}
