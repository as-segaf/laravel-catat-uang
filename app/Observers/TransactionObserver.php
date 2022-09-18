<?php

namespace App\Observers;

use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;

class TransactionObserver
{
    /**
     * Handle the transaction "created" event.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return void
     */
    public function created(Transaction $transaction)
    {
        $currentBookAmount = $transaction->book->amount;

        if ($transaction->type == TransactionTypeEnum::INCOME->value) {
            $newAmount = $currentBookAmount + $transaction->amount;
        }

        if ($transaction->type == TransactionTypeEnum::OUTCOME->value) {
            $newAmount = $currentBookAmount - $transaction->amount;
        }

        $transaction->book->update([
            'amount' => $newAmount
        ]);
    }

    /**
     * Handle the transaction "updated" event.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return void
     */
    public function updated(Transaction $transaction)
    {
        if ($transaction->isDirty('type') || $transaction->isDirty('amount')) {
            $bookAmountAfterRollback = $this->rollbackCalculation($transaction, $transaction->book->amount);
    
            if ($transaction->type == TransactionTypeEnum::INCOME->value) {
                $newAmount = $bookAmountAfterRollback + $transaction->amount;
            }
    
            if ($transaction->type == TransactionTypeEnum::OUTCOME->value) {
                $newAmount = $bookAmountAfterRollback - $transaction->amount;
            }
    
            $transaction->book->update([
                'amount' => $newAmount
            ]);
        }

    }

    /**
     * Handle the transaction "deleted" event.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return void
     */
    public function deleted(Transaction $transaction)
    {
        //
    }

    /**
     * Handle the transaction "restored" event.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return void
     */
    public function restored(Transaction $transaction)
    {
        //
    }

    /**
     * Handle the transaction "force deleted" event.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return void
     */
    public function forceDeleted(Transaction $transaction)
    {
        //
    }

    private function rollbackCalculation(Transaction $transaction, $currentBookAmount)
    {
        if ($transaction->getOriginal('type') == TransactionTypeEnum::INCOME->value) {
            $bookAmountAfterRollback = $currentBookAmount - $transaction->getOriginal('amount');
        }

        if ($transaction->getOriginal('type') == TransactionTypeEnum::OUTCOME->value) {
            $bookAmountAfterRollback = $currentBookAmount + $transaction->getOriginal('amount');
        }

        return $bookAmountAfterRollback;
    }
}
