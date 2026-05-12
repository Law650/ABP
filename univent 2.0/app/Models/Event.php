<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    // 1. Fillable sudah disesuaikan dengan struktur database Tahap 1
    protected $fillable = [
        'event_organizer_id', // Menggantikan user_id & organizer_name
        'organizer_type',
        'organizer_name',
        'category_id',        // Menggantikan event_category
        'event_title',
        'event_description',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'event_location',
        'registration_link',
        'contact_person',
        'event_poster',
        'status',
    ];

    // ----------------------------------------------------
    // FITUR BARU: Relasi ke Event Organizer & Kategori
    // ----------------------------------------------------
    
    /**
     * @return BelongsTo<EventOrganizer,$this>
     */
    public function eventOrganizer(): BelongsTo
    {
        return $this->belongsTo(EventOrganizer::class);
    }

    /**
     * @return BelongsTo<Category,$this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // ----------------------------------------------------
    // FITUR LAMA: Tetap dipertahankan
    // ----------------------------------------------------

    /**
     * @return HasMany<EventRegistration,$this>
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * @return HasMany<\App\Models\UserEventClick,$this>
     */
    public function clicks(): HasMany
    {
        // Menghubungkan Event dengan tabel user_event_clicks
        return $this->hasMany(\App\Models\UserEventClick::class, 'event_id');
    }

    public function getOrganizerRegistrationIdAttribute(): ?int
    {
        $registration = $this->registrations()->first();

        // Fix: Pakai nullsafe operator
        return $registration?->id;
    }
}