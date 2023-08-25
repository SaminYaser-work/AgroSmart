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
        Schema::create('pond_metrics', function (Blueprint $table) {
            $table->id();
            $table->double('water_temperature');
            $table->double('ph');
            $table->double('turbidity');

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
        Schema::dropIfExists('pond_metrics');
    }
};
