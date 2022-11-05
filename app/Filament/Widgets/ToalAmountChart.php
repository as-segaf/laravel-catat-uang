<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;
use Filament\Widgets\LineChartWidget;
use Flowframe\Trend\Trend;

class ToalAmountChart extends LineChartWidget
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

    private function countTotalAmount()
    {
        $data = [];
        $totalIncomeMinusOutcomePerMonth = [];
        $incomeData = $this->getTrendData(TransactionTypeEnum::INCOME);
        $outcomeData = $this->getTrendData(TransactionTypeEnum::OUTCOME);

        foreach ($incomeData as $key => $value) {
            $totalIncomeMinusOutcomePerMonth[] = $value - $outcomeData[$key];
        }

        foreach ($totalIncomeMinusOutcomePerMonth as $key => $amount) {
            $nextMonth = now()->format('n') + 1;
            if ($nextMonth == $key+1) {
                $data[$key] = 0;
                continue;
            }
            
            $data[$key] = $key == 0 ? $amount : $amount + $totalIncomeMinusOutcomePerMonth[$key-1];
        }

        return $data;
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
