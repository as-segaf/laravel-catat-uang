<?php

namespace App\Filament\Resources;

use App\Enums\TransactionTypeEnum;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use App\Models\User;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Select::make('user_id')
                            ->label('User')
                            ->options(User::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        TextInput::make('amount')
                            ->required()
                            ->mask(fn (TextInput\Mask $mask) => $mask
                                ->numeric()
                                ->integer()
                                ->thousandsSeparator('.'), // Add a separator for thousands.
                            )
                            ->rules([
                                function (Closure $get) {
                                    return function (string $attribute, $value, Closure $fail) use ($get) {
                                        if ($get('type') == TransactionTypeEnum::INCOME->value) {
                                            return;
                                        }

                                        $user = User::find($get('user_id'));
                                        
                                        if ($user->balance < $value) {
                                            return $fail("Saldo user tidak cukup");
                                        }
                                    };
                                },
                            ]),
                        Select::make('type')
                            ->options(TransactionTypeEnum::generateOptions())
                            ->required(),
                        DateTimePicker::make('transaction_at')
                            ->required()
                            ->default(now()),
                        Textarea::make('note'),
                        KeyValue::make('meta'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name'),
                TextColumn::make('invoice_id'),
                TextColumn::make('amount')
                    ->formatStateUsing(fn ($state) => money(($state ?? 0), 'IDR')),
                TextColumn::make('type'),
                TextColumn::make('transaction_at'),
                TextColumn::make('admin.name')
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }    
}
