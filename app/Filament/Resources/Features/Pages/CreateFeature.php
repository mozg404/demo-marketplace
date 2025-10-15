<?php

namespace App\Filament\Resources\Features\Pages;

use App\Filament\Resources\Features\FeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFeature extends CreateRecord
{
    protected static string $resource = FeatureResource::class;
}
