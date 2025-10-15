<?php

namespace App\Exceptions\Auth\EmailVerification;

use DomainException;

class EmailNotVerifiedException extends DomainException
{
    protected $message = 'Email не подтверждён';
    protected $code = 403;
}