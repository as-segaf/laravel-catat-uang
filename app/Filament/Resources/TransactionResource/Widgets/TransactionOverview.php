<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Enums\TransactionTypeEnum;
use App\Models\Book;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Arr;

class TransactionOverview extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public $totalAmount = 0;

    public $totalIncomeAmount = 0;

    public $totalOutcomeAmount = 0;

    protected $listeners = [
        'bookSelected',
    ];

    public function mount()
    {
        $bookId = null;

        if ($filter = request('tableFilters', null)) {
            $bookFilter = Arr::get($filter, 'book', null);
            $bookId = Arr::get($bookFilter, 'value', null);
        }

        $this->totalAmount = $this->countTotalAmount($bookId);
        $this->totalIncomeAmount = $this->countTotalAmountByType(TransactionTypeEnum::INCOME, $bookId);
        $this->totalOutcomeAmount = $this->countTotalAmountByType(TransactionTypeEnum::OUTCOME, $bookId);
    }

    protected function getCards(): array
    {
        return [
            Card::make('Total Amount', money($this->totalAmount, 'idr', true)),
            Card::make('Total Income Amount', money($this->totalIncomeAmount, 'idr', true)),
            Card::make('Total Outcome Amount', money($this->totalOutcomeAmount, 'idr', true)),
        ];
    }

    private function countTotalAmount($bookId = null)
    {
        return Book::query()
            ->when($bookId, function ($query, $bookId) {
                $query->where('id', $bookId);
            })
            ->sum('amount');
    }

    private function countTotalAmountByType(TransactionTypeEnum $type, $bookId = null)
    {
        return Transaction::query()
            ->where('type', $type->value)
            ->when($bookId, function ($query, $bookId) {
                $query->where('book_id', $bookId);
            })
            ->sum('amount');
    }

    public function bookSelected($bookId)
    {
        $this->totalAmount = $this->countTotalAmount($bookId);
        $this->totalIncomeAmount = $this->countTotalAmountByType(TransactionTypeEnum::INCOME, $bookId);
        $this->totalOutcomeAmount = $this->countTotalAmountByType(TransactionTypeEnum::OUTCOME, $bookId);
    }
}
