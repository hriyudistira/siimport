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
        Schema::create('clearances', function (Blueprint $table) {
            $table->id();
            $table->string('kode_po')->constrained('documents')->cascadeOnDelete();
            $table->string('supplier');
            $table->string('aju_pib');
            $table->string('doc_pib');
            $table->string('nopen_pib');
            $table->date('spb_date');
            $table->string('doc_spb');
            $table->string('cek_bc');
            $table->string('awb_master');
            $table->string('awb_house');
            $table->date('awb_date');
            $table->string('doc_awb');
            $table->string('no_invoice');
            $table->date('invdoc_date');
            $table->string('doc_invdoc');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clearances');
    }
};
