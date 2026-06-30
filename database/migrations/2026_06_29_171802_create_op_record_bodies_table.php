<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('op_record_bodies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('header_id')->constrained('op_record_headers')->cascadeOnDelete();
            $table->string('part_code', 100);
            $table->foreignId('lot_id')->nullable()->constrained('m_lot_numbers');
            $table->foreignId('code_id')->constrained('m_activity_codes');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_min')->default(0);
            $table->integer('qty')->default(0);
            $table->integer('ng')->default(0);
            $table->integer('hold')->default(0);
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('op_record_bodies');
    }
};
