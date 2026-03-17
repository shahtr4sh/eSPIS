<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_pi_distribution_weights', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('kpi_pi_id');
            $table->unsignedBigInteger('distribution_unit_id');

            $table->decimal('weight_value', 15, 2)->nullable();
            $table->timestamps();

            $table->foreign('kpi_pi_id', 'fk_kpi_weight_kpi')
                ->references('id')
                ->on('kpi_pis')
                ->cascadeOnDelete();

            $table->foreign('distribution_unit_id', 'fk_kpi_weight_unit')
                ->references('id')
                ->on('distribution_units')
                ->cascadeOnDelete();

            $table->unique(
                ['kpi_pi_id', 'distribution_unit_id'],
                'uq_kpi_unit_weight'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_pi_distribution_weights');
    }
};
