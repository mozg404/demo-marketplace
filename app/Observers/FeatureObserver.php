<?php

namespace App\Observers;

use App\Models\Feature;
use App\Services\Feature\FeatureQuery;

readonly class FeatureObserver
{
    public function __construct(
        private FeatureQuery $featureQuery,
    ) {
    }

    public function created(Feature $feature): void
    {
        $this->featureQuery->cacheClear();
    }

    public function updated(Feature $feature): void
    {
        $this->featureQuery->cacheClear();
    }

    public function deleted(Feature $feature): void
    {
        $this->featureQuery->cacheClear();
    }
}
