<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('User asking the question');
            $table->unsignedBigInteger('consultant_id')->nullable()->comment('Pharmacist/Doctor responding');
            $table->text('question');
            $table->text('response')->nullable();
            $table->enum('status', ['pending', 'answered', 'closed'])->default('pending');
            $table->unsignedBigInteger('medicine_id')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('login')->onDelete('cascade');
            $table->foreign('consultant_id')->references('id')->on('login')->onDelete('set null');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
