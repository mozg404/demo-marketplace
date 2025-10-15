<?php

namespace App\Enum;

enum PaymentStatus: string
{
    case NEW = 'new';               // Статус при создании
    case SUCCESS = 'success';       // Успешная оплата
    case COMPLETED = 'completed';   // Завершен
    case FAILED = 'failed';         // Ошибка
    case CANCELLED = 'cancelled';   // Отменен
}
