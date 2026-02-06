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
        Schema::table('transactions', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('transactions', 'type')) {
                $table->string('type')->default('sale'); // 'sale', 'stock_purchase', 'stock_addition'
            }
            if (!Schema::hasColumn('transactions', 'medicine_id')) {
                $table->integer('medicine_id')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'quantity')) {
                $table->integer('quantity')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'balance')) {
                $table->decimal('balance', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('transactions', 'reference_id')) {
                $table->string('reference_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }
};
