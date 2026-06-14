<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'barcode',
        'name',
        'category',
        'price',
        'purchase_price',
        'stock',
        'min_stock',
        'unit',
        'branch_id',
        'deleted_by'
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    // Accessors
    public function getStockStatusAttribute()
    {
        if ($this->stock <= 0) return 'out_of_stock';
        if ($this->stock <= $this->min_stock) return 'low_stock';
        if ($this->stock <= $this->min_stock * 2) return 'warning';
        return 'good';
    }

    public function getStockStatusColorAttribute()
    {
        return match ($this->stock_status) {
            'out_of_stock' => 'red',
            'low_stock' => 'orange',
            'warning' => 'yellow',
            default => 'green',
        };
    }

    public function getStockStatusTextAttribute()
    {
        return match ($this->stock_status) {
            'out_of_stock' => 'Habis',
            'low_stock' => 'Stok Menipis',
            'warning' => 'Segera Restok',
            default => 'Stok Aman',
        };
    }

    // Methods
    public function updateStock($quantity, $type, $userId, $note = null)
    {
        $oldStock = $this->stock;

        if ($type === 'in') {
            $this->stock += $quantity;
        } elseif ($type === 'out') {
            $this->stock -= $quantity;
        } elseif ($type === 'adjustment') {
            $this->stock = $quantity;
            $quantity = $this->stock - $oldStock;
        }

        $this->save();

        // Create stock log
        $this->stockLogs()->create([
            'type' => $type,
            'quantity' => abs($quantity),
            'stock_before' => $oldStock,
            'stock_after' => $this->stock,
            'note' => $note,
            'user_id' => $userId,
        ]);

        return $this;
    }
}