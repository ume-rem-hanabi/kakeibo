<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'icon',
        'color',
        'sort_order',
    ];

    /**
     * カテゴリに紐づく固定費
     */
    public function fixedCosts(): HasMany
    {
        return $this->hasMany(FixedCost::class);
    }
}
