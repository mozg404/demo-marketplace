<?php

namespace App\Exceptions\Auth;

use DomainException;

class InvalidPasswordException extends DomainException
{
    protected $message = 'Неверный пароль';
}