<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Borrowing extends Model
{
    protected $fillable = [
        'member_id', 'book_id', 'borrow_date', 'due_date',
        'return_date', 'status', 'quantity'
    ];

    protected $dates = [
        'borrow_date', 'due_date', 'return_date', 'created_at', 'updated_at'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function penalty()
    {
        return $this->hasOne(Penalty::class);
    }

    public function isOverdue()
    {
        return $this->status !== 'returned' && 
               $this->due_date < Carbon::now()->toDateString();
    }

    public function getDaysOverdue()
    {
        if ($this->status === 'returned') {
            return $this->return_date > $this->due_date 
                ? $this->return_date->diffInDays($this->due_date)
                : 0;
        }
        return $this->isOverdue() 
            ? Carbon::now()->diffInDays($this->due_date)
            : 0;
    }

    public function markAsReturned($returnDate = null)
    {
        $this->return_date = $returnDate ?? Carbon::now()->toDateString();
        $this->status = 'returned';
        $this->save();

        // Increase available copies
        $this->book->increaseAvailableCopies($this->quantity);
    }

    public function updateStatus()
    {
        if ($this->status !== 'returned' && $this->isOverdue()) {
            $this->status = 'overdue';
            $this->save();
        }
    }
}