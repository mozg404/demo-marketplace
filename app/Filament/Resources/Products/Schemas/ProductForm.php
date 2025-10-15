<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enum\ProductStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Продавец')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('name')
                    ->label('Название')
                    ->required(),
                TextInput::make('current_price')
                    ->label('Текущая цена')
                    ->required()
                    ->numeric(),
                TextInput::make('base_price')
                    ->label('Ориг. цена')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->label('Статус')
                    ->options(ProductStatus::class)
                    ->default('draft')
                    ->required(),
                Textarea::make('description')
                    ->label('Описание')
                    ->columnSpanFull(),
                Textarea::make('instruction')
                    ->label('Инструкция')
                    ->columnSpanFull(),
                Select::make('category_id')
                    ->label('Категория')
                    ->relationship('category', 'name'),
                TextInput::make('positive_feedbacks_count')
                    ->label('+')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('negative_feedbacks_count')
                    ->label('-')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('rating')
                    ->label('Рейтинг')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
