<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['user_id', 'type', 'transaction_date'], 'idx_transactions_user_type_date');
            $table->index(['user_id', 'transaction_date'], 'idx_transactions_user_date');
            $table->index(['user_id', 'is_pending'], 'idx_transactions_user_pending');
        });

        Schema::table('pending_notifications', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'idx_pending_notifications_user_status');
            $table->index(['user_id', 'status', 'created_at'], 'idx_pending_notifications_user_status_created');
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->index(['user_id', 'type'], 'idx_accounts_user_type');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_transactions_user_type_date');
            $table->dropIndex('idx_transactions_user_date');
            $table->dropIndex('idx_transactions_user_pending');
        });

        Schema::table('pending_notifications', function (Blueprint $table) {
            $table->dropIndex('idx_pending_notifications_user_status');
            $table->dropIndex('idx_pending_notifications_user_status_created');
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropIndex('idx_accounts_user_type');
        });
    }
};
