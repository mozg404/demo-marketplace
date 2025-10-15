<?php

namespace App\Exceptions\Feedback;

use DomainException;

class FeedbackAlreadyExistsException extends DomainException
{
    protected $message = 'Отзыв уже существует';
    protected $code = 409;
}