<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('dok', function (Blueprint $table) {
            $table->id();
            $table->String('nama');
            $table->String('foto');
            $table->timestamps();


        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dok');
    }
};
