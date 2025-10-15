<?php

namespace App\Exceptions\Auth;

use DomainException;
use Throwable;

class PasswordResetException extends DomainException
{
    protected string $status;

    public function __construct(string $status, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $this->status = $status;

        parent::__construct($message, $code, $previous);
    }
    
    public function getStatus(): string
    {
        return $this->status;
    }
}