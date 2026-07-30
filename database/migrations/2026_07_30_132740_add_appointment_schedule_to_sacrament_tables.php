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
            'funerals',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->date('appointment_date')->nullable()->after('status');
                $table->time('appointment_time')->nullable()->after('appointment_date');
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
            'funerals',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn([
                    'appointment_date',
                    'appointment_time',
                ]);
            });
        }
    }
};