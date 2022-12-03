<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;
use Filament\Widgets\LineChartWidget;
use Flowframe\Trend\Trend;

class TotalAmountChart extends LineChartWidget
{
    protected static ?string $heading = 'Total Amount Chart';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Total Amount',
                    'data' => $this->countTotalAmount(),
                    'borderColor' => 'orange',
                    'backgroundColor' => 'orange',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    public function countTotalAmount()
    {
        $totalAmountPerMonth = [];
        $incomeData = $this->getTrendData(TransactionTypeEnum::INCOME);
        $outcomeData = $this->getTrendData(TransactionTypeEnum::OUTCOME);

        foreach ($incomeData as $key => $value) {
            $totalAmountPerMonth[] = $value - $outcomeData[$key];
        }

        foreach ($totalAmountPerMonth as $key => $value) {
            if ($key == 0) {
                continue;
            }

            $nextMonth = now()->format('n') + 1;
            if ($key >= $nextMonth) {
                $totalAmountPerMonth[$key] = 0;
                continue;
            }

            $totalAmountPerMonth[$key] = $value + $totalAmountPerMonth[$key - 1];
        }

        return $totalAmountPerMonth;
    }

    private function getTrendData(TransactionTypeEnum $type)
    {
        return Trend::query(Transaction::where('type', $type->value))
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
