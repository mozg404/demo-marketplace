<?php

namespace App\Events;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class OrderCreatedFromCart
{
    use Dispatchable;

    public function __construct(
        public Order $order
    ) {}
}