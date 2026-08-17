<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {

            if (!Schema::hasColumn('appointments', 'user_id')) {
                $table->unsignedBigInteger('user_id')
                    ->nullable()
                    ->after('id');
            }

            if (!Schema::hasColumn('appointments', 'email')) {
                $table->string('email')
                    ->nullable()
                    ->after('contact_number');
            }

            if (!Schema::hasColumn('appointments', 'appointment_time')) {
                $table->time('appointment_time')
                    ->nullable()
                    ->after('appointment_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {

            if (Schema::hasColumn('appointments', 'appointment_time')) {
                $table->dropColumn('appointment_time');
            }

            if (Schema::hasColumn('appointments', 'email')) {
                $table->dropColumn('email');
            }

            if (Schema::hasColumn('appointments', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};