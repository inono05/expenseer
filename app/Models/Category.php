<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;
    //
    protected $table = 'categories';
    protected $fillable = [
        'user_id',
        'name',
        'color',
        'icon',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function monthlySpent($month, $year): float
    {
        return $this->expenses()->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
