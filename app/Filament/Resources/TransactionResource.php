<?php

namespace App\Filament\Resources;

use App\Enums\TransactionTypeEnum;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\Widgets\TransactionOverview;
use App\Models\Book;
use App\Models\Transaction;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        Card::make()
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('book_id')
                                            ->label('Book')
                                            ->options(Book::select('id', 'name')->pluck('name', 'id'))
                                            ->searchable()
                                            ->required(),
                                        TextInput::make('amount')
                                            ->label('Amount')
                                            ->numeric()
                                            ->mask(fn (Mask $mask) => $mask->money('', ',', 0))
                                            ->required(),
                                    ]),
                                Textarea::make('note')
                                    ->label('Note'),
                            ])
                            ->columnSpan(2),
                        Card::make()
                            ->schema([
                                Select::make('type')
                                    ->label('Type')
                                    ->options(TransactionTypeEnum::getValuesAndLabelsOptions())
                                    ->required(),
                                DateTimePicker::make('transaction_at')
                                    ->label('Transaction at')
                                    ->default(now()),
                                Placeholder::make('created_at')
                                    ->content(fn ($record) => $record?->created_at?->format('Y-m-d H:i') ?: '-'),
                                Placeholder::make('updated_at')
                                    ->content(fn ($record) => $record?->updated_at?->format('Y-m-d H:i') ?: '-'),
                            ])
                            ->columnSpan(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_at')
                    ->label('Transaction at')
                    ->dateTime('Y-m-d, H:i')
                    ->sortable(),
                TextColumn::make('book.name')
                    ->label('Book')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->searchable()
                    ->sortable()
                    ->money('IDR', true),
                BadgeColumn::make('type')
                    ->enum(
                        TransactionTypeEnum::getValuesAndLabelsOptions()
                    )
                    ->colors([
                        'success' => TransactionTypeEnum::INCOME->value,
                        'danger' => TransactionTypeEnum::OUTCOME->value,
                    ])
                    ->sortable(),
                TextColumn::make('note')
                    ->label('Note')
                    ->searchable()
                    ->limit(50),
            ])
            ->defaultSort('transaction_at', 'desc')
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

    public static function getWidgets(): array
    {
        return [
            TransactionOverview::class
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
