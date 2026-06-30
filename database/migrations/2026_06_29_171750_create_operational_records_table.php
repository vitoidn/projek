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
        Schema::create('operational_records', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('nik');
            $table->string('operator_name');
            $table->foreignId('shift_id')->constrained('master_shifts');
            $table->foreignId('line_id')->constrained('master_lines');
            $table->string('process')->default('Manual Bending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_records');
    }
};
