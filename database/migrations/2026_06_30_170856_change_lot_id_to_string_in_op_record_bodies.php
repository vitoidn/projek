<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('op_record_bodies', function (Blueprint $table) {
            $table->dropForeign(['lot_id']);
            $table->string('lot_id', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('op_record_bodies', function (Blueprint $table) {
            $table->unsignedBigInteger('lot_id')->nullable()->change();
            $table->foreign('lot_id')->references('id')->on('m_lot_numbers');
        });
    }
};
