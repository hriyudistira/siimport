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
            $table->foreignId('kode_po')->constrained('purchases')->cascadeOnDelete();
            $table->string('supplier');
            $table->string('payment_type');
            $table->date('send_po_date');
            $table->date('send_doc_date');
            $table->string('incoterms');
            $table->string('ship_by');
            $table->string('ppjk');
            $table->date('etd_date');
            $table->date('eta_date');
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
