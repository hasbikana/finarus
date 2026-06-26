<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE pending_notifications MODIFY COLUMN source ENUM('push_notif', 'ocr', 'email') NOT NULL");

        Schema::table('pending_notifications', function (Blueprint $table) {
            $table->string('email_message_id', 255)->unique()->nullable()->after('source');
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete()->after('email_message_id');
        });
    }

    public function down(): void
    {
        Schema::table('pending_notifications', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn(['email_message_id', 'account_id']);
        });

        DB::statement("ALTER TABLE pending_notifications MODIFY COLUMN source ENUM('push_notif', 'ocr') NOT NULL");
    }
};
