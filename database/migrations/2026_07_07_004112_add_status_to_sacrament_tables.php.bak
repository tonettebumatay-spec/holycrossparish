<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = ['baptisms', 'communions', 'confirmations', 'weddings', 'funerals'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                // Add status column after 'remarks' with default 'pending'
                $table->string('status')->default('pending')->after('remarks');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['baptisms', 'communions', 'confirmations', 'weddings', 'funerals'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};