<?php

namespace App\Enum;

enum TransactionType: string
{
    case GATEWAY_DEPOSIT = 'gateway_deposit'; // Пополнение через платежный шлюз
    case ORDER_PAYMENT = 'order_payment'; // Оплата заказа
    case SELLER_PAYOUT = 'seller_payout'; // Выплата продавцу
    case ADMIN_CORRECTION = 'admin_correction'; // Корректировка администратором
}