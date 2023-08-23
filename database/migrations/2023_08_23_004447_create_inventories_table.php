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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->boolean('is_operational');
            $table->string('reason_for_failure')->nullable();
            $table->double('buying_price');
            $table->double('yearly_depreciation');

            $table->foreignId('farm_id')->references('id')->on('farms');
            $table->foreignId('supplier_id')->references('id')->on('suppliers');
            $table->foreignId('purchase_order_id')->references('id')->on('purchase_orders');

            $table->foreignId('crop_project_id')->nullable()->references('id')->on('crop_projects');
            $table->foreignId('storage_id')->nullable()->references('id')->on('storages');
            $table->foreignId('pond_id')->nullable()->references('id')->on('ponds');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
