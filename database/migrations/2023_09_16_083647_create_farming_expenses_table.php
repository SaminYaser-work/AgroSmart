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
        Schema::create('farming_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->integer('day');
            $table->integer('month');
            $table->integer('year');
            $table->double('amount');
            $table->foreignId('field_id')->references('id')->on('fields');
            $table->foreignId('farm_id')->references('id')->on('farms');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farming_expenses');
    }
};
