<?php

namespace App\Enum;

enum ProductGroup: string
{
    case Games = 'games';
    case Subscriptions = 'subscriptions';
    case Certificates = 'certificates';

    public function getCategoryPath(): string
    {
        return match ($this) {
            self::Games => 'keys/games',
            self::Subscriptions => 'subscriptions',
            self::Certificates => 'certificates',
        };
    }
}