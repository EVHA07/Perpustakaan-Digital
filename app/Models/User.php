<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'total_reading_time',
        'total_books_read',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function histories(): HasMany
    {
        return $this->hasMany(History::class);
    }

    public function bookStats(): HasMany
    {
        return $this->hasMany(UserBookStats::class);
    }

    public function readingSessions(): HasMany
    {
        return $this->hasMany(ReadingSession::class);
    }

    public function getTotalReadingTimeAttribute(): int
    {
        return (int) $this->bookStats()->sum('total_seconds');
    }

    public function getTotalBooksReadAttribute(): int
    {
        return $this->bookStats()->where('total_seconds', '>', 0)->count();
    }
}
