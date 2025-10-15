<?php

namespace App\Services\Order;

use App\Exceptions\Order\OrderAccessDeniedException;
use App\Exceptions\Order\OrderIsNotCompletedException;
use App\Models\Order;
use App\Models\User;

class OrderChecker
{
    public function ensureOrderAccess(Order $order, User $user): void
    {
        if ($order->user_id !== $user->id) {
            throw new OrderAccessDeniedException();
        }
    }

    public function ensureCompletedOrder(Order $order): void
    {
        if (!$order->isCompleted()) {
            throw new OrderIsNotCompletedException();
        }
    }
}