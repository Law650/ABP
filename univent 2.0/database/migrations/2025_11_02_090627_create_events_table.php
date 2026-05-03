<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            // 1. Primary Key diletakkan paling atas
            $table->id();

            // 2. Relasi ke tabel Event Organizers dan Categories
            $table->foreignId('event_organizer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();

            // 3. Detail Event
            $table->string('event_title');
            $table->text('event_description'); // Nanti kita isi dengan bantuan AI Gemini
            $table->date('start_date');
            $table->time('start_time');
            $table->date('end_date'); // Tanggal ini akan kita pakai untuk filter otomatis di UI
            $table->time('end_time');
            $table->string('event_location'); // Berupa teks biasa, sudah sangat tepat!
            $table->string('registration_link')->nullable();
            
            // 4. Nomor HP Panitia (Nanti kita ubah formatnya untuk tombol WhatsApp)
            $table->string('contact_person'); 
            
            // 5. Poster menggunakan longText karena akan menyimpan format Base64
            $table->longText('event_poster')->nullable();

            // 6. Status validasi event
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};