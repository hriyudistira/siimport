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
        Schema::create('duty_taxes', function (Blueprint $table) {
            $table->id();
            $table->string('kode_po')->constrained('documents')->cascadeOnDelete();
            $table->string('supplier');
            $table->string('bm');
            $table->string('pph');
            $table->string('ppn');
            $table->string('total');
            $table->string('no_bill');
            $table->date('bill_date');
            $table->string('doc_bill');
            $table->string('no_ntpn');
            $table->date('ntpn_date');
            $table->string('doc_ntpn');
            $table->string('cocc');
            $table->string('no_pay');
            $table->string('note');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('duty_taxes');
    }
};
