<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom untuk data validasi EO (Boleh kosong/nullable untuk user biasa)
            $table->string('eo_org_type')->nullable()->comment('Internal Kampus / Eksternal Publik');
            $table->string('eo_org_name')->nullable()->comment('Nama Organisasi/Instansi');
            $table->string('eo_pic_name')->nullable()->comment('Nama Penanggung Jawab (PIC)');
            $table->string('eo_phone')->nullable()->comment('Nomor WhatsApp Aktif');
            $table->string('eo_instagram')->nullable()->comment('Akun Instagram/Website');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus kolom jika migrasi di-rollback
            $table->dropColumn([
                'eo_org_type',
                'eo_org_name',
                'eo_pic_name',
                'eo_phone',
                'eo_instagram'
            ]);
        });
    }
};