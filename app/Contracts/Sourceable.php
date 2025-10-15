<?php

namespace App\Contracts;

interface Sourceable
{
    public function getSourceableType(): string;
    public function getSourceableId(): int;
}