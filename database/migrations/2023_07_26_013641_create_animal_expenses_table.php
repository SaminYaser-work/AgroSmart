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
        Schema::create('animal_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->date('date');
            $table->integer('day');
            $table->integer('month');
            $table->integer('year');
            $table->double('amount');
            $table->foreignId('animal_id')->references('id')->on('animals');
            $table->foreignId('farm_id')->references('id')->on('farms');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animal_expenses');
    }
};
