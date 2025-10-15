<?php

declare(strict_types=1);

namespace App\Exceptions\User;

use DomainException;

class EmailAlreadyExistsException extends DomainException
{
    protected $message = 'Пользователь с таким email уже существует';
    protected $code = 409;
}
