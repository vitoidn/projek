<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horenzo_reports', function (Blueprint $table) {
            $table->id();
            $table->json('filter_params');
            $table->foreignId('generated_by')->constrained('users');
            $table->json('snapshot_data');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horenzo_reports');
    }
};
