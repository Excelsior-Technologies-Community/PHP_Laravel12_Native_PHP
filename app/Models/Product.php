<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'category',
        'description',
        'price',
        'quantity',
        'minimum_stock',
        'status',
        'supplier_id',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'minimum_stock' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($product) {
            if (empty($product->sku)) {
                $product->sku = 'SKU-' . strtoupper(substr($product->category ?? 'GEN', 0, 3)) . '-' . rand(1000, 9999);
            }
            if (empty($product->barcode)) {
                $product->barcode = '890' . rand(10000000, 99999999);
            }
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->minimum_stock && $this->quantity > 0;
    }

    public function isOutOfStock(): bool
    {
        return $this->quantity == 0;
    }

    public function recordMovement(string $type, int $quantityChange, ?string $reason = null, ?string $refNo = null, ?string $createdBy = null): StockMovement
    {
        $previous = $this->quantity;
        $new = max(0, $previous + $quantityChange);

        $this->update(['quantity' => $new]);

        return $this->stockMovements()->create([
            'type' => $type,
            'quantity_change' => $quantityChange,
            'previous_quantity' => $previous,
            'new_quantity' => $new,
            'reason' => $reason,
            'reference_no' => $refNo ?? 'REF-' . strtoupper(uniqid()),
            'created_by' => $createdBy ?? 'Inventory System',
        ]);
    }
}
