<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penalty extends Model
{
    protected $fillable = [
        'member_id', 'borrowing_id', 'days_overdue', 'penalty_amount',
        'payment_status', 'penalty_date', 'paid_date', 'remarks'
    ];

    protected $dates = [
        'penalty_date', 'paid_date', 'created_at', 'updated_at'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function borrowing()
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function markAsPaid()
    {
        $this->payment_status = 'paid';
        $this->paid_date = now();
        $this->save();
    }

    public function markAsUnpaid()
    {
        $this->payment_status = 'unpaid';
        $this->paid_date = null;
        $this->save();
    }

    public function waive()
    {
        $this->payment_status = 'waived';
        $this->save();
    }
}