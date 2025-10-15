<?php

namespace App\Filament\Resources\Users;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Exception;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Users\RelationManagers\ProductsRelationManager;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\User;
use App\Services\Balance\BalanceAdjuster;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $modelLabel = 'Пользователь';
    protected static ?string $pluralModelLabel = 'Пользователи';
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedUsers;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Toggle::make('is_admin')
                    ->label('Администратор')
                    ->onColor('danger')
                    ->offColor('gray'),
                TextInput::make('balance')
                    ->numeric()
                    ->prefix('₽')
                    ->default(0),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255)
                    ->hiddenOn('edit')
                    ->visibleOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('balance')
                    ->label('Баланс')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                Filter::make('verified')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
                Filter::make('unverified')
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                Action::make('adjust_balance')
                    ->icon('heroicon-o-banknotes')
                    ->label('Изменить баланс')
                    ->schema([
                        TextInput::make('amount')
                            ->numeric()
                            ->required()
                            ->label('Сумма')
                            ->default(fn (User $user) => $user->balance)
                            ->rules([
                                function (User $user) {
                                    return function (string $attribute, $value, callable $fail) use ($user) {
                                        if ($value <= 0) {
                                            $fail('Сумма должна быть положительной');
                                        }
                                    };
                                }
                            ])
                    ])
                    ->action(function (User $user, array $data, BalanceAdjuster $corrector) {
                        try {
                            $transaction = $corrector->setBalance($user, $data['amount']);

                            Notification::make()
                                ->title("Баланс изменен на {$transaction->amount}")
                                ->success()
                                ->send();

                        } catch (Exception $e) {
                            Notification::make()
                                ->title('Ошибка!')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
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
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
