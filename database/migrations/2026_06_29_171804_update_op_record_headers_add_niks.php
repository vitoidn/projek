<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('op_record_headers', function (Blueprint $table) {
            $table->text('niks')->nullable()->after('prepare_signature');
            $table->text('process_2')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('op_record_headers', function (Blueprint $table) {
            $table->dropColumn('niks');
        });
    }
};
