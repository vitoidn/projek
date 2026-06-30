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
        Schema::create('master_parts', function (Blueprint $table) {
            $table->id();
            $table->string('part_code')->unique();
            $table->string('lot_number')->nullable();
            $table->integer('qty_per_lot');
            $table->integer('cycle_time_sec');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_parts');
    }
};
