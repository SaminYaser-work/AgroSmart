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
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->references('id')->on('workers');
            $table->foreignId('farm_id')->references('id')->on('farms');
            $table->string('month');
            $table->string('year');
            $table->double('base');
            $table->double('overtime');
            $table->double('bonus');
            $table->double('penalty');
            $table->double('total');
            $table->boolean('paid')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
