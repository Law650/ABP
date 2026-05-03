<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menjalankan migration (Membuat struktur tabel beserta algoritmanya).
     */
    public function up(): void
    {
        Schema::create('user_event_clicks', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel events. 'cascadeOnDelete' memastikan jika event dihapus, histori kliknya ikut bersih.
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            
            // Kolom untuk IP Address. Panjang maksimal 45 karakter untuk mendukung format IPv6.
            $table->string('ip_address', 45)->nullable();
            
            // Kolom untuk jenis browser dan perangkat.
            $table->string('user_agent', 255)->nullable();
            
            $table->timestamps();

            // ----------------------------------------------------
            // DOKUMENTASI PENTING (ALGORITMA UNIQUE CLICK)
            // ----------------------------------------------------
            // Baris ini akan mengunci ketiga kolom menjadi satu kesatuan.
            // Database akan otomatis menolak penyimpanan jika ada pengunjung
            // dengan IP dan User Agent yang sama me-refresh halaman berulang kali.
            $table->unique(['event_id', 'ip_address', 'user_agent'], 'unique_user_click');
        });
    }

    /**
     * Membatalkan migration (Menghapus tabel jika diperlukan).
     */
    public function down(): void
    {
        Schema::dropIfExists('user_event_clicks');
    }
};