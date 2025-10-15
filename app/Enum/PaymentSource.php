<?php

namespace App\Enum;

enum PaymentSource: string
{
    case REPLENISHMENT = 'replenishment'; // Ручное пополнение в кабинете
    case ORDER = 'order'; // Оплата при заказе
}
