<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE accounts MODIFY COLUMN type ENUM('cash', 'ewallet', 'bank', 'credit_card') NOT NULL DEFAULT 'bank'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE accounts MODIFY COLUMN type ENUM('ewallet', 'bank', 'credit_card') NOT NULL DEFAULT 'bank'");
    }
};
