<?php

namespace App\Filament\Resources\Features;

use Filament\Actions\DeleteAction;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Features\RelationManagers\CategoriesRelationManager;
use App\Filament\Resources\Features\Pages\ListFeatures;
use App\Filament\Resources\Features\Pages\CreateFeature;
use App\Filament\Resources\Features\Pages\EditFeature;
use App\Enum\FeatureType;
use App\Models\Feature;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class FeatureResource extends Resource
{
    protected static ?string $model = Feature::class;
    protected static ?string $modelLabel = 'Характеристика';
    protected static ?string $pluralModelLabel = 'Характеристики';
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedListBullet;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('categories')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->required()
                    ->preload()
                    ->searchable()
                    ->native(false)
                    ->hint('Выберите одну или несколько категорий'),

                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

//                Forms\Components\TextInput::make('key')
//                    ->required()
//                    ->unique(ignoreRecord: true)
//                    ->maxLength(255)
//                    ->regex('/^[a-z0-9_]+$/') // Только латиница и подчёркивания
//                    ->hint('Например: edition_type'),

                Select::make('type')
                    ->options(FeatureType::names())
                    ->required()
                    ->live(),

                KeyValue::make('options')
                    ->nullable()
                    ->visible(fn (Get $get) => $get('type') === 'select')
                    ->keyLabel('Значение (для кода)')
                    ->valueLabel('Отображаемый текст'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                // Обновляем отображение категорий (теперь их может быть несколько)
                TextColumn::make('categories.name')
                    ->label('Категории')
                    ->badge()
                    ->separator(', ')
                    ->searchable(),

                TextColumn::make('name')
                    ->searchable(),

//                Tables\Columns\TextColumn::make('key')
//                    ->searchable(),

                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state->value) {
                        'select' => 'Select',
                        'text' => 'Text',
                        default => ucfirst($state->value)
                    }),

//                Tables\Columns\IconColumn::make('is_required')
//                    ->boolean()
//                    ->label('Обязательное'),
            ])
            ->filters([
                // Обновляем фильтр для работы с Many-to-Many
                SelectFilter::make('categories')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CategoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFeatures::route('/'),
            'create' => CreateFeature::route('/create'),
            'edit' => EditFeature::route('/{record}/edit'),
        ];
    }
}
