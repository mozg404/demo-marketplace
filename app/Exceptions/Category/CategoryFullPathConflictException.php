<?php

namespace App\Exceptions\Category;

use Exception;

class CategoryFullPathConflictException extends Exception
{
    protected $message = 'Category path conflict';
    protected $code = 422;
}