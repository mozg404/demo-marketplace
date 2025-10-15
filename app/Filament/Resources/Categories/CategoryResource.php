<?php

namespace App\Filament\Resources\Categories;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Models\Category;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack'; // Более подходящая иконка
    protected static ?string $modelLabel = 'Категория';
    protected static ?string $pluralModelLabel = 'Категории';
    protected static ?int $navigationSort = 1; // Позиция в меню

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('parent_id')
                    ->label('Родительская категория')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->columnSpanFull(),

                TextInput::make('name')
                    ->label('Название')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('slug')
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('title')
                    ->label('Заголовок')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('full_path')
                    ->label('URI')
                    ->searchable(),
                TextColumn::make('parent.name')
                    ->label('Родитель')
                    ->searchable() // Если нужно
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('_lft') // Сортировка по вложенности
            ->reorderable('_lft') // Включение drag-and-drop
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
