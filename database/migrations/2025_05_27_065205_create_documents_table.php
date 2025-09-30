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
            $table->string('kode_po');
            $table->integer('line_po');
            $table->string('supplier');
            $table->date('inv_date')->nullable();
            $table->date('biel_date')->nullable();
            $table->date('piel_date')->nullable();
            $table->date('cod_date')->nullable();
            $table->date('inv_actdate')->nullable();
            $table->date('biel_actdate')->nullable();
            $table->date('piel_actdate')->nullable();
            $table->date('cod_actdate')->nullable();
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
