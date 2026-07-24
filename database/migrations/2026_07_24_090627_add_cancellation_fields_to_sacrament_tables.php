<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $tables = ['baptisms', 'communions', 'confirmations', 'weddings', 'funerals'];

        foreach ($tables as $tableName) {
            // Add cancellation_reason if it doesn't exist
            if (!Schema::hasColumn($tableName, 'cancellation_reason')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->text('cancellation_reason')->nullable();
                });
            }

            // Add is_locked if it doesn't exist
            if (!Schema::hasColumn($tableName, 'is_locked')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->boolean('is_locked')->default(false);
                });
            }
        }
    }

    public function down()
    {
        $tables = ['baptisms', 'communions', 'confirmations', 'weddings', 'funerals'];

        foreach ($tables as $tableName) {
            if (Schema::hasColumn($tableName, 'cancellation_reason')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('cancellation_reason');
                });
            }
            if (Schema::hasColumn($tableName, 'is_locked')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('is_locked');
                });
            }
        }
    }
};