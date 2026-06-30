<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('op_record_header_nik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('header_id')->constrained('op_record_headers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('op_record_header_nik');
    }
};
