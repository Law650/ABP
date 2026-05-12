<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventOrganizer extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'description'];

    // Relasi: EO ini milik satu User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi: Satu EO bisa memiliki banyak Event
    public function events()
    {
        return $this->hasMany(Event::class);
    }
}