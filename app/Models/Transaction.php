<?php

namespace App\Models;

use App\Enums\TransactionTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Transaction extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $guarded = [];

    /**
     * Scope a query to only include income transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIncome($query)
    {
        return $query->where('type', TransactionTypeEnum::INCOME->value);
    }

    /**
     * Scope a query to only include outcome transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOutcome($query)
    {
        return $query->where('type', TransactionTypeEnum::OUTCOME->value);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
