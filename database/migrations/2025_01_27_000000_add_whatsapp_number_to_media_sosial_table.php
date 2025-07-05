<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('media_sosial', function (Blueprint $table) {
            $table->string('whatsapp_number')->nullable()->after('link_grup_beasiswa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media_sosial', function (Blueprint $table) {
            $table->dropColumn('whatsapp_number');
        });
    }
}; 