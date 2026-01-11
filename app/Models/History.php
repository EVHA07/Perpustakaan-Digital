<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class History extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'last_page',
        'total_time_spent',
        'last_read_at',
        'last_ping_at',
        'is_active',
        'last_activity_at',
    ];

    protected $casts = [
        'last_read_at' => 'datetime',
        'last_ping_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function bookStats(): HasOne
    {
        return $this->hasOne(UserBookStats::class, 'user_id', 'user_id')
            ->where('book_id', $this->book_id);
    }
}
