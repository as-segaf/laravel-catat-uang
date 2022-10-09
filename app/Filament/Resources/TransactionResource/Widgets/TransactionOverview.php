<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Enums\TransactionTypeEnum;
use App\Models\Book;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class TransactionOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getCards(): array
    {
        return [
            Card::make('Total Amount', $this->countTotalAmountFromAllBook()),
            Card::make('Total Income Amount', $this->countTotalOutputAmountFromAllBook(TransactionTypeEnum::INCOME->value)),
            Card::make('Total Outcome Amount', $this->countTotalOutputAmountFromAllBook(TransactionTypeEnum::OUTCOME->value)),
        ];
    }

    private function countTotalAmountFromAllBook()
    {
        return money(Book::sum('amount'), 'idr', true);
    }

    private function countTotalOutputAmountFromAllBook($type)
    {
        return money(Transaction::where('type', $type)->sum('amount'), 'idr', true);
    }
}
