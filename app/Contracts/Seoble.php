<?php

namespace App\Contracts;

use App\Support\SeoBuilder;

interface Seoble
{
    public function seo(): SeoBuilder;
}