<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'member_id', 'name', 'email', 'phone', 'address',
        'membership_date', 'status'
    ];

    protected $dates = [
        'membership_date', 'created_at', 'updated_at'
    ];

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function penalties()
    {
        return $this->hasMany(Penalty::class);
    }

    public function activeBorrowings()
    {
        return $this->borrowings()->where('status', 'borrowed')->orWhere('status', 'overdue');
    }

    public function totalPenalties()
    {
        return $this->penalties()
            ->where('payment_status', 'unpaid')
            ->sum('penalty_amount');
    }

    public function canBorrow()
    {
        return $this->status === 'active' && $this->totalPenalties() == 0;
    }
}