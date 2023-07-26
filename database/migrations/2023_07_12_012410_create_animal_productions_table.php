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
        Schema::create('animal_productions', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->date('date');
            $table->double('quantity');
            $table->string('unit');

            $table->foreignId('animal_id')->constrained('animals');
            $table->foreignId('farm_id')->constrained('farms');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animal_productions');
    }
};
