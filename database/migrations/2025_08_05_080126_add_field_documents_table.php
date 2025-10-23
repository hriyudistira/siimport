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
        Schema::table('documents', function (Blueprint $table) {
            //
            $table->date('ins_date')
                ->nullable()
                ->after('biel_date'); // Tambah kolom ins_date setelah kolom biel_date
            $table->date('ins_actdate')
                ->nullable()
                ->after('biel_actdate'); // Tambah kolom ins_actdate setelah kolom biel_actdate
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            //
        });
    }
};
