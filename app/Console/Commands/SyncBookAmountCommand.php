<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;

class SyncBookAmountCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'book:sync-amount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync book amount with all transactions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info('Starting sync book amount');

            $books = Book::with('transactions')->get();

            foreach ($books as $book) {
                $this->info('Processin book id '.$book->id);
                $totalIncome = $book->transactions()->income()->sum('amount');
                $totalOutcome = $book->transactions()->outcome()->sum('amount');

                $book->update([
                    'amount' => $totalIncome - $totalOutcome,
                ]);

                $this->info('Book id '.$book->id.' synced');
            }
        } catch (\Exception $e) {
            logger('Sync book amount command error : '.$e->getMessage());
            $this->error('error '.$e->getMessage());
        }
    }
}
