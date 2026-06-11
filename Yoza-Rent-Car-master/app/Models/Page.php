<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    // Tambahkan baris ini untuk memberikan izin pengisian data massal
    protected $fillable = [
        'slug',
        'title',
        'content',
    ];
}