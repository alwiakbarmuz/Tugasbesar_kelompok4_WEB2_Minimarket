<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'branch_id',
        'cashier_id',
        'transaction_date',
        'subtotal',
        'tax',
        'discount',
        'total',
        'cash',
        'change',
        'status',
        'notes',
        'deleted_by',
        'delete_reason'
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationship dengan user yang menghapus
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // Scope untuk transaksi yang tidak dihapus (default)
    public function scopeNotDeleted($query)
    {
        return $query->whereNull('deleted_at');
    }

    // Scope untuk transaksi yang dihapus
    public function scopeOnlyDeleted($query)
    {
        return $query->onlyTrashed();
    }

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    // Generate invoice number
    public static function generateInvoiceNumber()
    {
        $date = now();
        $prefix = 'INV/' . $date->format('Ymd') . '/';

        $lastTransaction = DB::table('transactions')
            ->where('invoice_number', 'LIKE', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(invoice_number, -6) AS UNSIGNED) DESC')
            ->first();

        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->invoice_number, -6);
            $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '000001';
        }

        return $prefix . $newNumber;
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            $transaction->invoice_number = self::generateInvoiceNumber();
        });
    }
}