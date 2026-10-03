<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('appointments', 'expired_at')) {
                $table->timestamp('expired_at')->nullable()->after('expires_at');
            }
            if (!Schema::hasColumn('appointments', 'expiry_reminder_sent')) {
                $table->boolean('expiry_reminder_sent')->default(false)->after('expired_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['expires_at', 'expired_at', 'expiry_reminder_sent']);
        });
    }
};