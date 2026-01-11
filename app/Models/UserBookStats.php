<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBookStats extends Model
{
    protected $table = 'user_book_stats';

    protected $fillable = [
        'user_id',
        'book_id',
        'total_seconds',
    ];

    protected $casts = [
        'total_seconds' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function incrementTotalSeconds(int $seconds): void
    {
        $this->total_seconds += $seconds;
        $this->save();
    }
}
