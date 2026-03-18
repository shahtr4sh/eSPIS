<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kpi_pis', function (Blueprint $table) {
            $table->string('target_input_mode')->default('annual_overall')->after('measurement');
        });
    }

    public function down(): void
    {
        Schema::table('kpi_pis', function (Blueprint $table) {
            $table->dropColumn('target_input_mode');
        });
    }
};
