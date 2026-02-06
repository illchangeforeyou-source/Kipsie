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
        // Create orders table if it doesn't exist
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('customer_name')->nullable();
                $table->json('items')->nullable();
                $table->decimal('total', 10, 2)->default(0);
                $table->timestamps();
            });
        }

        // Create transactions table if it doesn't exist
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->string('type')->nullable();
                $table->text('description')->nullable();
                $table->decimal('amount', 10, 2)->default(0);
                $table->decimal('balance', 10, 2)->default(0);
                $table->string('reference_id')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('transactions');
    }
};
