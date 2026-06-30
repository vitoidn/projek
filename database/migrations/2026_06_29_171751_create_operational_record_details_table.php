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
        Schema::create('operational_record_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('or_id')->constrained('operational_records')->onDelete('cascade');
            $table->foreignId('part_id')->constrained('master_parts');
            $table->string('lot_number');
            $table->integer('qty_per_lot');
            $table->integer('cycle_time_sec');
            $table->dateTime('start_time')->nullable();
            $table->dateTime('estimated_end')->nullable();
            $table->dateTime('actual_end')->nullable();
            $table->integer('standard_time_sec')->nullable();
            $table->integer('actual_time_sec')->nullable();
            $table->integer('total_downtime_sec')->default(0);
            $table->integer('working_time_sec')->nullable();
            $table->string('production_status')->nullable();
            $table->integer('qty_production')->default(0);
            $table->integer('qty_ok')->default(0);
            $table->integer('qty_ng')->default(0);
            $table->string('status')->default('Ready');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_record_details');
    }
};
