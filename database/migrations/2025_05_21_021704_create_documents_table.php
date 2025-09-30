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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kode_po')->constrained('purchases')->cascadeOnDelete();
            $table->string('supplier');
            $table->date('inv_date');
            $table->date('biel_date');
            $table->date('piel_date');
            $table->date('cod_date');
            $table->date('inv_actdate');
            $table->date('biel_actdate');
            $table->date('piel_actdate');
            $table->date('cod_actdate');
            $table->string('doc_permit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
