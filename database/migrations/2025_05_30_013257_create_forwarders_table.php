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
        Schema::create('forwarders', function (Blueprint $table) {
            $table->id();
            $table->string('kode_po')->constrained('documents')->cascadeOnDelete();
            $table->string('supplier');
            $table->string('payment_type');
            $table->date('send_po_date')->nullable();
            $table->string('doc_evidence');
            $table->string('incoterms');
            $table->string('ship_by');
            $table->string('ppjk');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forwarders');
    }
};
