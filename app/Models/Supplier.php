<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'payment_terms',
        'lead_time_days',
        'reliability_score',
        'notes',
    ];

    protected $casts = [
        'reliability_score' => 'float',
        'lead_time_days' => 'integer',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function lowStockProducts(): HasMany
    {
        return $this->hasMany(Product::class)
            ->whereColumn('quantity', '<=', 'minimum_stock');
    }
}
