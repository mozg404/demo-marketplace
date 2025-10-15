<?php

namespace App\Enum;

enum StockItemStatus: string
{
    case AVAILABLE = 'available';
    case RESERVED = 'reserved';
}