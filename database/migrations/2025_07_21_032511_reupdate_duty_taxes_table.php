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
        Schema::table('duty_taxes', function (Blueprint $table) {
            //
            $table->double('cocc')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('duty_taxes', function (Blueprint $table) {
            //
            $table->string('cocc', 255)->nullable()->change();
        });
    }
};
