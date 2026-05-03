<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_organizers', function (Blueprint $table) {
            $table->id();
            // Menghubungkan EO dengan User yang membuatnya
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Nama Organisasi / EO
            $table->text('description')->nullable(); // Deskripsi singkat EO
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_organizers');
    }
};