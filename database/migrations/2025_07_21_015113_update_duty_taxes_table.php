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
        Schema::table('duty_taxes', function (Blueprint $table) {
            // Ubah tipe kolom
            $table->string('kode_po', 9)->change();
            $table->double('bm')->nullable()->change();
            $table->double('pph')->nullable()->change();
            $table->double('ppn')->nullable()->change();
            $table->double('total')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('duty_taxes', function (Blueprint $table) {
            // Kembalikan ke string jika sebelumnya varchar
            $table->string('kode_po', 255)->change();
            $table->string('bm', 255)->nullable()->change();
            $table->string('pph', 255)->nullable()->change();
            $table->string('ppn', 255)->nullable()->change();
            $table->string('total', 255)->nullable()->change();
        });
    }
};
