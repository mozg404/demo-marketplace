<?php

namespace App\Exceptions\Auth\EmailVerification;

use DomainException;

class EmailAlreadyVerifiedException extends DomainException
{
    protected $message = 'Email уже подтверждён';
    protected $code = 409;
}