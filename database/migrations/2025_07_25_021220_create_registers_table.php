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
        Schema::create('registers', function (Blueprint $table) {
            $table->id();
            $table->string('kode_po', 9)->constrained('documents')->cascadeOnDelete();
            $table->string('supplier');
            $table->string('payment_type');
            $table->date('send_po_date')->nullable();
            $table->string('doc_evidence');
            $table->string('incoterms', 20)->nullable();
            $table->string('ship_by', 20)->nullable();
            $table->string('ppjk', 9)->nullable();
            $table->string('country', 20)->nullable();
            $table->string('container', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registers');
    }
};
