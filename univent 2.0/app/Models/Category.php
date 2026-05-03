<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Mengizinkan kolom ini diisi secara massal (mass assignment)
    protected $fillable = ['name', 'status'];

    // Relasi: Satu kategori bisa memiliki banyak event
    public function events()
    {
        return $this->hasMany(Event::class);
    }
}