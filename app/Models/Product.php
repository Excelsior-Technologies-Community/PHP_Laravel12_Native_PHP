<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [

        'name',
        'category',
        'description',
        'price',
        'quantity',
        'minimum_stock',
        'status',
        'image',

    ];

    protected $casts = [

        'price' => 'decimal:2',

    ];

    public function isLowStock()
    {
        return $this->quantity <= $this->minimum_stock
            && $this->quantity > 0;
    }


    public function isOutOfStock()
    {
        return $this->quantity == 0;
    }
}
