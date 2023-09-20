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
        Schema::create('journals', function (Blueprint $table) {
            $table->id();

            $table->date('date');
            $table->float('total_revenue')->default(0);
            $table->json('revenue_breakdown')->default(json_encode([]));
            $table->float('total_expense')->default(0);
            $table->json('expense_breakdown')->default(json_encode([]));
            $table->float('gross_profit')->default(0);
            $table->float('net_profit')->default(0);
            $table->float('total_assets')->default(0);
            $table->float('total_liabilities')->default(0);
            $table->float('account_receivable')->default(0);
            $table->float('account_payable')->default(0);
            $table->float('total_equity')->default(0);


            $table->index('date');
            $table->foreignId('farm_id');
            $table->unique(['date', 'farm_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
