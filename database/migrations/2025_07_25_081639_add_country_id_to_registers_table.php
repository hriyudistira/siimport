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
        // Schema::table('registers', function (Blueprint $table) {
            //  Tambah kolom country_id, nullable jika data lama belum diisi
            // $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('registers', function (Blueprint $table) {
            //
            // $table->dropForeign(['country_id']);
            // $table->dropColumn('country_id');
        // });
    }
};
