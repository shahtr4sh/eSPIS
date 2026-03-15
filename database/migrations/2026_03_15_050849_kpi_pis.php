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
        Schema::create('kpi_pis', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['KPI', 'PI']);
            $table->string('thrust')->nullable();
            $table->string('prime_objective');
            $table->text('strategy');
            $table->string('dimension');
            $table->string('title');
            $table->string('reference')->nullable();
            $table->text('operational_definition')->nullable();
            $table->foreignId('data_source_id')->nullable()->constrained('data_sources')->nullOnDelete();
            $table->string('distribution_type')->nullable();
            $table->foreignId('responsible_office_id')->nullable()->constrained('responsible_offices')->nullOnDelete();
            $table->decimal('baseline_value', 15, 2)->nullable();
            $table->decimal('annual_target', 15, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->string('status')->default('Draft');
            $table->string('attachment_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_pis');
    }
};
