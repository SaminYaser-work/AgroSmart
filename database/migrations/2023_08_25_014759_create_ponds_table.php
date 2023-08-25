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
        Schema::create('ponds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('pond_type');
            $table->string('water_type');
            $table->string('fish')->nullable();
            $table->double('initial_biomass')->nullable();
            $table->integer('initial_fish_count')->nullable();
            $table->double('size');

            $table->foreignId('farm_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ponds');
    }
};
