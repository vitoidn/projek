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
        Schema::create('horenzos', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('shift_id')->constrained('master_shifts');
            $table->foreignId('line_id')->constrained('master_lines');
            $table->integer('total_production_qty')->default(0);
            $table->integer('total_defect_qty')->default(0);
            $table->integer('operator_count')->default(0);
            $table->decimal('achievement_percent', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horenzos');
    }
};
