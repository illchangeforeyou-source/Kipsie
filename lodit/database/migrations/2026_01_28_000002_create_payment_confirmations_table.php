<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_confirmations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('cashier_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method')->comment('e.g., cash, card, transfer');
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('login')->onDelete('cascade');
            $table->foreign('cashier_id')->references('id')->on('login')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_confirmations');
    }
};
