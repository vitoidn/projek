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
        Schema::create('operational_record_defects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('or_detail_id')->constrained('operational_record_details')->onDelete('cascade');
            $table->foreignId('defect_id')->constrained('master_defects');
            $table->integer('qty')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_record_defects');
    }
};
