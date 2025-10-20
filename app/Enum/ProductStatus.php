<?php

namespace App\Enum;

enum ProductStatus: string
{
    case DRAFT = 'draft'; // Черновик (не виден в каталоге)
    case ACTIVE = 'active'; // Доступен для продажи
    case PAUSED = 'paused'; // Виден (только не в списках), но продажи приостановлены вручную

    public static function names(): array
    {
        return [
            self::ACTIVE->value => 'Активен',
            self::PAUSED->value => 'На паузе',
            self::DRAFT->value => 'Черновик',
        ];
    }
}
