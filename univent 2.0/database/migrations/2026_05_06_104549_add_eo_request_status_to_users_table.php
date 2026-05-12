<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom status pengajuan EO dengan nilai bawaan 'none'
            $table->enum('eo_request_status', ['none', 'pending', 'approved', 'rejected'])->default('none');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('eo_request_status');
        });
    }
};