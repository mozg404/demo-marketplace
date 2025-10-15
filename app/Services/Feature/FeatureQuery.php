<?php

namespace App\Services\Feature;

use App\Builders\FeatureQueryBuilder;
use App\Models\Feature;

class FeatureQuery
{
    public function query(): FeatureQueryBuilder
    {
        return Feature::query();
    }
}