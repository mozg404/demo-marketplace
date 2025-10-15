<?php

namespace App\Services\Order;

use App\Builders\OrderQueryBuilder;
use App\Models\Order;

class OrderQuery
{
    public function query(): OrderQueryBuilder
    {
        return Order::query();
    }
}