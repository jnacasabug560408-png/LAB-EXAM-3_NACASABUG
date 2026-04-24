<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'isbn', 'title', 'author', 'category', 'description',
        'total_copies', 'available_copies', 'publisher', 'publication_year'
    ];

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function isAvailable()
    {
        return $this->available_copies > 0;
    }

    public function decreaseAvailableCopies($quantity = 1)
    {
        $this->available_copies -= $quantity;
        $this->save();
    }

    public function increaseAvailableCopies($quantity = 1)
    {
        $this->available_copies += $quantity;
        $this->save();
    }
}