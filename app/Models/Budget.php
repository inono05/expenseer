<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use HasFactory, SoftDeletes;
    //
    protected $table = 'budgets';
    protected $fillable = [
        'user_id', 'category_id', 'amount', 'month', 'year', 'type'
    ];
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'month' => 'integer',
            'year' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->amount - $this->amount_spent;
    }

    public function getAmountSpentAttribute(): float
    {
        return (float) (
        $this->category
            ? $this->category->monthlySpent($this->month, $this->year)
            : Expense::forUser($this->user_id)
            ->inMonth($this->month, $this->year)
            ->sum('amount')
        );
    }

    public function getPercentageUsedAttribute(): float{
        if ($this->amount === 0){
            return 0;
        }
        return round(($this->amount_spent/$this->amount) * 100);
    }

    public function getBudgetStatusAttribute(): string
    {
        return match (true) {
            $this->percentage_used < 50 => 'Good',
            $this->percentage_used < 90 => 'Warning',
            default => 'Over',
        };
    }
}
