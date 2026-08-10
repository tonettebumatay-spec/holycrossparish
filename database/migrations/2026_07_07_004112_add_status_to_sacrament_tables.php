<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'baptisms',
            'communions',
            'confirmations',
            'weddings',
            'funerals'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('status')->default('pending');
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'baptisms',
            'communions',
            'confirmations',
            'weddings',
            'funerals'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};