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
        Schema::create('pond_weekly_reports', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->double('production');
            $table->double('yield');
            $table->double('survival_rate');
            $table->double('average_weight');
            $table->double('average_growth');
            $table->double('dissolved_oxygen');
            $table->double('water_level');
            $table->double('water_temperature');
            $table->double('ph');
            $table->double('turbidity');
            $table->double('ammonia');
            $table->double('nitrate');

            $table->foreignId('pond_id')->references('id')->on('ponds');
            $table->foreignId('farm_id')->references('id')->on('farms');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pond_weekly_reports');
    }
};
