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
        Schema::create('kpi_pi_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_pi_id')->constrained('kpi_pis')->cascadeOnDelete();
            $table->year('year');
            $table->enum('quarter', ['Q1', 'Q2', 'Q3', 'Q4']);
            $table->decimal('quarter_target', 15, 2)->nullable();
            $table->decimal('actual_value', 15, 2)->nullable();
            $table->decimal('achievement_percentage', 8, 2)->nullable();
            $table->date('achievement_date')->nullable();
            $table->text('remarks')->nullable();
            $table->string('evidence_path')->nullable();
            $table->timestamps();

            $table->unique(['kpi_pi_id', 'year', 'quarter']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_pi_achievements');
    }
};
