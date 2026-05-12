<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            // Tambahkan kolom organizer_type dan organizer_name
            $table->string('organizer_type')->after('event_title')->nullable();
            $table->string('organizer_name')->after('organizer_type')->nullable();
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['organizer_type', 'organizer_name']);
        });
    }
};
