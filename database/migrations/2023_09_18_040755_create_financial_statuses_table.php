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
        Schema::create('financial_statuses', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->float('total_revenue');
            $table->json('revenue_breakdown')->nullable();
            $table->float('total_expense');
            $table->json('expense_breakdown')->nullable();
            $table->float('gross_profit')->nullable();
            $table->float('net_profit');
            $table->float('total_assets');
            $table->float('total_liabilities');
            $table->float('account_receivable');
            $table->float('account_payable');
            $table->float('total_equity');




            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_statuses');
    }
};
