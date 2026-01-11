<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingSession extends Model
{
    protected $table = 'reading_sessions';

    protected $fillable = [
        'user_id',
        'book_id',
        'started_at',
        'last_ping_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'last_ping_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function isActive(): bool
    {
        return $this->last_ping_at && $this->last_ping_at->gt(now()->subMinutes(5));
    }
}
