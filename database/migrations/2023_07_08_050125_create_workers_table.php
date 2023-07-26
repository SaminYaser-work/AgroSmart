<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->id();

            $table->string('first_name');
            $table->string('last_name');
            $table->string("phone_number");
            $table->date("start_date");
            $table->date("end_date")->nullable();
            $table->float("salary");
            $table->float("bonus");
            $table->float("over_time_rate");
            $table->float("expected_hours");
            $table->string("designation");

            $table->foreignId('farm_id')->references('id')->on('farms');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
