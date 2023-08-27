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
        Schema::create('crop_projects', function (Blueprint $table) {
            $table->id();
            $table->string('crop_name');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('expected_end_date');
            $table->string('status');
            $table->double('yield');
            $table->double('expected_yield');

            $table->foreignId('storage_id')->nullable()->references('id')->on('storages');
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
        Schema::dropIfExists('crop_projects');
    }
};
