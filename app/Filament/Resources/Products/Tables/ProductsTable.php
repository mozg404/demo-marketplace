<?php

namespace App\Filament\Resources\Products\Tables;

use App\Enum\ProductStatus;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                TextColumn::make('user.name')
                    ->label('Продавец')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Название')
                    ->searchable(),
                TextColumn::make('current_price')
                    ->label('Цена')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('base_price')
                    ->label('Ориг. цена')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Статус')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Дата обновления')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('category.name')
                    ->label('Категория')
                    ->searchable(),
                TextColumn::make('positive_feedbacks_count')
                    ->label('+')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('negative_feedbacks_count')
                    ->label('-')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('rating')
                    ->label('Рейтинг')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make('Изменить'),
                Action::make('Опубликовать')->action(function (Product $record) {
                    $record->status = ProductStatus::ACTIVE;
                    $record->save();
                }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
