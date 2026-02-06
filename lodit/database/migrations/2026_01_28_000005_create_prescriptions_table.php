<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('medicine_id');
            $table->unsignedBigInteger('pharmacist_id')->nullable();
            $table->string('file_path')->comment('Path to uploaded prescription file');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('pharmacist_notes')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('login')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
            $table->foreign('pharmacist_id')->references('id')->on('login')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
