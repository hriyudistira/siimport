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
            // Ubah panjang kolom kode_po menjadi varchar(9)
            $table->string('kode_po', 9)->change();

            // Tambahkan kolom doc_pl dan doc_insurance
            $table->string('doc_pl', 255)->nullable()->after('doc_permit');
            $table->string('doc_insurance', 255)->nullable()->after('doc_pl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Kembalikan panjang kolom kode_po ke default (jika awalnya varchar(255))
            $table->string('kode_po', 255)->change();

            // Hapus kolom yang ditambahkan
            $table->dropColumn(['doc_pl', 'doc_insurance']);
        });
    }
};
