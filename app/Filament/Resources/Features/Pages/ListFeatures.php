<?php

namespace App\Filament\Resources\Features\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Features\FeatureResource;
use Filament\Resources\Pages\ListRecords;

class ListFeatures extends ListRecords
{
    protected static string $resource = FeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
