<?php

namespace App\Exceptions\Auth;

use DomainException;

class AuthenticationFailedException extends DomainException
{
    protected $message = 'Неверный логин или пароль';
    protected $code = 401;
}