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
        Schema::create('kpi_pi_distribution_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_pi_id')->constrained('kpi_pis')->cascadeOnDelete();
            $table->foreignId('distribution_unit_id')->constrained('distribution_units')->cascadeOnDelete();
            $table->enum('quarter', ['Q1', 'Q2', 'Q3', 'Q4']);
            $table->decimal('target_value', 15, 2)->nullable();
            $table->decimal('achievement_value', 15, 2)->nullable();
            $table->timestamps();

            $table->unique(['kpi_pi_id', 'distribution_unit_id', 'quarter'], 'kpi_unit_quarter_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_pi_distribution_details');
    }
};
