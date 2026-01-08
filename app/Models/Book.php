<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'kategori',
        'sinopsis',
        'cover_image',
        'file_path',
        'total_pages',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function histories(): HasMany
    {
        return $this->hasMany(History::class);
    }
}
