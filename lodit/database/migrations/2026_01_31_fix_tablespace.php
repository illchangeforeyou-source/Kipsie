<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip this migration - the login table already exists
        // This was a one-time fix migration that's no longer needed
        // since the database is already properly set up
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Do nothing on rollback
    }
};
