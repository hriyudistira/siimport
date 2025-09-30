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
        Schema::create('arrivals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kode_po')->constrained('purchases')->cascadeOnDelete();
            $table->string('supplier');
            $table->date('etd_date');
            $table->date('eta_date');
            $table->date('etacbi_date');
            $table->date('etd_actdate');
            $table->date('eta_actdate');
            $table->date('etacbi_actdate');
            $table->date('rec_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arrivals');
    }
};
