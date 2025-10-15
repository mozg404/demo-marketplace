<?php

namespace App\Exceptions\Category;

use Exception;

class CategoryNotFoundException extends Exception
{
    protected $message = 'Категория не найдена';
    protected $code = 404;
}