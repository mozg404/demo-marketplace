<?php

namespace App\Exceptions\Auth;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserNotFoundByEmailException extends ModelNotFoundException
{
    protected $message = 'Пользователь не найден';
    protected $code = 404;
}