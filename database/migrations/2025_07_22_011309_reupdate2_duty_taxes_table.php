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
            // Ubah tipe kolom ke BIGINT
            $table->bigInteger('bm')->change();
            $table->bigInteger('ppn')->change();
            $table->bigInteger('pph')->change();
            $table->bigInteger('total')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('duty_taxes', function (Blueprint $table) {
            //
            // Kembalikan ke tipe sebelumnya, misalnya DECIMAL atau DOUBLE
            $table->double('bm', 15, 2)->change();
            $table->double('ppn', 15, 2)->change();
            $table->double('pph', 15, 2)->change();
            $table->double('total', 15, 2)->change();
        });
    }
};
