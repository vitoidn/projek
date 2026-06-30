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
        Schema::create('production_plannings', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('shift_id')->constrained('master_shifts');
            $table->foreignId('line_id')->constrained('master_lines');
            $table->foreignId('part_id')->constrained('master_parts');
            $table->integer('target_qty');
            $table->integer('jumlah_lot')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_plannings');
    }
};
