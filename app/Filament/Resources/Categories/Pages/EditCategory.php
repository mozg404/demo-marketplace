<?php

namespace App\Filament\Resources\Categories\Pages;

use Filament\Actions\DeleteAction;
use App\Exceptions\Category\CategoryFullPathConflictException;
use App\Filament\Resources\Categories\CategoryResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            return parent::handleRecordUpdate($record, $data);
        } catch (CategoryFullPathConflictException $e) {
            Notification::make()
                ->title('Конфликт путей')
                ->body($e->getMessage())
                ->danger()
                ->send();

            $this->halt();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
