<?php

namespace App\Exceptions\Auth\EmailVerification;

use DomainException;

class NoPendingEmailVerificationException extends DomainException
{
    protected $message = 'Нет email, ожидающего подтверждение';
    protected $code = 400;
}