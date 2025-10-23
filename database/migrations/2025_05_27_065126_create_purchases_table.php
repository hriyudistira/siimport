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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('kode_po');
            $table->string('kode_supplier');
            $table->string('supplier');
            $table->integer('line_po');
            $table->string('item');
            $table->string('desc_item');
            $table->integer('qty');
            $table->integer('harga');
            $table->date('order_date');
            $table->string('kode_pr');
            $table->integer('line_pr');
            $table->date('createpo');
            $table->string('buyer');
            $table->date('delivpr_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
