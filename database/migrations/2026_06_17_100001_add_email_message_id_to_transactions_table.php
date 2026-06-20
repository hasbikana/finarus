<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('email_message_id')->nullable()->unique()->after('description');
            $table->string('source')->nullable()->after('email_message_id')->comment('email, manual, import');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['email_message_id', 'source']);
        });
    }
};
