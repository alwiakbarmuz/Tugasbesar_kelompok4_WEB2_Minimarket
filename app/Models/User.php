<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;  

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;  

    protected $fillable = [
        'name',
        'email',
        'password',
        'branch_id',
        'must_change_password'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'must_change_password' => 'boolean',
    ];

    // Relationship dengan Branch
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'cashier_id');
    }

    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    // Helper methods untuk pengecekan role
    public function isOwner()
    {
        return $this->hasRole('owner');
    }

    public function isManager()
    {
        return $this->hasRole('manager');
    }

    public function isSupervisor()
    {
        return $this->hasRole('supervisor');
    }

    public function isCashier()
    {
        return $this->hasRole('cashier');
    }

    public function isWarehouse()
    {
        return $this->hasRole('warehouse');
    }

    // Scope untuk filter berdasarkan cabang
    public function scopeForBranch($query, $branchId)
    {
        if ($branchId) {
            return $query->where('branch_id', $branchId);
        }
        return $query;
    }
}