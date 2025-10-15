<?php

namespace App\ValueObjects;

use Webmozart\Assert\Assert;

readonly class Email
{
    public string $value;

    public function __construct(string $value)
    {
        Assert::email($value);
        $this->value = strtolower($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}