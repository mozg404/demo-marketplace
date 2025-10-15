<?php

namespace App\Services\Order;

use App\Builders\OrderItemQueryBuilder;
use App\Models\OrderItem;

class OrderItemQuery
{
    public function query(): OrderItemQueryBuilder
    {
        return OrderItem::query();
    }
}