<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionTypeEnum;
use App\Models\Book;
use App\Models\Transaction;
use Filament\Widgets\LineChartWidget;
use Flowframe\Trend\Trend;

class InOutTransactionChart extends LineChartWidget
{
    protected static ?string $heading = 'In/Out Transaction Chart';

    public ?string $filter = '0';

    protected function getFilters(): ?array
    {
        return array_merge([0 => 'All'], Book::pluck('name', 'id')->toArray());
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Income Amount',
                    'data' => $this->getTrendData(TransactionTypeEnum::INCOME),
                    'borderColor' => 'rgb(75, 192, 192)',
                    'backgroundColor' => 'rgb(75, 192, 192)'
                ],
                [
                    'label' => 'Outcome Amount',
                    'data' => $this->getTrendData(TransactionTypeEnum::OUTCOME),
                    'borderColor' => 'red',
                    'backgroundColor' => 'red'
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    private function modelQuery(TransactionTypeEnum $type)
    {
        $activeFilter = $this->filter;

        return Transaction::query()
            ->where('type', $type->value)
            ->when($activeFilter, function ($query, $activeFilter) {
                $query->where('book_id', $activeFilter);
            });
    }

    private function getTrendData(TransactionTypeEnum $type)
    {
        return Trend::query($this->modelQuery($type))
            ->dateColumn('transaction_at')
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('amount')
            ->map(fn ($data) => $data->aggregate);
    }
}
